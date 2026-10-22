#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

SSH_KEY="${SHOPLINK_STAGE_SSH_KEY:-$HOME/.ssh/rma_websupport_ed25519}"
SSH_PORT="${SHOPLINK_STAGE_SSH_PORT:-27498}"
SSH_USER_HOST="${SHOPLINK_STAGE_SSH_HOST:-uid118160@shell.r5.websupport.sk}"
REMOTE_DIR="${SHOPLINK_STAGE_PATH:-/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage}"
REMOTE_BACKUP_DIR="${SHOPLINK_STAGE_BACKUP_DIR:-/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/backups/shoplinkstage}"
FORCE_DEPLOY=0
DRY_RUN=0

for argument in "$@"; do
  case "$argument" in
    --force) FORCE_DEPLOY=1 ;;
    --dry-run) DRY_RUN=1 ;;
    *) echo "Unknown option: $argument" >&2; exit 2 ;;
  esac
done

echo "== Local status =="
git status --short --branch

RSYNC_MODE=()
if [[ "$DRY_RUN" -eq 1 ]]; then
  RSYNC_MODE=(--dry-run)
else
  BACKUP_NAME="shoplinkstage-$(date '+%Y%m%d-%H%M%S').tar.gz"
  echo "== Backup current shoplinkstage =="
  ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER_HOST" \
    "mkdir -p '$REMOTE_BACKUP_DIR' && tar --exclude='./storage/logs' --exclude='./storage/framework/cache' --exclude='./storage/framework/sessions' --exclude='./storage/framework/views' --exclude='./vendor' -czf '$REMOTE_BACKUP_DIR/$BACKUP_NAME' -C '$REMOTE_DIR' ."
fi

if [[ "$FORCE_DEPLOY" -ne 1 ]]; then
  echo "== Check remote worktree =="
  REMOTE_STATUS="$(ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER_HOST" \
    "if [ -d '$REMOTE_DIR/.git' ]; then cd '$REMOTE_DIR' && git status --porcelain; else echo '__NO_REMOTE_GIT__'; fi")"
  if [[ "$REMOTE_STATUS" == "__NO_REMOTE_GIT__" ]]; then
    echo "Remote shoplinkstage has no Git worktree yet; continuing with backup-protected deploy."
  elif [[ -n "$REMOTE_STATUS" ]]; then
    echo "Remote shoplinkstage has uncommitted changes. Deploy aborted." >&2
    echo "$REMOTE_STATUS" >&2
    exit 1
  fi
fi

echo "== Upload to shoplinkstage =="
rsync -az "${RSYNC_MODE[@]}" \
  -e "ssh -i $SSH_KEY -p $SSH_PORT" \
  --exclude '.git/' --exclude '.env' --exclude '.DS_Store' --exclude '._*' \
  --exclude 'node_modules/' --exclude 'vendor/' --exclude 'public/build/' \
  --exclude 'storage/framework/cache/' --exclude 'storage/framework/sessions/' \
  --exclude 'storage/framework/views/' --exclude 'storage/logs/' \
  ./ "$SSH_USER_HOST:$REMOTE_DIR/"

if [[ "$DRY_RUN" -eq 1 ]]; then
  echo "Dry run only. No remote cache cleared."
  exit 0
fi

echo "== Clear shoplinkstage cache =="
ssh -i "$SSH_KEY" -p "$SSH_PORT" "$SSH_USER_HOST" \
  "cd '$REMOTE_DIR' && php artisan route:clear >/dev/null && php artisan config:clear >/dev/null && php artisan view:clear >/dev/null"
echo "Deploy finished."
