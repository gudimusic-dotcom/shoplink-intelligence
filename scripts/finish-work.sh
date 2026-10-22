#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"
MESSAGE="${1:-Work checkpoint $(date '+%Y-%m-%d %H:%M')}"

git status --short
git add -A
git diff --cached --check
if git diff --cached --quiet; then
  echo "No changes to commit."
  exit 0
fi

git commit -m "$MESSAGE"
git log -1 --oneline
git status --short --branch
