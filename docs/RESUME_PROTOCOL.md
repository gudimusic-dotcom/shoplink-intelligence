# SHOPLINK Intelligence - RESUME PROTOCOL

Use this protocol on every workstation before continuing work.

## Goal

Make continuation deterministic.

A new workstation should not require reconstructing context from memory or chat history.

## Canonical order of truth

When resuming, use sources in this exact order:

1. `docs/CURRENT_HANDOFF.md`
2. `docs/WORKSTATE.md`
3. `docs/SESSION_LOG.md`
4. active business docs in `docs/`
5. current code in `platform/`
6. chat history only as secondary context

If chat history is missing or incomplete, continue from the files above.

## Required resume sequence

On a new machine or new session:

1. Open `docs/CURRENT_HANDOFF.md`
2. Open `docs/WORKSTATE.md`
3. Open `docs/SESSION_LOG.md`
4. Confirm current design and next-step direction
5. Open these implementation files:
   - `platform/app/Providers/AppServiceProvider.php`
   - `platform/app/Http/Controllers/DashboardController.php`
   - `platform/app/Http/Controllers/TaskController.php`
   - `platform/resources/views/tasks/index.blade.php`
   - `platform/resources/views/dashboard.blade.php`
   - `platform/resources/views/layouts/app.blade.php`
   - `platform/resources/views/layouts/navigation.blade.php`
   - `platform/resources/css/app.css`
   - `platform/routes/web.php`
   - `platform/public/build/manifest.json`
6. Run local checks:
   - `php artisan test`
   - `./vendor/bin/pint --dirty`
   - `npm run build`
7. Only after that continue implementation

If the active task is a micro UI change:
- activate `Micro UI Change Lock` from `AGENTS.md` before editing
- identify the exact selector or exact line before opening extra files
- if more than `1 file` or `1 patch` seems necessary, stop and explicitly reclassify the task as non-micro before proceeding

If the active task concerns TallStackUI date fields:
- check `platform/app/Providers/AppServiceProvider.php` first
- keep provider-level TallStackUI soft customization as canonical
- do not reintroduce page-level date picker CSS hacks unless the provider solution is proven impossible

## Required end-of-session sequence

At the end of every meaningful work block:

1. Update `docs/CURRENT_HANDOFF.md` when current access, deployment state, assets, or next steps change
2. Update `docs/WORKSTATE.md`
   - current state
   - current design decision
   - next recommended action
   - latest built asset names if relevant
3. Append a short entry to `docs/SESSION_LOG.md`
4. If deployment happened:
   - record where it was deployed
   - record the exact asset name
   - record whether cache clear happened

## Staging continuation checklist

Before touching staging:

1. Confirm current asset names from `platform/public/build/manifest.json`
2. Confirm server root from `docs/WORKSTATE.md`
3. Confirm DB/server credentials from `docs/WORKSTATE.md`
4. Upload changed app files
5. Upload matching `manifest.json`
6. Upload matching built CSS and JS assets
7. Clear server cache:
   - `php artisan view:clear`
   - `php artisan cache:clear`
   - `php artisan config:clear`
8. Verify the login HTML references the new CSS asset
9. Verify the changed screen in browser on staging

For a micro UI deploy:
- verify only the requested element first
- if unrelated visual regression appears, stop further edits and restore narrow scope before making new changes

Current SSH command pattern:
- `ssh -i "$HOME/.ssh/rma_websupport_ed25519" -p 27498 uid118160@shell.r5.websupport.sk`

Current staging app root:
- `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`

Important:
- staging has no extra `platform/` directory
- deploy paths must be relative to the staging app root
- if local file is `platform/resources/css/app.css`, remote target is `resources/css/app.css`
- if local file is `platform/public/build/assets/app-*.css`, remote target is `public/build/assets/app-*.css`

## Communication sync principle

Desired behavior:
- both workstations should feel like one continuous task

Practical rule:
- never rely on chat alone for continuity
- always write essential state to repository files

This means:
- server access details must live in project docs
- current deployment state must live in project docs
- latest design decisions must live in project docs
- next step must live in project docs

## Non-negotiable rule

If a decision is important enough to remember, it must be written to:
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
or
- `docs/SESSION_LOG.md`

If it is not written there, it must be treated as non-canonical memory.
