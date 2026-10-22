# SHOPLINK Intelligence - Agent Rules

This file is mandatory context for every Codex session in this project.

## First Read Order

Before changing code, read:
1. `docs/CURRENT_HANDOFF.md`
2. `docs/WORKSTATE.md`
3. `docs/RESUME_PROTOCOL.md`

## Working Rules

- Hungarian user communication is preferred unless the user switches language.
- Do not make broad layout/design changes while fixing a narrow functional bug.
- For a UI bug, inspect the browser console and real page behavior first.
- For staging-only behavior, verify on `https://shoplinkstage.1web.sk`, not only locally.
- Change the smallest possible file set.
- After every staging change: upload changed files, clear Laravel view/cache if Blade/PHP changed, and verify with the lightest sufficient method.
- Do not claim a feature works until it was clicked/tested in browser or the limitation is explicitly stated.
- Keep `docs/CURRENT_HANDOFF.md` and `docs/WORKSTATE.md` updated after important fixes.

## Efficiency Rules

- Work with minimal token usage.
- Do only the requested change; do not expand scope.
- Read only files needed for the current task after the mandatory handoff files.
- Do not repeat failed sandbox network commands; if DNS/network fails, use approved escalated mode once.
- Do not browser-login for small Blade/CSS changes unless visual or click verification is required.
- For simple UI changes use: edit, syntax/build, upload, cache clear, HTTP asset check.
- Keep progress updates very short.
- Final answer should only include what changed, where it was deployed, and what was verified.

## Micro UI Change Lock

Use this lock for requests such as:
- `+1px` font size
- color tweak
- spacing tweak
- border tweak
- single-field visual cleanup

Mandatory limits:
- touch the smallest possible file set
- default maximum is `1 file` and `1 patch`
- do not change Blade structure for a micro UI request
- do not change JS for a micro UI request
- do not refactor nearby code during a micro UI request
- do not include opportunistic cleanup or extra fixes

Allowed scope for micro UI requests:
- one existing CSS rule
- one existing utility class usage
- one existing input class assignment
- at most `1-3` directly related lines if no CSS-only route exists

Hard stop conditions:
- if the change would require touching both markup and CSS, stop and state that it is no longer a micro change
- if the change would affect create panel, edit panel, and task list together, stop and state the risk before editing
- if the diff grows beyond the directly requested element, stop and shrink the change

Required execution order for micro UI requests:
1. identify the exact selector or exact line first
2. change only that selector or line
3. build with the lightest sufficient check
4. deploy only the required files
5. verify only the requested element

Micro UI deploy rule:
- if the resulting diff is not obviously narrow, do not deploy yet
- first re-read the edited block and reduce it

Task Center protected zones during micro UI work:
- do not change create/edit panel open-close logic
- do not change date submit binding
- do not change TallStack date runtime imports
- do not change task row click behavior
- do not change mobile row composition
- unless the user explicitly asked for one of those

## Task Center Date Picker Rules

Do not replace TallStackUI date fields with native browser date inputs unless the user explicitly requests it.

Required implementation:
- `platform/resources/js/app.js` imports:
  - `../../vendor/tallstackui/tallstackui/dist/tallstackui-NrxCew1f.js`
  - `../../vendor/tallstackui/tallstackui/dist/tallstackui-date-CbEf0FZx.js`
  - `@alpinejs/anchor`
  - `@alpinejs/intersect`
- Register plugins before `Alpine.start()`:
  - `Alpine.plugin(anchor)`
  - `Alpine.plugin(intersect)`
- The real submitted date field is hidden `name="due_at"`.
- Create/edit submit handlers copy `.sl-field-shell-date input[type="text"]` into hidden `name="due_at"`.
- Do not use selectors such as `input[name="due_at_create_picker"]`; TallStackUI does not render that named input in this Blade-only context.
- Keep `.sl-field-shell-date > div` borderless to avoid the double/thick date input border.

Known failure modes:
- Missing date bundle: console shows `tallstackui_formDate is not defined`.
- Missing Alpine anchor plugin: calendar opens but appears at the wrong page position, for example `x:0, y:784`.
- Missing submit copy: visible date changes, but saved task has no `due_at`.

## Staging

- URL: `https://shoplinkstage.1web.sk`
- SSH command: `ssh -i "/Users/peterzatyko/.ssh/rma_websupport_ed25519" -p 27498 uid118160@shell.r5.websupport.sk`
- Remote app root: `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`
- Local Laravel app root: `platform/`
- Remote has no nested `platform/` directory.
- Local `platform/resources/...` deploys to remote `resources/...`.
- Local `platform/public/build/...` deploys to remote `public/build/...`.
- On this host, Vite assets are served from `/build/app-...`, not `/build/assets/app-...`.

## Staging Login

- Staging credentials are stored outside Git and must never be committed.

## Quality Bar

If a simple bug takes longer than expected:
- stop expanding scope,
- state the current verified facts,
- inspect console/network/DOM,
- fix only the root cause,
- verify in browser,
- document the result.
