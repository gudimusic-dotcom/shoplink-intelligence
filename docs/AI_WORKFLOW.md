# SHOPLINK Intelligence GitHub workflow

## Source of truth

- GitHub repository: `git@github.com:gudimusic-dotcom/shoplink-intelligence.git` (`origin`).
- Local working folder: `/Users/peterzatyko/Documents/web/Shoplink Intelligence`.
- Staging: `https://shoplinkstage.1web.sk`.
- Staging is a deploy target, not a source checkout.

## Work from home or office

```bash
git clone git@github.com:gudimusic-dotcom/shoplink-intelligence.git
cd "Shoplink Intelligence"
git checkout main
git pull --ff-only origin main
```

Before editing:

```bash
sed -n '1,240p' docs/CURRENT_HANDOFF.md
sed -n '1,220p' docs/WORKSTATE.md
git status --short --branch
```

After a verified change:

```bash
./scripts/finish-work.sh "Short change description"
git push origin main
```

## Staging deploy

```bash
./scripts/deploy-shoplinkstage.sh --dry-run
./scripts/deploy-shoplinkstage.sh
```

The deploy script creates a remote backup before upload, excludes `.env`, dependencies, generated build output and runtime data, clears Laravel caches, and aborts when a remote Git worktree exists and is dirty. Use `--force` only after reviewing the remote state.

## Never commit

- `.env` or any secret
- `vendor/`, `node_modules/`, generated caches and logs
- staging database or uploaded runtime data
- private SSH keys, tokens, passwords or session files

## Initial GitHub setup

The private repository is already connected. For a fresh checkout:

```bash
git clone git@github.com:gudimusic-dotcom/shoplink-intelligence.git
cd "Shoplink Intelligence"
```
