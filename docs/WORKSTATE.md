# SHOPLINK Intelligence - WORKSTATE

## Latest Audit - 2026-09-14

## Latest Feature - 2026-09-21

- Product Stats supplier tables now render 100 rows per server-side page with filter-preserving navigation; supplier totals remain visible. Query indexes were added for Product Stats item ordering/import lookups and incoming supplier/EAN lookups. Staging browser-verified on 2026-10-13.
- Settings now exposes confirmation-protected delete buttons for imported FDD incoming data and FDD/Hurtel supplier-stock data; feed URL settings remain preserved.

## 2026-09-28 / Hurtel supplier stock feed

- Settings EUR exchange-rate controls now use a compact single-row input-plus-currency layout. Current CSS asset: `app-LcClJ1ft.css`.
- Settings supplier cards now use aligned content and default-sized form labels; `Objednaný tovar` wording is corrected.
- Settings exchange-rate input cards are now compact and sized for values up to 10 characters.
- Settings task-category creation form now uses explicit labels: `Názov kategórie taskov`, `Poradie kategórie taskov`, and `Pridať kategóriu taskov`.

- Added `product_stat_supplier_stocks` and a dedicated Hurtel IdoSell XML parser.
- Matching key: Hurtel `<product id>` = Product Stats `Dod.kód.`; EAN is not the primary key.
- Stored data is limited to supplier, supplier code, normalized stock status, source filename, and check time. Prices/currency are ignored.
- Settings supplier panel now has `Hurtel / Importovať XML` and the Product Stats main table has `DOD sklad`.
- Supplier-specific feed controls were moved from Product Stats into Settings → Product Stats → Dodávatelia a objednávanie. FDD CSV and Hurtel XML are now shown beside their supplier rows; Product Stats keeps only the general XLSX import.
- Product Stats preserves the full copyable `Sklad. kód` EAN and supports filtering by `DOD sklad` status.
- FDD incoming CSV and supplier stock are separate concepts/tables; Settings now labels the existing FDD feed as `Objednateľný tovar` and shows a separate placeholder for the future FDD stock feed.
- Settings supplier panel has separate columns for `Objednateľný tovar` and `Sklad dodávateľa`; only implemented feed controls are active.
- Imported staging feed: `21,153` Hurtel products from `stock_light_export_for_midinetit2.xml`.
- Staging migration and cache clear completed; browser verified the Product Stats page and displayed import timestamp/count.
- Next step: add the same adapter pattern for each additional supplier feed.

- Product Stats now includes Partnertele supplier links using `https://partnertele.com/products/search?query={code}`; staging browser verified with row EAN `5903396400070`.

- Added separate Sales Intelligence route `/sales/category-report` for category assortment expansion analysis.
- KPI definitions: total revenue, total profit, weighted margin (`profit / revenue`), catalog SKU count including zero-sales SKUs, revenue per catalog SKU, and the percentage/count of highest-revenue SKUs needed to reach 80% of category revenue.
- Added channel filtering for all data, marketplace only, or without marketplace (own web).
- Report supports date filters and category aggregation at 2nd level, 3rd level, or leaf level.
- Deployed to staging and browser-verified: 3rd-level report loaded 617 category rows; selecting 2nd level and submitting the filter recalculated the grouped report.
- Current built assets: `platform/public/build/assets/app-vQK-Wqh6.css` and `platform/public/build/assets/app-D8T4qs8Q.js`.

- Sales Intelligence enhancement deployed: separate `/sales/top30` page with only a top-right Sales header `Top 30` button; the main overview and left menu remain clean. Browser verified on staging.
- Top 30 now uses rolling 30 days versus the previous 30 days, with Kusy/Obrat/Marža/Potenciál modes, average EUR price/ks, margin rate, revenue and potential score; normalized catalog joins prevent duplicate model cards. Staging browser verified.
- Product Stats P30 column is highlighted with a light blue background, and the selected product row uses a medium-light slate background. Staging browser verified.

- Websupport SSH works with the shared key `/Users/peterzatyko/.ssh/rma_websupport_ed25519`; its filename is historical, while the verified remote target is Shoplink staging.
- Staging is reachable at `https://shoplinkstage.1web.sk`; HTTPS redirects to `/login`.
- Remote Laravel root and `.env` exist; PHP 8.5.10 and Laravel 13.23.0 are available.
- Remote migrations: 21/21 `Ran`; critical task, PWA, push, and Product Stats routes exist.
- Current staging manifest: `/build/assets/app-B3_QrEUg.css`, `/build/assets/app-D8T4qs8Q.js`; both return `200`.
- PWA endpoints `/sw.js` and `/manifest.webmanifest` return `200`.
- Important config mismatch requiring explicit follow-up: staging `.env` has `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`, `DB_CONNECTION=sqlite`, and no `WEB_PUSH_*` entries. Do not assume the documented MySQL/Web Push settings are active.
- Local `npm run build` passed. Local PHP CLI is missing from PATH, so local PHPUnit was not run.
- No `.git` directory was found in the project root.

Last updated: 2026-08-31 / Product Stats sorted by latest ORDER
Project root: `Shoplink Intelligence/`
Application root: `platform/`

## Purpose

This file is the single source of truth for continuing work on any workstation.
If work continues on another computer, read this file first.

It records:
- current project state
- environment and deployment settings
- server and database access details
- implemented modules and design direction
- current staging behavior
- next recommended steps
- resume/sync-critical facts between workstations

## Resume First

On any machine, always read in this order:
1. `docs/CURRENT_HANDOFF.md`
2. `docs/WORKSTATE.md`
3. `docs/SESSION_LOG.md`
4. `docs/RESUME_PROTOCOL.md`

These files are the canonical continuation memory.

---

## Current Snapshot - 2026-08-01

Use this as the first operational truth when opening the project on another machine.

Current app state:
- Laravel app root is local `platform/`
- staging runs directly from the Laravel app root, not from a nested `platform/` directory
- current main UI direction is a clean, premium, dark SaaS operational UI
- full User Management is now admin-only; non-admin users should use only `/profile`
- Task Center, User Management and Sales Intelligence are implemented enough to use on staging
- Product Stats admin-only module now includes XLSX period import, header-based parsing, supplier grouping, incoming FDD import, and priority view
- Home Screen PWA Web Push base now exists for user-specific task notifications
- User admin edit/create uses readable light role/notification controls and can save `web_push_notif_new_task`
- Task Center now has snapshot polling for refresh-free state/comment syncing between list and edit panel
- all tasks now use `new` / `done`, shown as `Nová` / `Vybavená`, with AJAX completion checkboxes in the list and edit panel
- the Task Center desktop list no longer has a separate right-side `Status` column
- task create and task edit date pickers are now finalized with TallStackUI provider-level soft customization
- task edit now opens as a right-side modal from `/tasks`; there is no standalone task edit Blade view
- Task Center task rows are grouped by category headers; category is no longer a repeated row column
- Settings contains Task Center category management with create, edit, active toggle, and drag-and-drop ordering
- the date picker fix was deployed to staging and visually checked in browser on both task edit and task create
- the August 1, 2026 dashboard layout failure was caused by wrong public asset URL assumptions on staging, not by the dashboard Blade or Tailwind utility generation

Current critical files from the latest work:
- `platform/app/Http/Controllers/PushSubscriptionController.php`
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/app/Models/ProductStatImport.php`
- `platform/app/Models/ProductStatItem.php`
- `platform/app/Models/PushSubscription.php`
- `platform/app/Models/ProductStatIncomingItem.php`
- `platform/app/Services/ProductStatsImportService.php`
- `platform/app/Services/ProductStatsIncomingImportService.php`
- `platform/app/Services/WebPushService.php`
- `platform/resources/views/product-stats/index.blade.php`
- `platform/resources/views/pwa/sw.blade.php`
- `platform/database/migrations/2026_08_12_090000_create_product_stat_imports_table.php`
- `platform/database/migrations/2026_08_12_090100_create_product_stat_items_table.php`
- `platform/database/migrations/2026_08_12_130000_create_product_stat_incoming_items_table.php`
- `platform/database/migrations/2026_08_12_140000_add_period_to_product_stat_imports_table.php`
- `platform/database/migrations/2026_08_12_150000_add_stock_code_to_product_stat_items_table.php`
- `platform/database/migrations/2026_08_13_110000_create_push_subscriptions_table.php`
- `platform/database/migrations/2026_08_13_110100_add_web_push_task_setting_to_users_table.php`
- `platform/app/Http/Controllers/UserController.php`
- `platform/app/Models/User.php`
- `platform/app/Http/Controllers/TaskController.php`
- `platform/app/Http/Controllers/TaskCategoryController.php`
- `platform/app/Http/Controllers/SettingsController.php`
- `platform/app/Providers/AppServiceProvider.php`
- `platform/routes/web.php`
- `platform/resources/css/app.css`
- `platform/resources/views/tasks/index.blade.php`
- `platform/resources/views/settings/index.blade.php`
- `platform/resources/views/layouts/navigation.blade.php`
- `platform/public/build/manifest.json`
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`

Current frontend assets for staging after the category KPI report:
- CSS: `public/build/assets/app-vQK-Wqh6.css`
- JS: `public/build/assets/app-D8T4qs8Q.js`

Current staging verification:
- `https://shoplinkstage.1web.sk/build/manifest.json` returns `200`
- `https://shoplinkstage.1web.sk/build/app-Oa95rcDM.css` returns `200`
- `https://shoplinkstage.1web.sk/build/app-DQ-IHDUq.js` returns `200`
- `https://shoplinkstage.1web.sk/build/assets/app-BVNMMBvK.css` is not canonical on this host and must not be assumed to work
- staging browser verification uses credentials stored outside Git.
- `/tasks/5/edit` date picker looked dark and visually aligned
- `/tasks` quick-create date picker looked dark and visually aligned
- `/tasks` HTML contains category group headers such as `Všeobecné`, `Objednávka`, and `Platba`
- `/tasks` rows still open edit with `openTaskEdit(id)`, while the status dropdown remains isolated with `data-task-row-ignore`
- `/settings` HTML contains `Kategórie taskov`, 6 category update forms, 6 drag handles, 1 category create form, and drag reorder wiring
- Settings category editor uses flat dark TallStack fields with wrapper ring/background reset; no white input framing should remain there
- Settings category editor uses one shared desktop header for `Názov`, `Popis`, and `Aktívna`; row labels are screen-reader-only to avoid repeated visual noise
- `/tasks` HTML references `app-jZpbGTZ3.js`
- `Nový task` uses a vanilla JS fallback with `data-task-create-*` attributes and was verified to open the right-side create panel on staging
- task edit panels use the same vanilla sheet fallback with `data-task-edit-*` attributes and were verified to open from task title click on staging
- task create category/priority controls match edit styling through Shoplink `sl-status-menu` badge dropdowns; no native create category/priority selects remain
- Task Center now exposes `GET /tasks/snapshot` for in-place syncing of title, status, due state, and comments
- `/tasks` now polls current task ids every 15 seconds and refreshes list/edit values without full reload
- polling is skipped while the browser tab is hidden and reruns when visibility returns
- the broken dashboard layout was restored only after manifest paths were corrected to the actually reachable public URLs
- user edit notification/activity blocks were switched to the shared light checkbox surface
- task edit delete button text was adjusted to dark text for contrast
- task edit note/comment textareas now use standard `text-base` sizing
- Product Stats now supports the new summary XLS format with real column headers
- Product Stats summary import stores selected period dates
- Product Stats item import stores `stock_code`
- Product Stats incoming import currently supports `FDD` CSV by EAN and quantity
- Product Stats reorder calculations already deduct / offset incoming quantities
- Product Stats supplier link templates and target coverage days are now managed in Settings from DB table `product_stat_supplier_settings`
- Product Stats main table now uses the same structure in filtered and unfiltered views
- Product Stats main table is now sorted by latest `ORDER` date first, then by purchase priority
- Product Stats status badges now open the same supplier portal URL as the supplier shortcut badge
- Product Stats marks a product `Objednať` when effective S1 stock is less than or equal to P30 sales
- Sales order line import now reads marketplace context directly from line XLS columns `Ext` and `Portál`; empty `Ext` means own Mobilego web for that portal country
- Sales Intelligence overview no longer depends on `Objednávky XLS`; KPIs are aggregated from `sales_order_lines`
- Re-importing a line XLS replaces existing `sales_order_lines` in the XLS date range before inserting fresh rows
- Sales category displays and future Google Merchant imports strip leading `ESHOP >` from `g:product_type`
- Sales overview includes a separate monthly marketplace vs e-shop channel mix box
- Sales category report uses only Google Merchant `product_type` and can aggregate by 2nd level, 3rd level, or final leaf category
- Product Stats optional `Prioritné` toggle controls the priority supplier section visibility
- `manifest.webmanifest` and `sw.js` are now live on staging
- `Settings` now contains user-facing Web Push controls
- subscriptions are stored per user in `push_subscriptions`
- first push event implemented: new assigned task
- stage `.env` now contains `WEB_PUSH_PUBLIC_KEY`, `WEB_PUSH_PRIVATE_KEY`, `WEB_PUSH_SUBJECT`

Do not restart the date picker work from old CSS overrides. The canonical solution is the TallStackUI soft customization in `AppServiceProvider`.
Do not debug UI layout before confirming the public CSS and JS URLs return `200` over HTTP.
Current module name in the app should be treated as `Product Stats`.

Current Product Stats XLS mapping truth:
- `Kód` = SKU
- `EAN` is available in the new XLS, but displayed stock code must come from `Skladový kód`
- `Dodávateľ` is now a real column; fallback legacy source remains `DOD`
- `NC bez DPH (€)` / `NC bez DPH` is the current purchase cost source
- `Výpredaj` / `Vypr` now behaves as `1 = clearance`, `0/blank = normal`
- `Pre` is the selected-period sales column name to show in UI
- `Pr1` and `Pr2` are period sales from S1 and S2
- supplier names may contain digits inside text, e.g. `R2invest`, and must not be stripped out

Current Product Stats next best step:
- tune supplier-specific target coverage days in Settings based on real purchasing cadence
- add more supplier incoming import adapters after `FDD`

Current Sales Intelligence next best step:
- validate a real full-period line import and compare totals against Mobdata

Current Web Push truth:
- iPhone push target is only Home Screen installed PWA, not a normal Safari tab workflow
- current server package: `minishlink/web-push`
- current subscription model:
  - one subscription row per device endpoint
  - keyed by `endpoint_hash`
  - linked to `user_id`
- current user preference:
  - `users.web_push_notif_new_task`
- current first business event:
  - assigned user gets push for a newly created task
- still missing:
  - live iPhone end-to-end confirmation
  - comment push
  - due/overdue push

Current Task Center sync truth:
- target problem:
  - task updated in list but edit panel still showed old state until manual refresh
  - comment/status changes on another device were not reflected on the open screen
- current fix:
  - server returns a lightweight snapshot payload for visible task ids
  - frontend maps snapshot fields back into both list and edit-panel DOM nodes
  - comments are swapped in place using the existing comment HTML renderer
- current interval:
  - 15 seconds
- current verification:
  - stage route exists
  - stage assets return `200`
  - authenticated browser click verification is still recommended next

---

## Current Coding Status

Coding has already started. The project is no longer documentation-only.

Current reality:
- the Laravel application foundation exists in `platform/`;
- authentication, dashboard shell, Slovak localization, Task Center, User Management and Sales Intelligence are already implemented;
- documentation remains the decision source for all further modules;
- before continuing any feature work, read `CURRENT_HANDOFF.md`, this file, `SESSION_LOG.md`, `RESUME_PROTOCOL.md`, and the relevant active docs.

The old rule “documentation before development” should now be interpreted as: every new module or major workflow must be documented before expanding implementation, not that the existing code foundation should be ignored.

---

## 1. Business/Product Summary

Project name: `SHOPLINK Intelligence`

The system is being built as a premium internal operational intelligence platform in Laravel.

Main intent:
- one central operating screen for sales performance
- purchase recommendation intelligence: which products to reorder, from which supplier, and in what estimated quantity
- RMA / complaint handling
- imports and data health
- task coordination
- user/role visibility

Target language of the application:
- UI language: `Slovak`
- conversation during this build work was in `Hungarian`

---

## 2. Current Technical Stack

Framework:
- Laravel `13.23.0`

Auth:
- Laravel Breeze (Blade)

Frontend:
- Blade
- Livewire `4`
- TallStackUI `3`
- Tailwind `4` / Vite pipeline
- lightweight custom UI component layer on top of Tailwind

Local PHP used during development:
- PHP `8.4.17`

Staging web runtime:
- Apache `2.4`
- PHP `8.5.8`

---

## 3. Main Existing Local Structure

Important local directories:
- `platform/app`
- `platform/resources/views`
- `platform/resources/css`
- `platform/database`
- `platform/routes`
- `docs`

Important current files:
- `platform/app/Http/Controllers/DashboardController.php`
- `platform/app/Models/User.php`
- `platform/app/Models/Role.php`
- `platform/app/Models/Task.php`
- `platform/app/Models/Rma.php`
- `platform/app/Models/ImportJob.php`
- `platform/resources/views/dashboard.blade.php`
- `platform/resources/views/layouts/app.blade.php`
- `platform/resources/views/layouts/navigation.blade.php`
- `platform/resources/css/app.css`
- `platform/routes/web.php`
- `platform/lang/sk.json`
- `platform/lang/sk/auth.php`
- `platform/lang/sk/passwords.php`
- `platform/lang/sk/pagination.php`
- `platform/lang/sk/validation.php`

---

## 4. Models / Domain Already Added

Existing core models:
- `Role`
- `Task`
- `Rma`
- `ImportJob`
- `User` with default role relation

Current task table structure:
- `title`
- `description`
- `priority`
- `status`
- `due_at`
- `assigned_user_id`
- morph relation: `source_type`, `source_id`
- `generated_by`

Current RMA model already supports:
- case data
- status
- reason / resolution fields
- assigned user
- polymorphic tasks relation

---

## 5. Current Functional State

### Implemented now

Working now:
- login
- dashboard route
- Slovak localization on auth pages and dashboard
- premium dark dashboard shell
- left sidebar navigation shell
- top status pill row
- dashboard tabs
- compact KPI strip
- focus modules surface
- Task Center MVP
- task category management
- User Management MVP
- Sales Intelligence Orders Overview MVP

Task Center MVP currently includes:
- task list
- create task
- task category selection
- create custom task categories
- filter by status
- filter by category
- assign task to user
- update task status

Default task categories seeded in Slovak:
- `Všeobecné`
- `Objednávka`
- `Reklamácia`
- `Platba`
- `Fakturácia`
- `Stratený balík`

User Management MVP currently includes:
- user list
- selected user edit panel
- edit name and email
- edit active/inactive state
- edit default role
- edit assigned roles
- optional password reset by filling password fields
- current user cannot deactivate their own account from the edit form

Sales Intelligence Orders Overview MVP currently includes:
- Mobdata XLSX order import from the web UI
- imports only orders with status `Vybavené`
- deduplication by `Kód objednávky`
- stores orders in `sales_orders`
- stores source/channel mapping in `sales_channels`
- stores currency rates in `exchange_rates`
- empty `Ext` is treated as `own_web`
- known marketplace Ext values are treated as `marketplace`
- future `Mobilego.sk` Ext is seeded as `own_web`
- order revenue is stored by original currency
- EUR conversion is calculated only when a currency rate is set
- first overview shows orders/revenue by currency, portal, channel type, marketplace Ext and month

### Not yet implemented as real workflows

Still mostly visual / placeholder:
- RMA front interactions
- import operations UI
- settings UI
- drag & drop modular dashboard

### Best next functional first step

Completed first real function:
- `Task Center MVP`

Why this was first:
- task model already existed
- smallest useful real workflow
- easy to connect later to RMA, imports, finance and order operations

Recommended next function after Task Center:
1. connect tasks to real business sources
2. start with `RMA front`
3. auto-generate tasks from complaint states and SLA rules

---

## 6. Current Dashboard Design Direction

Current accepted direction:
- premium
- dark SaaS operational workspace
- compact, information-dense layout
- flatter product UI
- less “bootstrap dashboard”
- fewer oversized cards
- stronger app-shell feel
- real reusable UI components for panels, buttons, pills, inputs and row lists

Explicit user preferences gathered during work:
- avoid old large KPI cards
- avoid too many separate equal-weight boxes
- avoid heavy box outlines
- avoid decorative corner arcs/lines
- use smaller radii
- use easier-to-scan premium flat composition
- show more content on screen at once
- task views should behave more like a modern work manager, not oversized cards
- tasks should sit in compact operational rows with assignee, status, due date and quick actions visible immediately
- do not leave browser-default or light TallStackUI styling inside Shoplink dark screens
- current main working UI should stay dark, clean and premium

Reference direction user liked:
- premium internal dashboard apps
- flatter enterprise/product UI
- light gray workspace shells
- white glass-like panels

Current main UI architecture:
- left sidebar uses compact component blocks, not decorative shells
- dashboard uses reusable metric cards and operational panels
- Task Center uses row-based list logic with full-width task list and create panel below
- owner is shown compactly with initials in row views
- `blocked` status is presented to users as `Čaká`
- app layout now loads `Livewire` + `TallStackUI` assets explicitly
- TallStackUI component prefix is `ts-`
- first real screen migrated to TallStackUI elements: `Task Center`
- compact pills, rails, utility rows
- morning workspace opening experience with AI help and live operational information

Current structure after latest redesign:
- left app rail
- top utility/status row
- hero heading
- tab strip
- compact KPI surface
- compact task preview table
- AI brief panel
- category and regional pulse side panels
- dense operational content panels below

Remaining UI improvement areas:
- auth pages and secondary settings screens should be aligned to the current dark Shoplink system
- more hover and inline interaction states can still be refined
- dashboard tables and lists can be connected to more live data

---

## 7. Local Verification Status

Latest local check in this Codex context:
- `npm run build` passed on 2026-07-31
- CSS: `public/build/assets/app-Die4Twu5.css`
- JS: `public/build/assets/app-BJMVC_oD.js`

PHP checks from earlier PHP-capable sessions passed multiple times:
- `php artisan test`
- `./vendor/bin/pint --dirty`

PHP checks were not rerun after the final date picker provider customization in this Codex context. Reason:
- the final change is provider-level TallStackUI class customization plus Blade/CSS cleanup
- the final deployed result was verified visually on staging in browser

Latest verified feature test additions:
- Task Center page renders for authenticated user
- task category can be created
- task can be created with category
- task status can be updated

Latest known built frontend assets:
- CSS: `public/build/assets/app-Die4Twu5.css`
- JS: `public/build/assets/app-BJMVC_oD.js`

---

## 8. Staging Environment

Staging URL:
- `https://shoplinkstage.1web.sk`

Current staging runtime:
- Apache `2.4`
- PHP `8.5.8`

Staging paths:
- app root: `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`
- public root: `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage/public`

Current staging deployment state as of `2026-07-31`:
- Task Center code is deployed
- User Management code is deployed
- Sales Intelligence code is deployed
- final TallStackUI date picker customization is deployed
- Laravel view, app and config caches were cleared after latest deploy

Staging test login:
- Staging credentials are stored outside Git and must never be committed.

Latest built asset during current state:
- CSS: `public/build/assets/app-Die4Twu5.css`
- JS: `public/build/assets/app-BJMVC_oD.js`

Important:
- asset names change after rebuild
- when continuing on another workstation, always check current `public/build/manifest.json`

### Staging login for app

- Staging credentials are stored outside Git and must never be committed.

### Server access

SSH host:
- `shell.r5.websupport.sk`

SSH port:
- `27498`

SSH user:
- `uid118160`

Known SSH command pattern:
- `ssh -i "/Users/peterzatyko/.ssh/rma_websupport_ed25519" -p 27498 uid118160@shell.r5.websupport.sk`

Used key path on the current machine:
- `/Users/peterzatyko/.ssh/rma_websupport_ed25519`

### Staging filesystem

App root on server:
- `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`

Public web root on server:
- `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage/public`

Confirmed earlier:
- subdomain document root is correct
- web PHP now runs as `8.5.8`

### Database for staging

DB host:
- `db.r5.websupport.sk`

DB port:
- `3306`

DB name:
- `shoplink_int27`

DB credentials are stored outside Git and must never be committed.

Given socket:
- `/tmp/mariadb114.sock`

Important note:
- for Laravel staging, host/port setup was used effectively
- socket was not the preferred active route in deployment behavior

---

## 9. Staging Deployment Notes

Already executed on staging in earlier setup:
- `composer install --no-dev --optimize-autoloader`
- `php artisan key:generate --force`
- `php artisan migrate --seed --force`
- `php artisan config:clear`
- `php artisan cache:clear`
- `php artisan view:clear`

Important deployment note:
- CSS asset paths must land in `public/build/assets/`
- there was an earlier mistake where CSS was uploaded to wrong path
- always verify `public/build/manifest.json` and the real uploaded asset path match
- if Vite manifest points to `assets/app-*.css`, the uploaded file must exist physically in:
  - `public/build/assets/app-*.css`
- uploading the CSS into `public/build/` root is wrong and will break the UI
- on this hosting, `/build/...` web URLs may also require the mirrored root `build/assets/...` path to exist

Typical deployment files that changed often:
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/navigation.blade.php`
- `resources/views/tasks/index.blade.php`
- `resources/css/app.css`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/TaskController.php`
- `app/Http/Controllers/DashboardController.php`
- `public/build/manifest.json`
- `public/build/assets/*.css`
- `public/build/assets/*.js`

Latest confirmed staging deploy nuance:
- the server is the Laravel app root directly at `shoplinkstage/`
- there is no extra `/platform` directory on the server
- uploads must target paths like:
  - `.../shoplinkstage/resources/...`
  - `.../shoplinkstage/public/build/...`

Latest UI refinement recorded:
- Task date picker was finalized using TallStackUI soft customization in `app/Providers/AppServiceProvider.php`
- task create and edit views no longer carry temporary date picker wrapper override classes
- Tailwind now scans provider-side classes through `@source '../../app/Providers/*.php';`
- latest built CSS asset:
  - `public/build/assets/app-Die4Twu5.css`
- latest built JS asset:
  - `public/build/assets/app-BJMVC_oD.js`

When continuing on another machine:
1. run local tests
2. run build
3. upload changed Blade/controller files
4. upload `manifest.json`
5. upload the new CSS and JS assets from `public/build/assets/`
6. clear staging caches:
   - `php artisan view:clear`
   - `php artisan cache:clear`
   - `php artisan config:clear`
7. verify the login HTML references the new CSS asset
8. verify the changed screen in browser on `https://shoplinkstage.1web.sk`

---

## 10. Localization State

Current expected language:
- Slovak

Already fixed:
- login page Slovak labels
- dashboard Slovak text
- role seed labels Slovak

Current role label example:
- `Administrátor`

Important env values previously used on staging:
- `APP_LOCALE=sk`
- `APP_FALLBACK_LOCALE=sk`
- `APP_FAKER_LOCALE=sk_SK`

---

## 11. Current Routes

Currently important routes:
- `/`
- `/dashboard`
- `/profile`
- `/tasks`
- `/tasks/{task}/edit`
- `/users`
- `/sales`
- `/sales/detail`
- `/sales/import`
- `/settings`

Current route state:
- `/` redirects to login or dashboard based on auth
- `/dashboard` uses `DashboardController`
- Task Center routes are real working routes
- User Management routes are real working routes
- Sales Intelligence and Settings routes are real working routes

---

## 12. Current Design Decisions That Should Not Be Lost

Do not go back to:
- bootstrap-style 4 large KPI cards as dominant hero widgets
- too many equal-sized independent boxes
- strong outlined boxes
- decorative corner arcs / corner strokes
- oversized radii
- busy gradients everywhere

Prefer:
- flatter premium surfaces
- fewer but stronger content zones
- one-shell feeling
- compact status rows
- restrained borders
- controlled spacing
- stronger information hierarchy

---

## 13. Recommended Immediate Next Step

Best next implementation step:
- standardize the finalized TallStackUI date picker pattern everywhere dates appear
- after that, continue with RMA front and RMA-generated tasks

Date picker standardization scope:
- keep the provider-level TallStackUI customization as the shared pattern
- do not reintroduce page-specific date picker CSS hacks
- replace remaining plain text/date fields only where it fits the current Shoplink dark UI
- verify every converted view in browser after staging deploy

Recommended next functional feature:
- RMA front interactions
- connect RMA states/SLA rules to generated tasks
- show generated tasks in Task Center with source context

---

## 14. How To Resume On Another Workstation

Do this first:
1. open `docs/CURRENT_HANDOFF.md`
2. open this file: `docs/WORKSTATE.md`
3. open `docs/SESSION_LOG.md`
4. open `docs/RESUME_PROTOCOL.md`
5. open project root and `platform/`
6. inspect current:
   - `platform/app/Providers/AppServiceProvider.php`
   - `platform/resources/views/tasks/index.blade.php`
   - `platform/app/Http/Controllers/TaskController.php`
   - `platform/resources/views/dashboard.blade.php`
   - `platform/resources/css/app.css`
   - `platform/app/Http/Controllers/DashboardController.php`
   - `platform/resources/views/layouts/app.blade.php`
   - `platform/resources/views/layouts/navigation.blade.php`
   - `platform/public/build/manifest.json`
7. run:
   - `php artisan test`
   - `./vendor/bin/pint --dirty`
   - `npm run build`
8. continue only after verifying latest state

If deploying to staging:
1. use current `manifest.json`
2. upload the matching CSS asset
3. clear Laravel cache on server
4. verify in browser at `shoplinkstage.1web.sk`

---

## 15. Sync Rules

Required rule for future work:
- every important work session must update `docs/CURRENT_HANDOFF.md` when current access, deployment state, assets, or next steps change
- every important work session must update `docs/WORKSTATE.md`
- every important work session must append to `docs/SESSION_LOG.md`
- if resume behavior changes, update `docs/RESUME_PROTOCOL.md`

Canonical memory files:
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
- `docs/SESSION_LOG.md`
- `docs/RESUME_PROTOCOL.md`

Do not rely on chat memory alone.

---

## 16. Important Security / Practical Note

This file intentionally contains sensitive environment information because the explicit goal is seamless continuation between workstations.

If the repository is ever moved to a shared remote outside trusted control, these files must be cleaned or split immediately.

---

## 17. Latest Sales Detail State

Added Sales Detail page:
- route: `/sales/detail`
- route name: `sales.detail`
- controller method: `SalesIntelligenceController::detail`
- view: `resources/views/sales/detail.blade.php`

Purpose:
- product-level reporting from imported order lines
- decision-focused reports, not only sales totals

Current Sales Detail reports:
- KPI totals from `sales_order_lines`:
  - imported lines
  - sold pieces
  - net revenue EUR
  - purchase cost EUR
  - margin EUR and margin %
- decision list by product:
  - product/model
  - brand
  - sold quantity
  - revenue label shown as `Obrat`
  - margin
  - margin %
  - margin per piece
  - signal label
- product signals:
  - `Strata`
  - `Cena`
  - `Podporiť`
  - `Stabilné`
- best products to support
- high turnover / low margin products for price control
- category performance by margin
- marketplace product performance
- country product performance
- negative margin products section when present

Latest staging deploy for Sales Detail:
- historical asset names from the Sales Detail deploy are listed below; current global frontend assets are in the Current Snapshot at the top of this file
- `routes/web.php`
- `app/Http/Controllers/SalesIntelligenceController.php`
- `resources/views/sales/index.blade.php`
- `resources/views/sales/detail.blade.php`
- `resources/css/app.css`
- `public/build/manifest.json`
- `public/build/assets/app-gS35MAiP.css`
- `public/build/assets/app-BvRk9kiK.js`

Latest staging verification:
- route list shows `/sales/detail`
- CSS asset `/build/assets/app-gS35MAiP.css` returns HTTP 200
- server-side render check passed:
  - `sales-detail-render-ok`

Latest detail-list adjustment:
- category column was removed from the main product decision list
- category reporting remains as a separate aggregate section
- main table minimum width reduced to fit better on screen
- purchase cost column was removed from the main product decision list
- `Marža / ks` was added instead, because it is easier to interpret at a glance
- `Netto` label was renamed to `Obrat` in Sales Detail
- `Netto produktový obrat` label was simplified to `Produktový obrat` in Sales Overview
- `Margin leaders` block was replaced with `Najlepšie produkty na podporu`
- support candidates are filtered by volume, margin rate and margin per piece instead of only total margin
- forced asset refresh after styling issue:
  - historical CSS asset for that session was `public/build/assets/app-BVrlUcVj.css`
  - public URL `/build/assets/app-BVrlUcVj.css` returns HTTP 200
  - login HTML references `app-BVrlUcVj.css`

## 18. Latest Task Date Picker State

Final current status:
- task create and task edit use a provider-level TallStackUI dark date picker skin
- the solution lives in `platform/app/Providers/AppServiceProvider.php`
- Tailwind keeps provider-side classes because `platform/resources/css/app.css` contains `@source '../../app/Providers/*.php';`
- task create/edit Blade files no longer carry temporary date picker wrapper/floating/class overrides
- the previous CSS-only attempts with `sl-task-date-*` and `ts-date-reset-*` must be treated as obsolete
- stage was synced over SSH, Laravel caches were cleared after deploy, and both task date pickers were visually verified in browser
- later, the standalone edit page was replaced by the task index right-side modal; the provider-level date picker skin remains the shared date picker implementation

Files changed for the final fix:
- `platform/app/Providers/AppServiceProvider.php`
- `platform/resources/css/app.css`
- `platform/resources/views/tasks/index.blade.php`
- `platform/public/build/manifest.json`
- historical date picker CSS asset: `platform/public/build/assets/app-Die4Twu5.css`
- `platform/public/build/assets/app-BJMVC_oD.js`

Date picker staging verification from that fix:
- login HTML references `app-Die4Twu5.css`
- `/tasks/5/edit` date picker opens as a dark Shoplink panel
- `/tasks` create form date picker opens as the same dark Shoplink panel
- right side of the input is no longer white
- calendar popup is no longer the light TallStackUI/browser-default look
- `php artisan view:clear` passed
- `php artisan cache:clear` passed
- `php artisan config:clear` passed

Latest known built frontend asset:
- CSS: `public/build/assets/app-CsZzx5XQ.css`
- JS: `public/build/assets/app-BJMVC_oD.js`

## 19. ITHZ-MCP macOS fallback state

Current status:
- official macOS ITHZ-MCP package was downloaded from `https://ithz.dev/download/`
- runtime is installed outside the project at `/Users/peterzatyko/.local/share/ithz-mcp/runtime`
- local bootstrap file exists at `project.md`
- install log exists at `.ithz-install/install.log`
- Cursor MCP config exists at `.cursor/mcp.json`
- Claude Code MCP config exists at `.mcp.json`
- Codex MCP config was appended to `/Users/peterzatyko/.codex/config.toml`

Native-binary gap:
- no compatible macOS `ithz-native` was found via `ITHZ_NATIVE_EXE`
- no package-local `native/ithz-native` exists
- no `$HOME/.local/share/ithz-mcp/native/ithz-native` exists
- `project.ithz` was not created
- native archive/search/write memory remains deferred

Verification:
- runtime version check passed
- runtime self-test passed
- `.cursor/mcp.json` config validation passed
- `.mcp.json` config validation passed
- MCP client smoke started and passed read-only basics, including initialize, tools list, status, context pack, why_file, decision_log and gate_history
- MCP client smoke failed archive/search checks because native archive storage is unavailable

Security notes:
- legacy smoke generated `.ithz_mcp/index/index.json` with raw text, including documentation lines with staging credentials
- that generated legacy index was removed immediately
- `.ithz_mcp/` is now ignored in `.gitignore`
- do not run broad ITHZ legacy indexing/search on this project until a redacted allowlist is configured
- raw secrets, credentials, private keys, tokens, passwords and `.env` contents must not be imported into ITHZ memory

## 20. Task edit modal state

Current status:
- task edit no longer uses a standalone edit Blade view
- `GET /tasks/{task}/edit` now renders `tasks.index` with `selectedTask`
- the selected task opens in a right-side dark slide-over modal
- `/tasks` renders normally with `openEditId: null`
- clicking anywhere on a task row opens the edit slide-over with `openTaskEdit(id)`, except the status dropdown
- the list `Upraviť` link was removed
- header `Nový task` opens a right-side dark create slide-over through `task-create-open`
- the old bottom `Nová úloha` create block was removed
- `/tasks/{task}/edit` renders with `openEditId` set to the selected task id
- ESC closes the visible create/edit panel without navigating back
- backdrop click, X and cancel close the visible panel
- create/edit date picker forms keep a real hidden `name=due_at`
- on submit, the visible TallStack date input value is copied from `property=due_at_create_picker` or `property=due_at_picker_ID` into `name=due_at`
- this is required because the non-Livewire TallStack date component does not submit `property="due_at"` as a normal form field in this Blade-only context
- task due dates are displayed as date only with `format('d.m.Y')`
- edit status uses the same `sl-status-menu` and `sl-task-status-*` visual pattern as the task list, not a native select
- edit priority uses the same `sl-status-menu` pattern with `sl-priority-badge` / `sl-priority-*`
- edit category uses the same `sl-status-menu` pattern with `sl-category-badge` / `sl-category-tone-*`
- the task row is subtly highlighted when selected
- `platform/resources/views/tasks/edit.blade.php` was removed as an obsolete view

Files changed:
- `platform/app/Http/Controllers/TaskController.php`
- `platform/resources/views/tasks/index.blade.php`
- `platform/resources/views/tasks/edit.blade.php` deleted
- `platform/public/build/manifest.json`
- `platform/public/build/assets/app-CsZzx5XQ.css`

Staging verification:
- deploy uploaded controller, task index Blade, manifest and CSS asset
- staging Laravel caches cleared
- `php -l app/Http/Controllers/TaskController.php` passed on staging
- `php artisan route:list --name=tasks` shows all five task routes
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/tasks` HTML references `app-CsZzx5XQ.css`
- `/tasks` HTML contains `task-create-open`, `openCreate`, row-level `openTaskEdit(5)`, `data-task-row-ignore`, `keydown.escape.window` and `translate-x-full`
- `/tasks` HTML no longer contains `Nová úloha`, `quick-create` or list `>Upraviť<`
- `/tasks` HTML contains date submit copy selectors for `[property=due_at_create_picker]` and `[property=due_at_picker_5]`
- `/tasks` HTML contains date-only output such as `20.07.2026`, with no `0:00`
- `/tasks/5/edit` HTML contains `taskStatusSummary5`, `taskStatusLabel5` and `sl-task-status-*` classes for edit status
- `/tasks/5/edit` HTML contains `taskPrioritySummary5` and `taskCategorySummary5`
- `/tasks/5/edit` HTML contains `openEditId: 5` and `Upraviť task`
- obsolete remote `resources/views/tasks/edit.blade.php` was removed

Verification gap:
- local PHP tests could not run because `php` is not available in the local shell PATH
- frontend build passed with `npm run build`

## 21. TallStackUI date picker runtime fix

Current status:
- `Nový task` and edit task keep using TallStackUI `<x-ts-date>`, not native browser date inputs
- the date picker requires the separate TallStackUI date bundle import in `platform/resources/js/app.js`
- TallStackUI floating date popovers also require Alpine plugins `@alpinejs/anchor` and `@alpinejs/intersect`; without `anchor`, the calendar can open at the wrong page position such as `x:0, y:784` instead of below the input
- required imports are:
  - `../../vendor/tallstackui/tallstackui/dist/tallstackui-NrxCew1f.js`
  - `../../vendor/tallstackui/tallstackui/dist/tallstackui-date-CbEf0FZx.js`
  - `@alpinejs/anchor`
  - `@alpinejs/intersect`
- without the date bundle, browser console shows `ReferenceError: tallstackui_formDate is not defined`, and the picker does not open
- `Nový task` opening is handled by Alpine store plus a minimal click bridge for `[data-task-create-open]`; do not reintroduce manual `display:block` slide-over manipulation for the create panel
- date field styling has one visible inner border only; `.sl-field-shell-date > div` must stay borderless to avoid a double/thick border
- create/edit submit handlers copy `.sl-field-shell-date input[type="text"]` into the real hidden `name=due_at`; do not search for `input[name="due_at_create_picker"]`, because TallStackUI does not render that named input in this Blade-only usage

Files changed:
- `platform/resources/js/app.js`
- `platform/resources/css/app.css`
- `platform/resources/views/tasks/index.blade.php`
- `platform/package.json`
- `platform/package-lock.json`
- `platform/public/build/manifest.json`
- `platform/public/build/app-BC4X1lUJ.css`
- `platform/public/build/app-HtRUCpV8.js`

Verification:
- local browser test on `/tasks` confirmed `Nový task` opens
- local browser test confirmed clicking `Termín` opens a visible TallStackUI floating calendar
- local browser test confirmed date field wrapper border is `0px` outside and `1px` only on the inner input wrapper
- staging manifest references `app-BC4X1lUJ.css` and `app-HtRUCpV8.js`
- staging browser test logged in as Peter Zatyko confirmed `Nový task` opens
- staging browser test confirmed clicking create `Termín` opens the calendar below the input: input around `x=682,y=284`, floating calendar around `x=691,y=331`
- staging real create test created `CODEX DATE TEST 1785590314073` with selected due date `17.08.2026`
- the staging test task was deleted immediately after verification; final reload confirmed it was gone and active task count returned to `8`

## 2026-08-03 / Task Center restore snapshot

Current Task Center staging state:
- `platform/resources/views/tasks/index.blade.php` was redeployed after a Task Center regression
- task create/edit title and note use the shared dark panel field styling class `sl-panel-input`
- task create/edit owner uses a native dark `select` with the same panel field styling
- desktop task list status/date columns were widened so the date stays aligned in the row

Current staging assets:
- manifest CSS: `assets/app-CTI352Fu.css`
- manifest JS: `assets/app-DQ-IHDUq.js`

Deployment actions completed:
- uploaded `platform/resources/views/tasks/index.blade.php`
- uploaded `platform/public/build/manifest.json`
- uploaded `platform/public/build/assets/app-CTI352Fu.css`
- uploaded `platform/public/build/app-CTI352Fu.css`
- cleared staging caches with:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan config:clear`

Checks completed:
- local `npm run build` passed
- staging `public/build/manifest.json` points to `assets/app-CTI352Fu.css`
- staging file `public/build/assets/app-CTI352Fu.css` exists
- HTTP `https://shoplinkstage.1web.sk/build/manifest.json` returned `200`
- HTTP `https://shoplinkstage.1web.sk/build/assets/app-CTI352Fu.css` returned `200`

Verification gap:
- no final authenticated browser visual pass was completed in this session
- if Task Center still appears stale in-browser, first do a hard refresh on `/tasks`

## 2026-08-03 / Comments and dashboard window snapshot

Current state:
- task edit slide-over includes a comment thread
- users can add comments and edit their own comments inline via AJAX
- comments do not refresh or close the task panel
- users have a per-user dashboard setting `dashboard_task_window_days`
- Settings contains a form for that dashboard task window value
- dashboard task list shows upcoming due tasks within that window and overdue tasks

## 2026-09-28 / Latest Settings layout

- `Task okno a push` is displayed beside `Kategórie taskov`.
- Exchange-rate edit inputs are visible for all configured currencies.
- Deployed and browser-verified on staging `/settings`.
- Upper Settings also contains a separate add-category box and category delete buttons.
- Supplier settings headers are visible for supplier, URL link, target days, orderable feed, and supplier stock.

## 2026-09-28 / FDD stock feed

- FDD has separate orderable CSV and supplier-stock ZIP/CSV imports.
- FDD stock parser uses `id` and `ilość produktów`; staging contains the supplied 3,858-row feed.
- Product Stats now matches supplier stock for both FDD and Hurtel.
- Settings supports saved stock-feed URLs and one-click URL refresh for FDD and Hurtel, with count and last-update timestamp.
- Supplier Settings uses two-row cards; FDD orderable CSV now supports file and URL import with count and last import time.
- Supplier cards were compacted into left basic settings and right feed controls, with aligned controls and dark text.
- Feed controls now use side-by-side orderable/stock panels to reduce supplier card height.
- Removed responsive stacking from the desktop supplier card layout; left basics and right feed panels stay side-by-side.
- Supplier grid now uses inline fixed columns to prevent staging CSS from stacking the boxes.
- Left supplier basics column is wider; orderable and stock panels are stacked vertically on the right.
- Empty supplier cards are hidden by default; Settings has a show-all toggle. Current staging UI shows 15 hidden empty suppliers.
- Supplier typography was enlarged and supplier names now have a dark background badge.
- Exchange-rate section no longer uses a two-column layout beside suppliers; it is full width above them.
- Settings save buttons for rates and suppliers are content-width and right-aligned.
- Settings positive actions were unified to the dashboard primary button design.
- Supplier cards no longer use nested right-side boxes; feed sections are flat and divided by one line.

Product Stats latest:
- supplier, text-search, and S1/S2 stock filters are applied in the database query before item enrichment
- server-side pagination was removed; full filtered supplier tables are shown again
- the wide table uses local horizontal scrolling for the final columns

Current staging assets:
- CSS: `app--SNK03NE.css`
- JS: `app-CbJBg8UK.js`

Database changes applied on staging:
- `task_comments` table exists
- `users.dashboard_task_window_days` exists
## 2026-10-12

- `/product-stats/preparation` uses channel-aware warehouse planning: own web + non-Alza marketplace -> S1, Alza -> S2.
- Historical sales and recommendations are displayed separately as S1/Alza or S1/S2.
- Staging deploy verified: controller syntax clean, Blade cache rebuilt, authenticated page shows the new headers and split values.
- Current Alza listing availability is not checked automatically yet.
- Fixed Alza matching for the actual staging value `ALZA.CZ` by using the `ALZA%` prefix; browser verification now shows non-zero Alza S2 values.
