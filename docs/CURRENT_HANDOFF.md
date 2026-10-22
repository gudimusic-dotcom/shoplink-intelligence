# SHOPLINK Intelligence - CURRENT HANDOFF

Last updated: 2026-09-21 / Partnertele Product Stats link

Previous local feature work:
- Added a separate `/sales/category-report` report for category assortment expansion analysis.
- Added a top channel filter with `Všetko`, `Len marketplace`, and `Bez marketplace` (own web); all six KPIs recalculate for the selected channel.
- The report shows revenue, profit, weighted margin rate, catalog SKU count, revenue per SKU, and the percentage/count of catalog SKUs needed to reach 80% of category revenue.
- It supports the existing 2nd level, 3rd level, and leaf category aggregation plus date filters.
- Deployed to staging and browser-verified on 2026-09-20; the 3rd-level table loaded 617 category rows and the 2nd-level filter recalculated the grouped values.
- Latest frontend assets after the category KPI report: `public/build/assets/app-vQK-Wqh6.css` and `public/build/assets/app-D8T4qs8Q.js`.

Latest local feature work:
- Product Stats now uses 100-row server-side pagination per supplier, with filter-preserving previous/next links; full supplier totals remain in the group header. Added query indexes for Product Stats items and incoming feed lookups. Deployed and browser-verified on 2026-10-13.
- Settings now has confirmed delete actions for imported FDD incoming data and FDD/Hurtel supplier-stock data. Deployed and browser-verified on 2026-10-08; delete actions were not executed during verification.
- Settings EUR exchange-rate controls now use a space-saving single-row layout: value input followed by currency label. Deployed and browser-verified on 2026-09-28.
- Settings supplier cards were visually aligned: supplier names now share the input left edge, labels use the default form size, inner boxes use consistent spacing, and the label is corrected to `Objednaný tovar`. Deployed and browser-verified on 2026-09-28.
- Settings „Kurzy voči EUR“ input cards are now compact, sized for values up to 10 characters. Deployed and browser-verified on 2026-09-28.
- Settings task-category creation form now clearly labels the fields and action as task categories. Deployed and browser-verified on 2026-09-28.
- Product Stats now supports a separate Hurtel XML stock import. The parser matches Hurtel `<product id="...">` to the Product Stats `Dod.kód.` field and shows `Skladom`, `Vypredané`, or `Neznáme` in a `DOD sklad` column. The feed import controls for Hurtel and FDD are grouped under Settings → Product Stats → Dodávatelia a objednávanie; Product Stats itself keeps only the general XLSX import.
- Product Stats keeps the original `Sklad. kód` display: the secondary line remains the supplier EAN/helper code extracted from `Dodávateľ`; `Dod.kód.` is now shown as a secondary line under SKU and is also used as the supplier-feed matching key.
- SKU and `Dod.kód.` secondary lines are constrained with ellipsis and title tooltip so long supplier codes cannot expand the table.
- `Sklad. kód` is intentionally never truncated because it contains the copyable EAN.
- Product Stats has a `DOD sklad` filter for all / in stock / out of stock / unknown supplier-feed status.
- Settings labels distinguish FDD `Objednateľný tovar` (incoming feed) from the separate future FDD `Sklad dodávateľa` feed; Hurtel is labeled as supplier stock.
- Settings layout now uses a narrow task-category editor, compact exchange-rate inputs, and a wide supplier grid with separate incoming/stock columns.
- Product Stats now includes a Partnertele supplier product link using the row EAN after `query=`; deployed and browser-verified on 2026-09-21.
- Product Stats now includes a Toptel supplier product link using the row EAN in Toptel's search URL; deployed and browser-verified on 2026-09-21.
- Sales Intelligence now includes a separate `/sales/top30` page linked as `Top 30` in the Sales header only; the main `/sales` overview no longer embeds the grid.
- Deployed to staging on 2026-09-14; browser verified that `/sales` shows the top-right button without the embedded grid and `/sales/top30` shows 30 image cards with live 7-day values.
- Top 30 product card background was lightened from dark slate on 2026-09-14; staging view cache cleared and `/sales/top30` rechecked.
- Top 30 uses a rolling 30-day period with comparison to the previous 30 days. It supports `Kusy`, `Obrat`, `Marža`, and sales-based `Potenciál`; cards show average EUR price/ks, margin rate, revenue, and potential score. Catalog/model grouping is normalized to prevent duplicate cards.
- Latest frontend assets after Settings exchange-rate layout: `public/build/assets/app-LcClJ1ft.css` and `public/build/assets/app-D8T4qs8Q.js`.
- Product Stats P30 column has a light blue background for visibility; the selected product row uses a medium-light slate background. Deployed and browser-verified on staging on 2026-09-14.

## 2026-09-14 Resume Audit

- Websupport SSH verified with the shared key `/Users/peterzatyko/.ssh/rma_websupport_ed25519`; key filename is historical and does not identify this project
- staging HTTPS verified: `https://shoplinkstage.1web.sk` redirects to `/login`
- remote app root and `.env` exist; Laravel 13.23.0 / PHP 8.5.10 respond
- all 21 remote migrations are marked `Ran`
- critical task, PWA, push, and Product Stats routes are present
- current staging asset URLs are `/build/assets/app-B3_QrEUg.css` and `/build/assets/app-D8T4qs8Q.js`
- staging PWA endpoints `/sw.js` and `/manifest.webmanifest` return `200`
- staging `.env` currently reports `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`, `DB_CONNECTION=sqlite`; no `WEB_PUSH_*` entries were found
- local `platform/.env` uses SQLite and local URL as expected; local PHP CLI is not available in PATH
- local `npm run build` passed and produced the same CSS/JS names as the current staging manifest; assets were not deployed in this audit
- project root currently has no Git repository (`.git` directory not found)

Open this file first when continuing from another machine, then read:
1. `AGENTS.md`
2. `docs/WORKSTATE.md`
3. `docs/SESSION_LOG.md`
4. `docs/RESUME_PROTOCOL.md`

## Current State

- Local project root: `Shoplink Intelligence/`
- Laravel app root: `platform/`
- Staging URL: `https://shoplinkstage.1web.sk`
- Current UI direction: clean, premium, dark SaaS operational UI
- Implemented modules on staging: Dashboard, Task Center, User Management, Sales Intelligence, Settings
- User-specific iPhone/Home Screen Web Push base is now implemented on staging
- User admin edit/create now shows readable light role/notification controls and saves `web_push_notif_new_task`
- Task Center now has snapshot polling so list, edit panel, status, due date, and comments can refresh without manual page reload
- Task Center now uses a two-state completion flow for all tasks: `Nová` / `Vybavená`
- every task row has an instant completion checkbox before the title; the right-side `Status` column was removed
- Current active task outcome: task create/edit date pickers are dark, task edit opens as a right-side modal from `/tasks`, the Task Center list is grouped by task category, and task categories are managed from Settings
- Product Stats supplier settings are now stored in DB and editable in Settings: supplier portal URL template plus target coverage days for `OBJ`
- Product Stats main list is now sorted by latest `ORDER` date first, then by purchase priority
- Product Stats status badges now open the same supplier portal URL as the supplier shortcut badge
- Product Stats marks a product `Objednať` when effective S1 stock is less than or equal to P30 sales
- Sales category report uses only Google Merchant `product_type` and supports 2nd level, 3rd level, or final category aggregation.
- Active execution rule: `Micro UI Change Lock` in `AGENTS.md` is now mandatory for small visual requests to prevent broad unintended Task Center regressions

## Latest Date Picker Fix

The date picker must not be fixed with page-level CSS guesses.

Canonical implementation:
- `platform/app/Providers/AppServiceProvider.php`
- TallStackUI soft customization for:
  - `form.date`
  - `form.date.input`
  - `form.date.floating`

Tailwind requirement:
- `platform/resources/css/app.css` contains `@source '../../app/Providers/*.php';`

Task views:
- `platform/resources/views/tasks/index.blade.php`

Important:
- old `sl-task-date-*` and `ts-date-reset-*` hacks are obsolete
- the right side of the input is no longer white
- the calendar popup is no longer light/browser-default
- `platform/resources/views/tasks/edit.blade.php` was removed; the edit route now renders the index view with a selected task modal

## Latest Assets Before Category KPI Report

From `platform/public/build/manifest.json`:
- CSS: `public/build/assets/app-xalE3_95.css`
- JS: `public/build/assets/app-CH0aJJYF.js`

Staging verification:
- `/build/manifest.json` returns `200`
- `/build/app-BVNMMBvK.css` returns `200`
- `/build/app-6RjXc3zl.js` returns `200`
- `/build/assets/app-BVNMMBvK.css` must not be treated as canonical on this host
- `/tasks/5/edit` date picker was visually checked in browser
- `/tasks` quick-create date picker was visually checked in browser

Latest task create/edit panel deploy:
- CSS: `public/build/assets/app-C-vagXGD.css`
- JS: `public/build/assets/app-jZpbGTZ3.js`
- `/tasks` renders with `openEditId: null`
- header `Nový task` opens a right-side create slide-over
- the old bottom `Nová úloha` create block was removed
- clicking anywhere on a task row opens the edit slide-over with `openTaskEdit(id)`, except the status dropdown
- the list `Upraviť` button/link was removed
- the task list is grouped by compact category separators, ordered by task category `sort_order` / `name`
- the category column was removed from task rows; category now appears once at the start of each group
- empty categories are hidden; uncategorized tasks appear under `Bez kategórie`
- Settings now has a `Kategórie taskov` section for creating categories, editing category names/descriptions, drag-and-drop ordering, and toggling `is_active`
- category order is changed by dragging the left handle; the backend persists it by rewriting `sort_order` in 10-step increments
- the numeric `sort_order` field is hidden in edit rows and remains only as a technical fallback; users should not need to edit order numbers manually
- Settings category inputs are flat dark: the TallStack input wrapper ring/background is reset via `div:has(> .ts-field-reset)` / `.relative:has(.ts-field-reset) > div`
- Settings category editor list has one shared desktop header (`Názov`, `Popis`, `Aktívna`); labels are not repeated visibly in every row
- Task create/edit panels have vanilla JS panel fallbacks in `resources/js/app.js`
- create panel uses `data-task-create-*`; edit panels use `data-task-edit-*`
- Do not rely only on Alpine for Task Center panel open/close behavior
- create category and priority use the same Shoplink `sl-status-menu` badge dropdown pattern as edit, not native TallStack selects
- task create/filter dropdowns show active categories only; existing tasks with inactive categories remain visible in their group
- edit/create date submit copies the visible TallStack input value from `property=due_at_picker_ID` / `property=due_at_create_picker` into the real hidden `name=due_at`
- task due dates are displayed as date only (`d.m.Y`), without `0:00`
- edit status uses the same Shoplink `sl-status-menu` / `sl-task-status-*` dropdown styling as the task list
- edit priority and category also use Shoplink badge dropdowns, not native selects
- `/tasks/5/edit` renders with `openEditId: 5` as deep-link fallback
- ESC closes the visible create/edit panel without navigating back
- `/tasks/5/edit` HTTP 200 after deleting the obsolete standalone edit Blade
- latest `/tasks` stage browser verification showed compact category groups such as `Objednávka` and `Platba`
- latest `/settings` stage verification showed 6 drag-enabled category rows, 6 drag handles, 1 create form, and working reorder endpoint
- latest visual check confirmed no white TallStack wrapper/ring around category editor inputs
- latest visual check confirmed the category editor is compact: one header row, six data rows, no repeated visible row labels
- latest `/tasks` browser click test confirmed `Nový task` opens the create panel and task title click opens the edit panel with JS asset `app-jZpbGTZ3.js`

## ITHZ-MCP Local Setup

Installed on this Mac as a documented macOS fallback:
- Runtime: `/Users/peterzatyko/.local/share/ithz-mcp/runtime`
- Project bootstrap: `project.md`
- Install log: `.ithz-install/install.log`
- Cursor MCP config: `.cursor/mcp.json`
- Claude Code MCP config: `.mcp.json`
- Codex MCP config: `/Users/peterzatyko/.codex/config.toml`

Important:
- official macOS package did not include compatible `ithz-native`
- `project.ithz` was not created
- storage profile is `legacy`
- `.ithz_mcp/` is ignored and must not be committed
- do not run ITHZ legacy search/index on this project until secret-safe allowlisting is configured, because project docs contain staging credentials

Verified:
- `python3 -m ithz_mcp version` passed from the installed runtime
- `python3 -m ithz_mcp self-test --verbose` passed
- `.cursor/mcp.json` validation passed
- `.mcp.json` validation passed
- MCP client smoke partially passed for read-only basics, but archive/search checks failed because native archive storage is missing

## Staging Login

- Staging credentials are stored outside Git and must never be committed.

## SSH Access

Command:
- `ssh -i "/Users/peterzatyko/.ssh/rma_websupport_ed25519" -p 27498 uid118160@shell.r5.websupport.sk`

Server app root:
- `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`

Server public root:
- `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage/public`

Important:
- staging has no nested `platform/` directory
- local `platform/resources/...` deploys to remote `resources/...`
- local `platform/public/build/...` deploys to remote `public/build/...`
- on `shoplinkstage.1web.sk`, the public asset URL is currently `/build/app-...`, not `/build/assets/app-...`

## Staging Database

- DB host: `db.r5.websupport.sk`
- DB port: `3306`
- DB name: `shoplink_int27`
- DB credentials are stored outside Git and must never be committed.

## Deploy Checklist

1. Run checks locally:
   - `php artisan test`
   - `./vendor/bin/pint --dirty`
   - `npm run build`
2. Confirm asset names in `platform/public/build/manifest.json`
3. Upload changed files to the staging app root
4. Upload `platform/public/build/manifest.json`
5. Upload matching CSS and JS assets from `platform/public/build/`
6. Clear staging caches:
   - `php artisan view:clear`
   - `php artisan cache:clear`
   - `php artisan config:clear`
7. Verify over HTTP that the referenced CSS and JS return `200`
8. Verify login/dashboard HTML references the same asset names
9. Verify the changed screen in browser

## Next Recommended Work

1. Browser-click verification of cross-device Task Center sync:
   - change status in list
   - open edit panel and confirm mirrored state
   - add comment on one device and confirm refresh on another open screen
2. End-to-end real iPhone verification of Web Push from Home Screen icon with a real user tap on `Zapnúť push`
3. Continue `Product Stats` on the new XLS schema already deployed
4. Continue with RMA front interactions

## Latest Product Stats Supplier Settings

- deployed on 2026-08-20
- new DB table:
  - `product_stat_supplier_settings`
- new model:
  - `App\Models\ProductStatSupplierSetting`
- new route:
  - `PATCH /settings/product-stats-suppliers`
- Settings now edits per supplier:
  - `portal_url_template` with `{code}` placeholder
  - `target_coverage_days`
- Product Stats now builds supplier portal URLs from this table instead of hardcoded controller matches
- Product Stats now recalculates `recommended_s1_order` per supplier using that supplier's `target_coverage_days`
- staging migration was executed and seeded the default suppliers; Toptel was added later through a targeted staging DB setting update because unrelated older migrations were pending there

## Latest Task Center Sync State

- deployed on 2026-08-13
- new route:
  - `GET /tasks/snapshot`
- new controller response:
  - returns task title, status, status label, due label/state, rendered comments HTML, and `updated_at`
- current frontend behavior:
  - `/tasks` polls visible task ids every `15s`
  - polling pauses while the tab is hidden
  - polling runs again on tab focus/visibility return
  - task list title, inline status, edit-panel status, due date badges, and comments are updated in place without full refresh
- stage verification completed:
  - `php artisan route:list` contains `tasks/snapshot`
  - new assets return HTTP `200`
  - unauthenticated `GET /tasks/snapshot` redirects to `/login` as expected
- verification gap:
  - live browser interaction still needs a real click test with two open clients or one client plus mobile

## Latest Web Push / PWA State

- iOS Home Screen PWA Web Push groundwork was implemented on 2026-08-13.
- Current goal covered:
  - user-specific subscriptions
  - Home Screen PWA support
  - first push event for `nová priradená úloha`
- New DB:
  - `push_subscriptions`
  - `users.web_push_notif_new_task`
- New routes:
  - `GET /manifest.webmanifest`
  - `GET /sw.js`
  - `POST /push/subscriptions`
  - `DELETE /push/subscriptions`
- New app files:
  - `platform/app/Models/PushSubscription.php`
  - `platform/app/Http/Controllers/PushSubscriptionController.php`
  - `platform/app/Services/WebPushService.php`
  - `platform/resources/views/pwa/sw.blade.php`
- Current user-facing setup:
  - `Nastavenia` contains a new `iPhone / PWA push` block
  - user can enable/disable `Push pri novej priradenej úlohe`
  - user can tap `Zapnúť push` / `Vypnúť push`
- Current implementation behavior:
  - subscription is stored by `user_id`
  - one user can have multiple devices
  - the same device subscription is rebound to the currently logged-in user when saved again
  - new task push is sent only to the assigned user, not globally
- Stage env already configured:
  - `WEB_PUSH_PUBLIC_KEY`
  - `WEB_PUSH_PRIVATE_KEY`
  - `WEB_PUSH_SUBJECT=mailto:task@shoplink.sk`
- Stage verification completed:
  - `manifest.webmanifest` returns `200`
  - `sw.js` returns `200`
  - push subscription routes exist in `php artisan route:list`
  - new migrations ran successfully on 2026-08-13
- Verification gap:
  - real delivery to iPhone was not fully confirmed in this session because that requires a real Home Screen install, user permission prompt, and a live subscribed device

## Sales Intelligence Purchase Recommendation Notes

Goal:
- Build a Sales Intelligence module that answers: which products should be reordered, from which supplier, and roughly how many units.

Input exports reviewed:
- `/Users/peterzatyko/Downloads/Sumarizacia_predaja.xlsx`
  - Sheet: `export`
  - Rows: 1895
  - Columns include: `Kód`, `EAN`, `Skladový kód`, `Dodávateľ`, `Dod.kód.`, `Názov`, `S1`, `S2`, `Predaj`, `Ext`, `Výpredaj`, `NC bez DPH (€)`, `LastOrder`, `Dní`
  - Missing in that file: `P30`, `POP`
- `/Users/peterzatyko/Downloads/predaj.xlsx`
  - Sheet: `Sheet1`
  - Confirmed workbook size on 2026-08-12: `2897` rows, `19` columns
  - First visible row contains product data directly; there is no readable header row in the top line of the file
  - Columns include: `Kód`, `Skladový kód`, `DOD`, `Dod.kód.`, `Názov`, `S1`, `S2`, `Pre`, `NoA`, `Alz`, `Pr1`, `Pr2`, `P30`, `POP`, `Ext`, `Vypr`, `NC`, `LastOrder`, `Dní`

Field meanings from user:
- `Kód` = internal product code / SKU
- `Skladový kód` = EAN
- `DOD` / `Dodávateľ` = supplier; sometimes includes helper EANs, but starts with supplier name/abbreviation
- `Dod.kód.` = supplier product code
- `S1` = stock in Veľký Krtíš
- `S2` = stock in Prague; currently Alza orders are sent from S1
- `Pre` / `Predaj` = sales for the selected export period
- `P30` = sales in last 30 days
- `POP` = total lifetime product sales
- `Vypr` / `Výpredaj` = if set, product is clearance and should not be reordered
- `LastOrder` = last sale date
- `Dní` = product age in the system

Recommended import requirements to request from the external programmer:
- Keep `P30`, `POP`, `Pre`, `S1`, `S2`, `DOD`, `Dod.kód.`, `NC`, `LastOrder`, `Dní`, `Vypr`
- Add export period metadata, preferably explicit `period_from` and `period_to`
- If possible, add `P90`, supplier lead time, reserved stock, and already ordered/incoming quantity

Recommended module logic:
- total stock = `S1 + S2`
- sale velocity = preferably `P30 / 30`; fallback `Pre / selected_period_days`
- stock coverage days = `total_stock / sale_velocity`
- do not recommend reorder when `Vypr` is set
- priority groups: `Objednať teraz`, `Čoskoro dôjde`, `Dostatok skladu`, `Výpredaj - nekupovať`, `Bez predaja`
- first screen should be supplier-grouped reorder list with SKU, product name, supplier, stock, P30, POP, coverage days, last sale, purchase cost, and recommended quantity

## Latest Fix

- light-theme cleanup deployed for Users + Task edit UI
- `Používateľ je aktívny` and task email notification blocks use the shared light `sl-checkbox-surface`
- task edit `Zmazať task` button now uses dark text for contrast
- task edit note textarea, new comment textarea, and comment edit textarea use standard `text-base` sizing
- staging caches cleared after deploy on 2026-08-12

- `Product Stats` was expanded on 2026-08-12 from a simple scaffold into the current working admin-only module.
- Access remains admin-only via the same Users gate.
- Current module capabilities:
  - summary XLSX import with period fields `Od` / `Do`
  - parser works by header names, not fixed column indexes
  - supports new XLS headers:
    - `Kód` -> SKU
    - `Skladový kód` -> stock code
    - `Dodávateľ` or `DOD` -> supplier
    - `Predaj` or `Pre` -> selected-period sales
    - `NC bez DPH (€)` / `NC bez DPH` / `NC` -> purchase cost
    - `Výpredaj` / `Vypr` with new `0/1` handling
  - supplier normalization now keeps text suppliers like `R2invest`
  - suppliers can be split from combined source values, but purely numeric fragments must not become suppliers
  - FDD incoming CSV import by EAN + quantity
  - FDD incoming clear action
  - reorder math now counts incoming quantity so already ordered stock is not recommended again
  - main table now includes:
    - `Produkt`
    - `SKU`
    - `Sklad. kód`
    - `Stav`
    - `S1`
    - `Na ceste`
    - `S2`
    - `Pre`
    - `P30`
    - `POP`
    - `S1 dni`
    - `Objednať`
    - `NC`
    - `LastOrder`
  - compact grouped `Importy` area:
    - `Sumarizácia XLSX`
    - `FDD`
    - status badge `Prázdne` or `Naplnené X / Y ks`
    - auto-submit after file selection
  - optional `Prioritné` filter toggle
  - priority supplier section now shows a real compact table, not stacked cards
- New Product Stats app files now relevant:
  - `platform/app/Http/Controllers/ProductStatController.php`
  - `platform/app/Models/ProductStatImport.php`
  - `platform/app/Models/ProductStatItem.php`
  - `platform/app/Models/ProductStatIncomingItem.php`
  - `platform/app/Services/ProductStatsImportService.php`
  - `platform/app/Services/ProductStatsIncomingImportService.php`
  - `platform/resources/views/product-stats/index.blade.php`
  - `platform/routes/web.php`
- New staging migrations already executed:
  - `2026_08_12_130000_create_product_stat_incoming_items_table`
  - `2026_08_12_140000_add_period_to_product_stat_imports_table`
  - `2026_08_12_150000_add_stock_code_to_product_stat_items_table`
- Current business truth for the new XLS format:
  - `Pre` = sales in the selected export period
  - `P30` = last 30 days sales
  - `POP` = total lifetime sales
  - `Pr1` = period sales from S1
  - `Pr2` = period sales from S2
  - `S2` is the Prague/Alza-side stock context
  - displayed stock code must come from XLS `Skladový kód`, not from EAN

- `Product Stats` admin-only module scaffold was added on 2026-08-12.
- Access is intentionally restricted with the same admin gate as Users: `canManageUsers()`.
- New route/module:
  - `/product-stats`
  - `POST /product-stats/import`
- Current module scope:
  - XLSX import of `predaj.xlsx`-like files
  - first-sheet parsing
  - supplier normalization from the start of `DOD`
  - supplier-grouped overview with `S1`, `S2`, `P30`, `POP`, `Alza`, `NC`, `LastOrder`
- New DB tables:
  - `product_stat_imports`
  - `product_stat_items`
- Current limitation:
  - this is the first scaffold only; reorder recommendation rules are not implemented yet

- User Management access was tightened on 2026-08-11.
- Only `admin` role users can open the full Users module and edit any user.
- Canonical gate: `App\\Models\\User::canManageUsers()`.
- Protected routes/actions now include:
  - `/users`
  - `/users/create`
  - `/users/{user}/edit`
  - create/update/test-email actions in `UserController`
- `Používatelia` navigation is hidden for non-admin users.

- Task comments are now implemented on 2026-08-03.
- Every task edit slide-over now has a comment thread section.
- Users can add comments under their own identity and initials avatar.
- Users can edit their own comments inline without refreshing or closing the task panel.
- New staging routes now exist:
  - `tasks.comments.store`
  - `tasks.comments.update`
- Dashboard task window is now configurable per user from Settings.
- New user setting column: `dashboard_task_window_days`
- Dashboard task list now shows tasks due within the configured next-X-days window plus overdue tasks.
- Dashboard overdue dates now use a flat red background with white text; today dates use a flat amber background with white text.
- Current staging build assets are `app--SNK03NE.css` and `app-CbJBg8UK.js`.
- Staging migrations executed successfully:
  - `2026_08_03_120000_create_task_comments_table`
  - `2026_08_03_120100_add_dashboard_task_window_days_to_users_table`

- Task Center emergency UI rollback/fix was deployed on 2026-08-03.
- Restored dark panel styling for task create/edit title, owner, and note fields with one consistent Shoplink border treatment.
- Replaced broken Task Center owner field wrapper with a native dark select inside the same panel style system.
- Tightened the task list desktop columns so the due date stays on the same row instead of dropping lower.
- Current staging manifest now references `app-CTI352Fu.css` and `app-DQ-IHDUq.js`.
- Deploy uploaded:
  - `resources/views/tasks/index.blade.php`
  - `public/build/manifest.json`
  - `public/build/assets/app-CTI352Fu.css`
  - `public/build/app-CTI352Fu.css`
- Staging cache clear passed:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan config:clear`
- Verified on staging server:
  - `public/build/manifest.json` points to `assets/app-CTI352Fu.css`
  - `public/build/assets/app-CTI352Fu.css` exists
  - HTTP `https://shoplinkstage.1web.sk/build/assets/app-CTI352Fu.css` returned `200`
- Verification gap:
  - final visual browser pass was not completed in this session, so if the UI still looks stale in-browser, force refresh first and recheck `/tasks`

- Dashboard was compacted on 2026-08-02.
- Removed dashboard header status pills `Dnes`, `Blokované`, `Bez termínu`.
- Removed top metric cards `Otvorené úlohy`, `Aktívne RMA prípady`, `Dnešné importy`.
- Removed the Today section subtitle `Úlohy zoradené na dnešné spracovanie`.
- Dashboard Today task rows now use `.sl-dashboard-task-row`: one compact row with task title, status, and date when present; no owner column.
- Current staging build assets are `app-Oa95rcDM.css` and `app-DQ-IHDUq.js`.
- Staging HTTP verification passed: `/build/manifest.json` references `app-Oa95rcDM.css`, and `/build/app-Oa95rcDM.css` returns `200`.
- Task Center status changes are now AJAX-based. The list must not reload when changing a task status from the row dropdown.
- `PATCH /tasks/{task}/status` returns JSON when `Accept: application/json`; fallback redirect remains for non-JS form behavior.
- JS handler in `platform/resources/js/app.js` catches `[data-task-status-form]`, sends `fetch()`, updates only `[data-task-status-summary]` / `[data-task-status-label]`, closes the dropdown, and shows toast `Stav úlohy bol uložený.`
- Current staging build assets are `app-DJGC0bTx.css` and `app-DQ-IHDUq.js`.
- Staging browser verification passed: changed task `10` from `Nová` to `Rozpracovaná` without leaving `/tasks`, saw toast, then reverted it back to `Nová`.
- Fixed the Task Center date picker end-to-end on staging.
- `platform/resources/js/app.js` must import the main TallStackUI bundle, `tallstackui-date-CbEf0FZx.js`, `@alpinejs/anchor`, and `@alpinejs/intersect`.
- Without `@alpinejs/anchor`, the TallStackUI date calendar can technically open but render in the wrong position, for example `x:0, y:784`.
- Current staging build assets are `app-BC4X1lUJ.css` and `app-HtRUCpV8.js`.
- Create/edit submit handlers must copy `.sl-field-shell-date input[type="text"]` into hidden `name=due_at`; do not search for `input[name="due_at_create_picker"]`.
- Staging browser verification passed: login, `Nový task`, `Termín` click, calendar positioned under the input, real task create with `17.08.2026`, then test task deletion.
- Keep `.sl-field-shell-date > div` borderless; only the inner TallUI input wrapper should have a single visible border.

Settings layout latest:
- `Task okno a push` is beside `Kategórie taskov` in the upper Settings section.
- Rate edit inputs for Ft, Kč, Zł, lei, and € are present and visible.
- Staging `/settings` was syntax-checked, view cache cleared, and browser-verified on 2026-09-28.
- Upper Settings now has three boxes: categories, task window/push, and add category.
- Task categories have a confirmed delete action; assigned tasks are preserved without a category.
- FDD supplier stock feed is implemented separately from FDD orderable CSV. It accepts ZIP/CSV, maps `id` to supplier code and `ilość produktów` to stock status/quantity.
- Supplied FDD feed is currently imported on staging: 3,858 rows, 3,719 in stock, 139 out of stock.
- Supplier stock URLs are stored in product_stat_supplier_settings.stock_feed_url. FDD ZIP/CSV and Hurtel XML can be refreshed from Settings with Aktualizovať z URL; the screen shows imported count and last checked_at. Other suppliers still need their own parser before URL import can be enabled.
- Settings supplier rows are now two-row supplier cards. FDD also has a stored incoming_feed_url and URL import for the orderable CSV; file import remains available for both FDD orderable data and FDD/Hurtel stock.
- Supplier cards are currently compact two-column layouts: left basic supplier settings, right orderable/stock feed controls. Text labels and metadata use dark text.
- Within the right feed box, orderable and stock panels are side-by-side on medium screens to reduce vertical space.
- Supplier card columns are now always rendered side-by-side in the desktop Settings view; the feed panels are also always side-by-side.
- Inline grid columns were added after the utility classes still stacked in the staging browser; the left basic box is now fixed-width and the right feed box occupies the remaining width.
- Final supplier card proportions use a wider left basics column and vertically stacked orderable/stock panels on the right.
- Settings hides empty supplier cards by default, shows active/configured suppliers first, and provides a toggle to reveal all suppliers.
- Supplier names are now visually emphasized with a dark badge/background; supplier labels and metadata use standard readable sizing.
- Settings exchange rates are forced into a full-width row above supplier settings; they no longer share a column with suppliers on large screens.
- Rate and supplier save buttons are compact, right-aligned controls instead of full-width bars.
- Positive Settings actions use the same sl-btn-primary style/color as Uložiť dashboard okno; delete remains destructive styling.
- Supplier cards were flattened: one main card, one left gray basics panel, and flat right feed sections with a single divider.

Product Stats latest:
- supplier, text-search, and S1/S2 stock filters are applied in the database query before item enrichment
- server-side pagination was removed; full filtered supplier tables are shown again
- the wide table uses local horizontal scrolling for the final columns
## 2026-10-12 / Q4 warehouse-aware preparation

- Product Stats preparation now splits sales from `sales_order_lines` into S1 and S2 planning streams.
- Own web and all non-Alza marketplace sales are assigned to S1 (Veľký Krtíš); Alza sales are assigned to S2.
- S90, S180, Q4, Forecast, and Obj. now show S1/Alza or S1/S2 values separately.
- Recommended quantities subtract incoming goods only from S1; S2 recommendations use current S2 stock.
- Deployed to staging; PHP syntax and Blade cache passed; authenticated browser verification passed on `/product-stats/preparation`.
- Limitation: this is historical order-channel classification. It does not yet verify whether the product listing is currently active on Alza; that requires a separate current marketplace availability/feed check.
- Follow-up fix: staging stores Alza as `ALZA.CZ`; the classifier now matches `ALZA%`. Browser verification shows populated Alza values and reasons.
