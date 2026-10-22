# SHOPLINK Intelligence - SESSION LOG

This file is append-only.

Purpose:
- preserve short chronological memory between workstations
- record what changed in each important work session
- prevent re-thinking already finished context

## Entry format

For each meaningful session, record:
- date
- environment used
- what changed
- what was deployed
- what remains open

---

## 2026-09-14 / Sales Intelligence Top 30 visual overview

Implemented locally:
- rolling last-7-days Top 30 product query ordered by sold quantity
- catalog image join, rank cards, sold pieces, order count, and comparison with previous 7 days
- modern responsive visual product grid in Sales Intelligence
- separate `/sales/top30` page with `Top 30` sidebar and header buttons

Verification:
- `npm run build` passed
- deployed to Shoplink staging: controller, Blade view, manifest, `app-CJSm_9LK.css`, and `app-D8T4qs8Q.js`
- `view:clear`, `cache:clear`, and `config:clear` completed
- PHP syntax passed; `/sales/top30` browser verification showed 30 image cards and real 7-day values

Follow-up correction:
- removed the embedded Top 30 grid from `/sales`
- removed `Top 30` from the left sidebar
- kept only the top-right `Top 30` header button
- browser verified `/sales` after deployment

Micro UI follow-up:
- lightened Top 30 product box background from `bg-slate-800/65` to `bg-slate-700/45`
- deployed only `_top30.blade.php`; view cache cleared and staging page rechecked

Analytics follow-up:
- added Kusy, Obrat, Marža and sales-based Potenciál modes
- added average EUR price/ks, margin rate, revenue and potential score to cards
- normalized product/catalog grouping to avoid duplicate model cards
- staging browser verified the Potenciál mode and affected model appears once

Period adjustment:
- changed Top 30 from rolling 7 days to rolling 30 days
- comparison now uses the preceding 30-day period
- staging browser verified `16.08. – 14.09.2026` and no 7-day label remains

---

## 2026-09-14 / Resume and Websupport audit

Environment:
- local Codex workspace
- staging: `shoplinkstage.1web.sk`

Verified:
- SSH works with the shared key `/Users/peterzatyko/.ssh/rma_websupport_ed25519` (historical filename); the verified remote target is Shoplink staging, with Laravel root, `.env`, PHP 8.5.10 and Laravel 13.23.0 available
- HTTPS, login redirect, current asset manifest, PWA endpoints and critical routes work
- all 21 remote migrations are marked `Ran`
- local `npm run build` passed; local PHP CLI is unavailable

Important findings:
- staging `.env` currently uses `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`, SQLite, and has no `WEB_PUSH_*` entries
- `/build/app-...` is the valid staging asset path; `/build/assets/app-...` returns `404`
- no Git repository is present in the project root

No staging changes or deployments were made.

---

## 2026-07-28 / Initial build + staging setup

Environment:
- local Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- Laravel app initialized in `platform/`
- Breeze auth added
- core models added: `Role`, `Task`, `Rma`, `ImportJob`
- Slovak localization added
- staging PHP/runtime issues diagnosed and resolved
- staging DB configured and migrations/seeding executed
- dark premium dashboard shell created
- dashboard iterated multiple times toward flatter premium internal app style
- sidebar app rail introduced
- top utility/status row introduced
- dashboard tabs introduced
- old large KPI cards removed
- KPI row converted into one compact KPI surface
- 4 standalone module cards replaced by one combined focus-modules surface
- focus-modules surface simplified further into:
  - one main highlighted module
  - one compact side list of modules
- decorative corner graphics removed
- large radii and strong borders reduced

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-BZ2Hy1sg.css`
- JS: `public/build/assets/app-B9qO1Jfl.js`

Open design direction:
- continue reducing “dashboard template” feeling
- move toward single-canvas premium product layout
- convert one visual area into a real function next

Recommended next feature:
- Task Center MVP

---

## 2026-07-28 / Task Center MVP + category system

Environment:
- local Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- Task Center implemented as first real workflow
- sidebar `Task Center` navigation linked to real route
- task list page added
- task create flow added
- task status update flow added
- task assignment to user added
- task filtering by status and category added
- dedicated task category model added
- custom category creation added directly from UI
- default Slovak task categories seeded

Domain additions:
- new `task_categories` table
- `tasks.task_category_id` added
- `Task -> category` relation added
- `User -> assignedTasks` relation added

Default seeded categories:
- `Všeobecné`
- `Objednávka`
- `Reklamácia`
- `Platba`
- `Fakturácia`
- `Stratený balík`

Deployment:
- Task Center controllers, models, routes, migrations, seeders, views and CSS uploaded to staging
- staging migrations executed successfully
- `TaskCategorySeeder` executed successfully
- compiled views cleared
- application cache cleared

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-CE19WWCY.css`
- JS: `public/build/assets/app-B9qO1Jfl.js`

Recommended next feature:
- connect tasks to real business events, starting with RMA

---

## 2026-07-28 / Light premium workspace redesign

## 2026-08-12 / Light UI cleanup for Users + Task edit

Environment:
- local Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- user edit activity and task email notification blocks switched to the shared light checkbox surface
- task edit delete button text changed to dark text for better contrast
- task edit note textarea, new comment textarea, and comment edit textarea aligned to standard `text-base` sizing

Deployment:
- updated Blade + CSS uploaded to staging
- rebuilt frontend assets uploaded
- `view:clear`, `cache:clear`, `config:clear` executed on staging

Latest built frontend asset at session end:
- CSS: `public/build/assets/app-Vhib-mwh.css`
- JS: `public/build/assets/app-Bqgkh8xy.js`

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- main application UI direction changed from dark premium to light premium daily workspace
- left rail kept, but the shell was redesigned into a slimmer white/glass navigation surface
- dashboard was rebuilt into a denser morning overview layout
- oversized visual boxes were removed from the main task workflow direction
- Task Center was redesigned into a compact row-based operational list
- quick-create task form and category management were kept, but compressed into side panels
- dashboard now shows task preview rows, AI brief, category load and regional pulse in a lighter layout

Design rules reinforced in this session:
- main daily UI should be light gray background with white surfaces
- more content must fit on screen at once
- tasks should use row logic, not inflated card logic
- color accents should communicate urgency and state
- dark premium monitor mode can be added later as a separate view, not the primary app shell

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-CbxC6JQN.css`
- JS: `public/build/assets/app-B9qO1Jfl.js`

Next recommended step:
- deploy this light workspace redesign to staging

---

## 2026-07-28 / Flat design refinement

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- removed large visual radii from the main workspace
- removed most decorative gradients from the daily UI shell
- flattened white panels, pills, buttons and row surfaces
- reduced shadow intensity
- kept the information-dense layout, but moved it closer to a stricter flat design system

Design rules reinforced in this session:
- no oversized rounded corners
- no decorative gradient backgrounds unless functionally justified
- prefer flat white surfaces on light gray workspace background
- emphasis should come from spacing, typography and state color, not from glossy effects

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-DMAFfe-S.css`
- JS: `public/build/assets/app-B9qO1Jfl.js`

---

## 2026-07-28 / Typography and UI system refinement

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- clarified that the design quality issue is not a Laravel limitation
- replaced the previous font setup with a cleaner UI typography direction
- reduced the admin-template feeling in the sidebar and main shell
- refined borders, spacing, label hierarchy and card density
- improved the sidebar structure with clearer workspace metadata

Design rules reinforced in this session:
- visual quality must come from a deliberate design system, not the framework
- cleaner font hierarchy is required across the shell
- borders should be subtle and consistent
- the UI should feel closer to a modern product workspace, not a default backend theme

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-xdWFixkf.css`
- JS: `public/build/assets/app-B9qO1Jfl.js`

---

## 2026-07-28 / Component-based UI reset

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- frontend stack migrated to Livewire 4 + TallStackUI 3 + Tailwind 4
- custom controls started being replaced by system UI components
- Task Center filters switched to TallStackUI select components
- row status switcher moved to TallStackUI select component
- task create form now uses TallStackUI date picker and system buttons
- default blue button styling removed in favor of neutral black/slate actions

Design rules reinforced in this session:
- use the UI system consistently, not only in isolated inputs
- primary actions should not default to blue
- text should stay high-contrast and readable
- operational task rows stay full-width; side panels must not steal task space

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-D7gghCoR.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-28 / TallStackUI checkbox + typography pass

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- Task Center received TallStackUI checkbox components in real use
- added `Iba moje tasky` checkbox filter in the task queue
- added `Priradiť mne` checkbox in task creation
- task filtering logic updated to support personal queue view
- main app font switched to `Manrope` for a cleaner product-style UI

Design rules reinforced in this session:
- use system components for micro-interactions too, not only selects and buttons
- checkbox accents should support the orange status language
- typography must feel more premium and more deliberate across the shell

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-B7Vrd-f3.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-28 / TallStackUI dark system pass

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- TallStackUI core components were customized globally through `AppServiceProvider`
- buttons, inputs, native selects, textarea, date picker, checkbox and dropdown surfaces now follow a black/graphite system
- floating panels and date popovers were converted to dark panels with orange accent states
- sidebar, dashboard shell and Task Center shell were moved from light gray to dark graphite UI
- accent logic shifted from blue toward orange for waiting / warning / action emphasis

Design rules reinforced in this session:
- do not style isolated controls differently from the system
- black / graphite surfaces should be the default shell direction
- orange is the primary accent for important operational states
- the component library must define the look, not ad-hoc local button styling

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-DDm9efRL.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

Main outcomes:
- replaced the oversized custom shell styling with a smaller reusable UI component layer
- rebuilt sidebar into a cleaner component-based workspace navigation
- rebuilt dashboard into flatter metric cards and operational panels
- rebuilt Task Center into a compact row-based list with a practical create panel
- removed the old decorative CSS-heavy dashboard structure from the current main screens
- aligned labels more closely with the real workflow language, including `Čaká` for blocked tasks

Design rules reinforced in this session:
- UI should be assembled from reusable components, not one-off decorated sections
- task work must stay row-first and information-dense
- typography should stay dark and readable, not washed-out gray
- the right side panel is acceptable only when it stays narrow and functionally justified

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-CnsitNiI.css`
- JS: `public/build/assets/app-D0FgSN_H.js`

Next recommended step:
- deploy this component-based UI reset to staging

---

## 2026-07-28 / Livewire + TallStackUI migration start

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- installed `Livewire 4.3.3`
- installed `TallStackUI 3.5.14`
- upgraded frontend stack to `Tailwind 4`
- migrated Vite/Tailwind/PostCSS setup to the Tailwind 4 pipeline
- enabled explicit `Livewire` + `TallStackUI` asset loading in the main layout
- configured TallStackUI component prefix to `ts-`
- rebuilt the first real screen (`Task Center`) with TallStackUI buttons, badges, inputs, selects and cards

Design/system direction reinforced in this session:
- stop hand-rolling every control
- use a real Laravel UI component system for forms and actions
- keep task list full-width
- keep text contrast dark and readable

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-D7gghCoR.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

Next recommended step:
- deploy the new Livewire + TallStackUI base to staging
- continue converting dashboard and navigation components to the same system

---

## 2026-07-29 / Task Center row spacing tightened

Environment:
- local Codex workspace
- staging target: `shoplinkstage.1web.sk`

Main outcomes:
- reduced vertical spacing between Task Center rows
- reduced row padding in the task list
- reduced spacing under task descriptions and mobile metadata
- rebuilt frontend assets after the compactness pass
- deployed the spacing tweak to staging
- cleared Laravel caches on staging after deploy

Important deployment correction in this session:
- the remote server runs Laravel directly from:
  - `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`
- there is no extra `/platform` directory remotely
- an initial upload attempt failed because it incorrectly targeted `/shoplinkstage/platform/...`
- corrected uploads then went to:
  - `resources/css/app.css`
  - `resources/views/tasks/index.blade.php`
  - `public/build/manifest.json`
  - `public/build/assets/app-CS76P4xo.css`
  - `public/build/assets/app-BvRk9kiK.js`

Latest local verification at this stage:
- `php artisan test` passed
- `./vendor/bin/pint --dirty` passed
- `npm run build` passed

Latest known built frontend asset at session end:
- CSS: `public/build/assets/app-CS76P4xo.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Coding status clarification

Environment:
- Codex workspace reading iCloud-synced project folder

Main outcomes:
- clarified in documentation that coding already started on 2026-07-28
- recorded that `platform/` is the active Laravel application root
- confirmed that the existing Laravel foundation, dashboard shell and Task Center MVP are current project state
- updated documentation wording so “documentation before development” now means every new module or major workflow must be documented before further implementation, not that existing implementation should be ignored

No code changes were made in this entry.

Next recommended step:
- continue from current codebase and documentation, likely with RMA front and RMA-generated tasks

## 2026-07-29 / User Management MVP

Environment:
- Codex workspace reading and writing the iCloud-synced project folder

Main outcomes:
- added `UserController`
- added user management routes under authenticated routes
- added `users.index` screen for listing users and editing the selected user
- added sidebar navigation entry `Používatelia`
- user edit now supports name, email, default role, assigned roles, active state and optional password change
- protected the current user from deactivating their own account through the edit form
- added `UserManagementTest` covering opening the page, updating profile/roles/status/password, and preserving password when the password fields are empty

Verification:
- `/opt/homebrew/bin/npm run build` passed
- latest built CSS asset: `public/build/assets/app-DiwkDYSo.css`
- latest built JS asset: `public/build/assets/app-BvRk9kiK.js`
- `php artisan test` was not run in this Codex context because `php` is not available in PATH
- `./vendor/bin/pint --dirty` was not run in this Codex context because it requires PHP

Next recommended step:
- run PHP tests and Pint in the PHP-capable environment
- then continue with RMA front and RMA-generated tasks

## 2026-07-29 / User Management staging deploy

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- deployed User Management MVP to staging
- uploaded `UserController.php`, `routes/web.php`, `resources/views/users/index.blade.php`, `resources/views/layouts/navigation.blade.php`
- uploaded `public/build/manifest.json`
- uploaded built assets `public/build/assets/app-DiwkDYSo.css` and `public/build/assets/app-BvRk9kiK.js`
- confirmed remote PHP and Laravel are available over SSH
- corrected current Mac SSH key path in WORKSTATE to `/Users/peterzatyko/.ssh/rma_websupport_ed25519`

Verification:
- remote `php -l app/Http/Controllers/UserController.php` passed
- remote `php artisan route:list --name=users` shows `users.index`, `users.edit`, `users.update`
- remote `php artisan view:clear` passed
- remote `php artisan cache:clear` passed
- HTTP login to staging test account passed
- HTTP GET `/users` after login returned the User Management page
- HTTP PATCH `/users/1` with unchanged test user values redirected successfully to `/users/1/edit`
- flash message `Používateľ bol aktualizovaný.` confirmed

Important note:
- `php artisan test` was intentionally not run on staging because current tests use `RefreshDatabase` and could affect staging data.

Next recommended step:
- continue with RMA front and RMA-generated tasks

## 2026-07-29 / User Management visibility fix

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- added a compact top navigation for narrow screens where the fixed left sidebar is hidden
- added `Používatelia` to the dashboard quick navigation row
- deployed updated `resources/views/layouts/navigation.blade.php` and `resources/views/dashboard.blade.php` to staging
- cleared staging view and application cache

Verification:
- HTTP dashboard after login contains `Používatelia` in 3 places
- direct `/users` route remains available and returns the User Management page

User-facing note:
- if an old dashboard is already open, hard refresh may be needed to see the new navigation.

## 2026-07-29 / Sales Intelligence Orders Overview MVP

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- added database tables `sales_channels`, `exchange_rates`, `sales_orders`
- added models `SalesChannel`, `ExchangeRate`, `SalesOrder`
- added lightweight XLSX reader for Mobdata order exports
- added `MobdataOrderImportService`
- added `SalesIntelligenceController`
- added `/sales` overview page
- added `/sales/import` XLSX upload endpoint
- added `/sales/rates` exchange-rate update endpoint
- added Sales Intelligence links to navigation and dashboard
- seeded sales channels including own web, `Mobilego.sk`, ALZA, MALL, Allegro and Kaufland mappings
- seeded exchange-rate rows for EUR, CZK, HUF, PLN and RON-style currencies, with EUR set to 1 and other rates left for manual setup

Business rules implemented:
- only orders with status `Vybavené` are imported as sales
- duplicate orders are skipped by `Kód objednávky`
- empty `Ext` means own web
- future `Mobilego.sk` Ext is own web
- marketplace classification comes from sales channel mapping, not from `Ext ID`
- product-level statistics are intentionally not calculated from this export because item quantities and item-level prices are missing

Deployment:
- uploaded code, migrations, seeders, routes and Blade views to staging
- ran remote PHP syntax checks successfully
- ran staging migrations successfully
- ran `SalesChannelSeeder` and `ExchangeRateSeeder` successfully
- cleared view and application cache

Import verification:
- imported `/Users/peterzatyko/Downloads/Export_Objednavok-2.xlsx` through the staging web endpoint
- import job completed with 20,294 imported rows, 0 skipped rows and 9,237 warnings
- warnings are expected because non-EUR currencies do not yet have rates set
- staging database count: 20,294 sales orders
- currency aggregates match the Excel inspection: EUR 11,057 / 299,040.49, CZK 7,678 / 5,414,403, HUF 1,251 / 15,742,200, PLN 244 / 19,985.56, RON 64 / 8,184.80
- HTTP `/sales` after login displays imported counts, ALZA.CZ, MALL and latest import file

Next recommended step:
- set exchange rates in the Sales Intelligence UI to enable total EUR revenue across CZK/HUF/PLN/RON
- then add filters/date range and later item-level product import when the detailed export is available

## 2026-07-29 / Sales Overview redesign and settings split

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- redesigned Sales Intelligence from wide table-heavy layout into compact managerial overview
- removed exchange-rate editing from the Sales dashboard
- added Settings screen for system exchange-rate configuration
- added `/settings` and `/settings/rates` routes
- added `SettingsController`
- Sales dashboard now includes KPI cards, donut chart for marketplace vs own web, monthly order bar chart, compact top marketplace list, compact country/portal list, currency cards and import log
- navigation now includes `Nastavenia`

Verification:
- remote PHP syntax checks passed for `SalesIntelligenceController` and `SettingsController`
- remote route list shows Sales and Settings routes
- staging cache and views cleared
- HTTP `/sales` after login contains `Orders Overview`, `conic-gradient`, `Mesačný vývoj objednávok`, `Top marketplace`, `ALZA.CZ` and `20 294`
- HTTP `/settings` after login contains `Nastavenia systému`, `Kurzy voči EUR` and `Uložiť a prepočítať objednávky`

Design reason:
- exchange rates are configuration, not daily sales analysis, so they belong in Settings
- country and marketplace tables should be compact and scannable, not stretched full-width

## 2026-07-29 / Sales Detail product reporting

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- added `/sales/detail` route
- added `SalesIntelligenceController::detail`
- created `resources/views/sales/detail.blade.php`
- added link from Sales Overview to Sales Detail
- built product-level reports from existing `sales_order_lines` and `product_catalog_items`

Report sections added:
- Product KPI strip: lines, pieces, net revenue, purchase cost, margin
- Decision list with product signal labels
- Top products by margin
- High turnover / low margin price-control list
- Category margin performance
- Marketplace product performance
- Country product performance
- Negative-margin section when such products exist

Verification:
- `./vendor/bin/pint --dirty` passed
- `php artisan test` passed after rerun
- `npm run build` passed
- local route list shows `sales.detail`
- staging route list shows `/sales/detail`
- staging CSS asset `app-C3P0XISl.css` returns HTTP 200
- staging server-side render check returned `sales-detail-render-ok`

Latest known built frontend asset:
- CSS: `public/build/assets/app-C3P0XISl.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Sales Detail table width cleanup

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- removed the category column from the main Sales Detail product decision list
- kept category performance as a separate aggregate report section
- reduced the main product table minimum width so it fits the screen better

Verification:
- `./vendor/bin/pint --dirty` passed
- `php artisan test` passed
- `npm run build` passed
- staging CSS asset `app-26dAxBkt.css` returns HTTP 200
- staging server-side render check returned `sales-detail-render-ok`

Latest known built frontend asset:
- CSS: `public/build/assets/app-26dAxBkt.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Sales Detail purchase column replaced

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- removed `Nákup` from the main product decision list
- added `Marža / ks`
- reduced the main product table minimum width again

Reason:
- aggregated purchase cost is hard to interpret directly in the list
- margin per piece is faster for product-level decision-making

Verification:
- `./vendor/bin/pint --dirty` passed
- `php artisan test` passed
- `npm run build` passed
- staging CSS asset `app-gS35MAiP.css` returns HTTP 200
- staging server-side render check returned `sales-detail-render-ok`

Latest known built frontend asset:
- CSS: `public/build/assets/app-gS35MAiP.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Sales Detail support candidates

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- replaced the duplicate `Margin leaders` block with `Najlepšie produkty na podporu`
- support candidates now require volume, healthy margin rate and positive margin per piece
- the block now shows margin, margin %, and margin per piece

Reason:
- total margin leaders repeated the main decision table
- support candidates better answer which products should be pushed commercially

Verification:
- `./vendor/bin/pint --dirty` passed
- `php artisan test` passed
- `npm run build` passed
- staging CSS asset `app-Kf39rgES.css` returns HTTP 200
- staging server-side render check returned `sales-detail-render-ok`

Latest known built frontend asset:
- CSS: `public/build/assets/app-Kf39rgES.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Forced CSS asset refresh after staging styling issue

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- forced a new Vite CSS hash by updating the internal build id in `resources/css/app.css`
- uploaded the new manifest and CSS asset with explicit target paths
- mirrored the asset to the hosting-required root `build/assets` path
- cleared staging caches

Verification:
- login HTML references `app-BVrlUcVj.css`
- `/build/assets/app-BVrlUcVj.css` returns HTTP 200 with `content-type: text/css`
- staging server-side render check returned `sales-detail-render-ok`

Latest known built frontend asset:
- CSS: `public/build/assets/app-BVrlUcVj.css`
- JS: `public/build/assets/app-BvRk9kiK.js`

## 2026-07-29 / Sales wording cleanup

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- renamed `Netto` to `Obrat` in the Sales Detail product table
- renamed `Netto obrat` to `Obrat` in the Sales Detail KPI strip
- renamed `Netto produktový obrat` to `Produktový obrat` in Sales Overview
- changed margin helper text from `% z netto obratu` to `% z obratu`

Verification:
- `./vendor/bin/pint --dirty` passed
- `php artisan test` passed
- staging cache cleared
- staging server-side render check returned `sales-detail-render-ok`

## 2026-07-31 / Task date picker staging sync

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- tightened the Task Center date picker styling so the create and edit forms use the same dark Shoplink skin
- uploaded the updated task views, shared CSS source, Vite manifest and built CSS asset over SSH
- cleared Laravel view, app and config caches on staging after sync

Verification:
- login HTML on staging references `app-hW-I_19C.css`
- `/build/assets/app-hW-I_19C.css` returns HTTP 200 with `content-type: text/css`
- latest known built CSS asset is `public/build/assets/app-hW-I_19C.css`

## 2026-07-31 / Task date picker inner wrapper fix

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- identified that the remaining white area came from the inner TallStack input wrapper and the teleported floating root, not the outer date shell
- tightened the CSS selectors to target the nested wrapper and the floating panel root directly
- rebuilt and redeployed the updated asset to staging

Verification:
- login HTML on staging references `app-BL_BdQeO.css`
- `/build/assets/app-BL_BdQeO.css` returns HTTP 200 with `content-type: text/css`
- latest known built CSS asset is `public/build/assets/app-BL_BdQeO.css`

## 2026-07-31 / Task date picker final TallStackUI customization

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- replaced the remaining task date picker CSS hacks with docs-aligned TallStackUI soft customization in `AppServiceProvider`
- added `@source '../../app/Providers/*.php';` so Tailwind keeps the provider-side class strings in the production build
- removed the temporary task view wrapper overrides and let the shared date picker skin come from the component layer
- synchronized the staging test login password back to the documented value so the browser check could be completed

Verification:
- logged into staging as `peter.zatyko@shoplink.sk`
- opened `/tasks/5/edit` and confirmed the date picker panel renders in the dark Shoplink skin
- opened `/tasks` and confirmed the task create date picker uses the same dark Shoplink skin
- latest known built CSS asset is `public/build/assets/app-Die4Twu5.css`

## 2026-07-31 / Continuation documentation cleanup

Environment:
- Codex workspace

Main outcomes:
- cleaned `docs/WORKSTATE.md` so it starts with the current 2026-07-31 operational snapshot
- added `docs/CURRENT_HANDOFF.md` as the fastest one-page continuation file for another machine
- removed contradictory current-state references to old date picker CSS attempts and old Vite assets from the active Workstate sections
- recorded the final date picker solution as provider-level TallStackUI soft customization in `AppServiceProvider`
- updated `docs/RESUME_PROTOCOL.md` with the exact SSH command pattern, staging app root, deploy path rules and asset verification checklist
- clarified that staging has no nested `platform/` directory and deploy paths are relative to the remote Laravel app root
- updated `docs/00_DOCUMENTATION_INDEX.md` so operational truth starts from `CURRENT_HANDOFF.md`, while strategic decisions still use the principles/vision order

Current canonical continuation facts:
- first file to open: `docs/CURRENT_HANDOFF.md`
- local app root: `platform/`
- staging URL: `https://shoplinkstage.1web.sk`
- staging login: `peter.zatyko@shoplink.sk` / `password`
- SSH: `ssh -i "/Users/peterzatyko/.ssh/rma_websupport_ed25519" -p 27498 uid118160@shell.r5.websupport.sk`
- staging app root: `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`
- latest CSS asset: `public/build/assets/app-Die4Twu5.css`
- latest JS asset: `public/build/assets/app-BJMVC_oD.js`

## 2026-07-31 / ITHZ-MCP macOS fallback install

Environment:
- Codex workspace
- current project root: `/Users/peterzatyko/Documents/web/Shoplink Intelligence`

Main outcomes:
- downloaded the official macOS ITHZ-MCP package from `https://ithz.dev/download/`
- unpacked the package outside the project under `/private/tmp`
- followed `install_ithz.md`
- installed the durable runtime outside the project at `/Users/peterzatyko/.local/share/ithz-mcp/runtime`
- created redacted project bootstrap `project.md`
- created `.ithz-install/install.log`
- installed Cursor MCP config at `.cursor/mcp.json`
- installed Claude Code MCP config at `.mcp.json`
- installed Codex MCP config in `/Users/peterzatyko/.codex/config.toml`
- did not run git commit

Native-binary gap:
- official macOS package did not include compatible `native/ithz-native`
- `ITHZ_NATIVE_EXE` was not set
- `$HOME/.local/share/ithz-mcp/native/ithz-native` was not present
- documented `install-project --apply --import-agent-history --agent-intake auto` stopped with `ithz-native.exe not found`
- per `install_ithz.md`, the MCP host fallback was installed with `legacy` storage instead of guessing native URLs
- `project.ithz` was not created

Verification:
- `python3 -m ithz_mcp version` passed
- `python3 -m ithz_mcp self-test --verbose` passed
- `.cursor/mcp.json` validation passed
- `.mcp.json` validation passed
- Codex config contains `ithz_mcp` and `ithz_mcp_write` entries pointing to the durable runtime
- MCP client smoke passed read-only basics, but failed archive/search checks because native archive storage is unavailable

Security correction:
- the legacy smoke check generated `.ithz_mcp/index/index.json` with raw text from project docs, including staging credential lines
- the generated legacy index files were deleted immediately
- `.ithz_mcp/` was added to `.gitignore`
- do not run broad ITHZ legacy index/search on this project until explicit redacted allowlisting is configured

## 2026-07-31 / Task edit modal deploy

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- changed task edit from a standalone page into a right-side dark slide-over modal opened from `/tasks`
- kept the existing `tasks.edit` route as the deep-link/fallback entry point
- `TaskController::edit()` now renders the task index with `selectedTask`
- selected task rows get a subtle dark highlight
- removed obsolete local `platform/resources/views/tasks/edit.blade.php`
- removed obsolete remote `resources/views/tasks/edit.blade.php`
- rebuilt frontend assets
- deployed controller, task index Blade, manifest and new CSS asset to staging

Verification:
- `npm run build` passed
- local PHP checks could not run because `php` is not available in the local shell PATH
- staging `php -l app/Http/Controllers/TaskController.php` passed
- staging task routes are present
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/tasks/5/edit` renders `openEdit: true`
- `/tasks/5/edit` contains `Task edit` and `Upraviť task`
- new staging CSS asset is `public/build/assets/app-DH3lmhIz.css`

## 2026-07-31 / Task edit slide-over polish

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- changed task edit opening from route navigation to client-side `openTaskEdit(id)`
- the edit panel now slides in from the right like the User Management `Nový používateľ` panel
- added ESC close behavior through `x-on:keydown.escape.window`
- backdrop click, X and cancel now share the same `closeTaskEdit()` behavior
- kept `/tasks/{task}/edit` as a deep-link fallback that opens the selected task modal on load

Verification:
- `npm run build` passed
- staging Laravel caches cleared
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/build/assets/app-Da4zos12.css` returned HTTP 200
- `/tasks` HTML contains `openTaskEdit(5)`, `closeTaskEdit()`, `keydown.escape.window` and `translate-x-full`
- `/tasks/5/edit` HTML contains `openEditId: 5`
- staging `php -l resources/views/tasks/index.blade.php` passed

## 2026-07-31 / Task Center create panel and title-click edit

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- changed `Nový task` from scroll-to-bottom behavior into a right-side create slide-over
- removed the old bottom `Nová úloha` quick-create card
- changed task editing so the task title opens the edit slide-over
- removed the list `Upraviť` link
- changed ESC behavior so it closes the visible create/edit panel without navigating back
- kept `/tasks/{task}/edit` as a deep-link fallback

Verification:
- `npm run build` passed
- staging Laravel caches cleared
- staging `php -l resources/views/tasks/index.blade.php` passed
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/build/assets/app-C-liLNwd.css` returned HTTP 200
- `/tasks` HTML contains `task-create-open`, `openCreate`, `openTaskEdit(5)` and `keydown.escape.window`
- `/tasks` HTML no longer contains `Nová úloha`, `quick-create` or list `>Upraviť<`
- `/tasks/5/edit` HTML contains `openEditId: 5`

## 2026-07-31 / Task row click and date submit fix

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- changed task rows so clicking anywhere on the row opens edit
- marked the status dropdown with `data-task-row-ignore` so status remains a separate dropdown interaction
- kept the task title as a normal deep-link fallback but removed the separate edit-button need
- fixed task create/edit date submission in the Blade-only TallStack date setup
- forms now keep a real hidden `name=due_at`
- on submit, the visible TallStack date input value is copied from `property=due_at_create_picker` or `property=due_at_picker_ID` into `name=due_at`

Why:
- TallStack date in this non-Livewire context renders the visible input with the passed `property` attribute, but it does not submit a normal `name=due_at` field
- relying on multiple same-property date components made edit dates stay empty and tasks remained `Bez termínu`

Verification:
- `npm run build` passed
- staging Laravel caches cleared
- staging `php -l resources/views/tasks/index.blade.php` passed
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/build/assets/app-CsZzx5XQ.css` returned HTTP 200
- `/tasks` HTML contains row-level `openTaskEdit(5)`, `data-task-row-ignore`, `[property=due_at_create_picker]` and `[property=due_at_picker_5]`
- `/tasks` HTML no longer contains list `>Upraviť<`

## 2026-07-31 / Task date display and edit status styling

Environment:
- Codex workspace
- staging: `shoplinkstage.1web.sk`

Main outcomes:
- changed task due date display from `d.m.Y H:i` to `d.m.Y`
- edit modal status control now uses the same `sl-status-menu` / `sl-task-status-*` dropdown visual pattern as the task list
- removed the native TallStack select for edit status
- edit status still submits through a hidden `name=status` field

Verification:
- `npm run build` passed
- staging Laravel caches cleared
- staging `php -l resources/views/tasks/index.blade.php` passed
- `/tasks` returned HTTP 200
- `/tasks/5/edit` returned HTTP 200
- `/tasks` HTML contains date-only output such as `20.07.2026`
- `/tasks` HTML contains no `0:00`
- `/tasks/5/edit` HTML contains `taskStatusSummary5`, `taskStatusLabel5` and `sl-task-status-*`

## 2026-07-31 / Task edit priority and category styling

Main outcomes:
- edit priority now uses the same Shoplink dropdown pattern with priority badge classes
- edit category now uses the same Shoplink dropdown pattern with category badge classes
- native edit selects for priority/category were replaced by hidden submit fields plus visual dropdowns

Verification:
- `npm run build` passed
- staging caches cleared
- staging Blade lint passed
- `/tasks/5/edit` returned HTTP 200
- `/tasks/5/edit` HTML contains `taskPrioritySummary5` and `taskCategorySummary5`
- `/tasks/5/edit` HTML contains no native edit priority/category select

## 2026-07-31 / Task list category grouping

Main outcomes:
- Task Center list now groups tasks under category headers such as `Všeobecné`, `Objednávka`, and `Platba`
- category headers follow task category `sort_order` / `name` order
- empty categories are not shown
- uncategorized tasks fall back to `Bez kategórie`
- the repeated category column was removed from task rows for a cleaner list
- row click editing and the isolated status dropdown behavior were preserved
- category separators were refined into compact dark gradient bars with a small accent dot and task counter

Verification:
- `npm run build` passed
- staging caches cleared
- staging Blade lint passed
- `/tasks` returned HTTP 200
- `/build/assets/app-BxP6UoMe.css` returned HTTP 200
- `/tasks` HTML references `app-BxP6UoMe.css`
- `/tasks` HTML contains category headers and row-level `openTaskEdit(id)`
- `/tasks` was visually checked in the in-app browser

## 2026-07-31 / Settings task category management

Main outcomes:
- Settings now contains a `Kategórie taskov` section
- existing categories can be edited for name, description, `sort_order`, and active state
- new categories can be created from Settings with an explicit sort order
- Task list grouping uses all categories for stable historical visibility
- task create/filter category dropdowns still show active categories only
- category rows can now be reordered by dragging the left handle
- drag reorder saves through `PATCH /task-categories/reorder`
- edit row numeric order fields are hidden and kept only as technical fallback
- category editor row borders and TallStack input outlines were darkened to better match the Shoplink UI

Verification:
- `npm run build` passed
- staging caches cleared
- staging PHP lint passed for controllers, routes, and affected Blade files
- `/settings` returned HTTP 200
- `/settings` HTML references `app-p5pbUjlh.css`
- `/settings` HTML contains 6 category update forms, 6 drag handles, 1 category create form, and reorder wiring
- `PATCH /task-categories/reorder` returned `200 {"status":"ok"}` with the current stage category order

## 2026-07-31 / Settings category editor flat fields

Main outcomes:
- removed the remaining white/light TallStack wrapper ring from Settings category editor inputs
- category editor rows are flat dark surfaces without inset/white card framing
- `.ts-field-reset` remains the canonical Shoplink reset hook for TallStack form fields
- TallStack wrapper is reset via `div:has(> .ts-field-reset)` and `.relative:has(.ts-field-reset) > div`

Verification:
- `npm run build` passed
- staging caches cleared
- `/settings` returned HTTP 200
- `/build/assets/app-W4Rbj7aG.css` returned HTTP 200
- browser computed style showed input border transparent, wrapper background transparent, wrapper shadow none
- screenshot visually confirmed flat dark category inputs without white wrapper/ring

## 2026-07-31 / Settings category editor label simplification

Main outcomes:
- removed repeated visible `Názov`, `Popis`, and `Aktívna` labels from every category row
- added one compact desktop header above the category rows
- kept row labels as `sr-only` for accessibility
- category editor is more compact and less repetitive

Verification:
- `npm run build` passed
- staging caches cleared
- staging Blade lint passed
- `/settings` returned HTTP 200
- `/build/assets/app-C-vagXGD.css` returned HTTP 200
- browser screenshot confirmed one header row and clean category rows without repeated visible labels

## 2026-07-31 / Task create panel open fix

Problem:
- On staging `/tasks`, clicking `Nový task` did not open the create panel.

Cause:
- Browser inspection showed `window.Alpine` was not globally available on `/tasks`, so relying only on Alpine `x-on` / `x-show` was not safe.
- After the first fix, the outer panel opened but Alpine had already written `display: none` on the inner `aside`, so the fallback also needed to open inner `x-show="openCreate"` parts.

Main outcomes:
- Replaced the header TallStack `Nový task` component with a native Shoplink button.
- Added `data-task-create-open`, `data-task-create-panel`, and `data-task-create-close` hooks.
- Added a vanilla JS fallback in `resources/js/app.js`.
- The fallback opens/closes the outer panel and all inner `[x-show="openCreate"]` parts.
- ESC closes the create panel through the fallback.

Verification:
- `npm run build` passed.
- staging caches cleared.
- staging Blade lint passed for `resources/views/tasks/index.blade.php`.
- `/tasks` loaded JS asset `app-BUmIwnKK.js`.
- browser click test confirmed `Nový task` opens the right-side create panel.
- computed state after click: panel `display: block`, aside `display: block`, body locked.

## 2026-07-31 / Task create/edit panel unification fix

Problem:
- The create panel opened differently from edit and did not visually match the edit panel.
- The first fallback made the panel appear, but did not restore the full slide-over behavior consistently.

Main outcomes:
- Restored Alpine in `resources/js/app.js`, with a no-op `$wire` magic for TallStack Blade-only usage.
- Kept stable vanilla panel fallbacks because Task Center panel open/close must not depend only on Alpine.
- Create panel uses `data-task-create-open`, `data-task-create-panel`, `data-task-create-overlay`, `data-task-create-sheet`, and `data-task-create-close`.
- Edit panels use `data-task-edit-open`, `data-task-edit-panel`, `data-task-edit-overlay`, `data-task-edit-sheet`, and `data-task-edit-close`.
- Create category and priority controls now match edit: Shoplink `sl-status-menu` badge dropdowns, not native TallStack selects.

Verification:
- `npm run build` passed.
- staging caches cleared.
- staging Blade lint passed for `resources/views/tasks/index.blade.php`.
- `/tasks` loaded JS asset `app-jZpbGTZ3.js`.
- create click test: panel `block`, sheet `block`, transform at `translateX(0)`, no console errors.
- create panel has no native category/priority selects.
- edit title click test: panel `block`, sheet `block`, priority/category badge menus present.

## 2026-08-02 / Dashboard compaction

Environment:
- local Codex workspace
- staging `shoplinkstage.1web.sk`

Main outcomes:
- Removed dashboard header status pills `Dnes`, `Blokované`, `Bez termínu`.
- Removed top dashboard metric cards for open tasks, active RMA cases, and today's imports.
- Removed Today subtitle `Úlohy zoradené na dnešné spracovanie`.
- Reworked dashboard Today task rows into a compact one-line row: task title, status, optional due date.

Verification:
- `php -l` passed for `DashboardController.php` and `dashboard.blade.php`.
- `npm run build` passed.
- Deployed dashboard Blade, dashboard controller, manifest, CSS asset `app-Oa95rcDM.css`, and JS asset `app-DQ-IHDUq.js`.
- Staging caches cleared.
- `/build/manifest.json` references `app-Oa95rcDM.css`.
- `/build/app-Oa95rcDM.css` returns HTTP 200.
- Browser login did not complete in the in-app browser during this session, so final visual verification was limited to asset/manifest HTTP checks and deployed source state.

## 2026-08-03 / Task Center emergency dark-field restore

Problem:
- Task Center regressed again on staging.
- In the task list, the due date dropped visually lower instead of staying aligned in the row.
- In create/edit, `Názov tasku` showed a white background and `Owner` lost its visible dark border, so the panel no longer matched the Shoplink dark UI.

Main outcomes:
- widened the Task Center desktop grid from `124px / 92px` status-date columns to `136px / 108px` so the date keeps its own aligned column
- added `self-center whitespace-nowrap` to the desktop due-date cell
- introduced a dedicated dark panel field class for Task Center forms:
  - `.sl-task-panel .sl-panel-input`
  - `.sl-task-panel .sl-panel-select`
- applied that shared field class to:
  - create title
  - create note
  - edit title
  - edit note
- replaced the broken `x-ts-select.native` owner field in both create/edit panels with a native `<select>` using the same Shoplink dark input styling
- rebuilt frontend assets locally with `npm run build`

Files changed:
- `platform/resources/views/tasks/index.blade.php`
- `platform/resources/css/app.css`
- `platform/public/build/manifest.json`
- `platform/public/build/assets/app-CTI352Fu.css`

Staging deploy:
- uploaded `resources/views/tasks/index.blade.php`
- uploaded `public/build/manifest.json`
- uploaded `public/build/assets/app-CTI352Fu.css`
- uploaded `public/build/app-CTI352Fu.css`
- cleared staging Laravel caches:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan config:clear`

Verification:
- local `npm run build` passed
- staging server `public/build/manifest.json` now references `assets/app-CTI352Fu.css`
- staging server file `public/build/assets/app-CTI352Fu.css` exists
- HTTP `https://shoplinkstage.1web.sk/build/manifest.json` returned `200`
- HTTP `https://shoplinkstage.1web.sk/build/assets/app-CTI352Fu.css` returned `200`

Verification gap:
- no final authenticated browser screenshot/run was completed in this session, so if stale CSS still appears in the browser, force refresh `/tasks` first and recheck

## 2026-08-03 / Task comments + dashboard task window

Main outcomes:
- added a new `task_comments` table with `task_id`, `user_id`, `body`, timestamps
- added `TaskComment` model and `Task::comments()` / `User::taskComments()` relations
- added task comment routes:
  - `POST /tasks/{task}/comments`
  - `PATCH /tasks/{task}/comments/{comment}`
- task comments render inside each task edit slide-over
- comment create and own-comment edit work over AJAX without refreshing or closing the task panel
- added `dashboard_task_window_days` to users
- added Settings form for `dashboard_task_window_days`
- dashboard now shows due tasks within the configured next-X-days window plus overdue tasks
- dashboard task rows keep Task Center-like single-row typography, owner initials circle, Task Center-like status badge width, and flat due-date backgrounds

Files changed:
- `platform/app/Http/Controllers/TaskController.php`
- `platform/app/Http/Controllers/DashboardController.php`
- `platform/app/Http/Controllers/SettingsController.php`
- `platform/app/Models/Task.php`
- `platform/app/Models/User.php`
- `platform/app/Models/TaskComment.php`
- `platform/database/migrations/2026_08_03_120000_create_task_comments_table.php`
- `platform/database/migrations/2026_08_03_120100_add_dashboard_task_window_days_to_users_table.php`
- `platform/resources/views/tasks/index.blade.php`
- `platform/resources/views/tasks/partials/comments-list.blade.php`
- `platform/resources/views/tasks/partials/comment-thread.blade.php`
- `platform/resources/views/dashboard.blade.php`
- `platform/resources/views/settings/index.blade.php`
- `platform/resources/css/app.css`
- `platform/resources/js/app.js`
- `platform/routes/web.php`
- `platform/public/build/manifest.json`
- `platform/public/build/assets/app--SNK03NE.css`
- `platform/public/build/assets/app-CbJBg8UK.js`

Staging verification:
- `npm run build` passed
- PHP syntax checks passed for changed controllers/models/routes
- staging migrations ran successfully
- staging route list shows `tasks.comments.store`, `tasks.comments.update`, `settings.task-center.update`
- staging caches cleared
- `/login` HTML references `app--SNK03NE.css` and `app-CbJBg8UK.js`

## 2026-08-11 - Users module access lockdown

Summary:
- restricted the full Users module to admins only
- hid `Používatelia` navigation for non-admin users
- kept self-management outside this module via `/profile`

Files changed:
- `platform/app/Http/Controllers/UserController.php`
- `platform/app/Models/User.php`
- `platform/resources/views/layouts/navigation.blade.php`
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
- `docs/SESSION_LOG.md`

Staging verification:
- uploaded 3 changed app files to staging
- `php -l` passed on staging for `UserController.php` and `User.php`
- staging caches cleared:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan config:clear`
- visual non-admin browser verification not completed in this session

## 2026-08-12 - Sales Intelligence purchase recommendation planning

Summary:
- reviewed two XLSX exports for a future Sales Intelligence reorder recommendation module
- no application code changed

Files reviewed:
- `/Users/peterzatyko/Downloads/Sumarizacia_predaja.xlsx`
- `/Users/peterzatyko/Downloads/predaj.xlsx`

Findings:
- `Sumarizacia_predaja.xlsx` has product, supplier, stock, selected-period sales, clearance flag, purchase cost, last sale, and product age, but not `P30` or `POP`
- `predaj.xlsx` contains the stronger structure for this module: `Pre`, `P30`, `POP`, `S1`, `S2`, `DOD`, `Dod.kód.`, `NC`, `LastOrder`, `Dní`, `Vypr`
- confirmed later on 2026-08-12: `predaj.xlsx` has `1` sheet (`Sheet1`), `2897` rows, `19` columns, and begins directly with product data rather than a readable header row

Proposed module:
- Sales Intelligence section for reorder recommendations
- answers which products to reorder, from which supplier, and approximate quantity
- main formula direction: stock coverage days from `S1 + S2` and sales velocity from `P30 / 30`
- clearance products (`Vypr`) should not be recommended for reorder
- recommended groups: `Objednať teraz`, `Čoskoro dôjde`, `Dostatok skladu`, `Výpredaj - nekupovať`, `Bez predaja`

Next step:
- request improved export from programmer with `P30`, `P90`, `POP`, selected period metadata, supplier lead time if available, reserved stock, and incoming/on-order quantity if available

## 2026-08-12 - Product Stats scaffold implemented

Summary:
- created the first admin-only `Product Stats` module scaffold
- added XLSX import for `predaj.xlsx`-style files
- added supplier-grouped overview page

Files changed:
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/app/Models/ProductStatImport.php`
- `platform/app/Models/ProductStatItem.php`
- `platform/app/Services/ProductStatsImportService.php`
- `platform/database/migrations/2026_08_12_090000_create_product_stat_imports_table.php`
- `platform/database/migrations/2026_08_12_090100_create_product_stat_items_table.php`
- `platform/resources/views/product-stats/index.blade.php`
- `platform/routes/web.php`
- `platform/resources/views/layouts/navigation.blade.php`
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
- `docs/SESSION_LOG.md`

Staging verification:
- PHP syntax checks passed for new controller, service, models, and routes
- staging migrations ran successfully
- staging caches cleared
- visual browser verification was started for `/product-stats`, but the module still needs a proper import test with a real XLSX upload

## 2026-08-12 - Product Stats parser and incoming workflow expansion

Summary:
- expanded Product Stats from initial scaffold to the current working admin-only purchasing view
- adapted import logic to the newer summary XLS schema
- added incoming FDD CSV import and integrated it into reorder math
- recorded the continuation state for another workstation

Files changed:
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/app/Models/ProductStatImport.php`
- `platform/app/Models/ProductStatItem.php`
- `platform/app/Models/ProductStatIncomingItem.php`
- `platform/app/Services/ProductStatsImportService.php`
- `platform/app/Services/ProductStatsIncomingImportService.php`
- `platform/resources/views/product-stats/index.blade.php`
- `platform/routes/web.php`
- `platform/database/migrations/2026_08_12_130000_create_product_stat_incoming_items_table.php`
- `platform/database/migrations/2026_08_12_140000_add_period_to_product_stat_imports_table.php`
- `platform/database/migrations/2026_08_12_150000_add_stock_code_to_product_stat_items_table.php`
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
- `docs/SESSION_LOG.md`

Business/parsing decisions locked in:
- new XLS uses real headers; parser must map by header names, not fixed indexes
- `Kód` = SKU
- `Skladový kód` = stock code to display in UI
- `Dodávateľ` is preferred supplier source; fallback legacy source is `DOD`
- `Predaj` or `Pre` = selected-period sales, shown in UI as `Pre`
- `NC bez DPH (€)` / `NC bez DPH` / `NC` = purchase cost
- `Výpredaj` / `Vypr`: `1` means clearance, `0` or blank means normal
- supplier parsing must preserve text suppliers that contain digits, e.g. `R2invest`
- purely numeric fragments from supplier source must not become standalone suppliers

Current module state:
- summary XLSX import supports `Od` and `Do`
- import metadata stores selected period
- main table includes `Na ceste`
- current incoming supplier adapter: `FDD`
- FDD incoming import uses CSV `EAN + quantity`
- FDD incoming data can be cleared from UI
- reorder recommendation uses available incoming quantity so already ordered stock is not double-counted
- `Prioritné` section is toggleable and shows compact supplier/product tables
- priority top products increased to 10

Staging verification:
- staging migrations executed:
  - `2026_08_12_130000_create_product_stat_incoming_items_table`
  - `2026_08_12_140000_add_period_to_product_stat_imports_table`
  - `2026_08_12_150000_add_stock_code_to_product_stat_items_table`
- stage import error `no column named stock_code` was fixed by deploying and running the missing migration
- repeated staging cache clear completed after deploys
- module was visually used on `https://shoplinkstage.1web.sk/product-stats`

Continuation note:
- next continuation should start from current docs plus the current `product-stats` Blade/controller/service files, not from the older `predaj.xlsx` planning notes

## 2026-08-13 - User-specific iPhone PWA Web Push base

Summary:
- implemented the first full PWA/Web Push base for SHOPLINK Intelligence
- subscriptions are user-specific, not broadcast
- first real push event is new assigned task

Files changed:
- `platform/app/Http/Controllers/PushSubscriptionController.php`
- `platform/app/Http/Controllers/SettingsController.php`
- `platform/app/Http/Controllers/TaskController.php`
- `platform/app/Models/PushSubscription.php`
- `platform/app/Models/User.php`
- `platform/app/Services/WebPushService.php`
- `platform/config/services.php`
- `platform/routes/web.php`
- `platform/resources/js/app.js`
- `platform/resources/views/layouts/app.blade.php`
- `platform/resources/views/layouts/guest.blade.php`
- `platform/resources/views/settings/index.blade.php`
- `platform/resources/views/pwa/sw.blade.php`
- `platform/public/icons/shoplink-icon-192.svg`
- `platform/public/icons/shoplink-icon-512.svg`
- `platform/public/icons/shoplink-badge.svg`
- `platform/database/migrations/2026_08_13_110000_create_push_subscriptions_table.php`
- `platform/database/migrations/2026_08_13_110100_add_web_push_task_setting_to_users_table.php`
- `platform/composer.json`
- `platform/composer.lock`
- `docs/CURRENT_HANDOFF.md`
- `docs/WORKSTATE.md`
- `docs/SESSION_LOG.md`

Implementation details:
- added `manifest.webmanifest`
- added `sw.js`
- registered service worker in app JS
- added settings UI block for `iPhone / PWA push`
- added subscribe/unsubscribe endpoints:
  - `push-subscriptions.store`
  - `push-subscriptions.destroy`
- added `push_subscriptions` table for per-user device endpoints
- added `users.web_push_notif_new_task`
- new tasks now trigger Web Push only to the assigned user if their push setting is enabled

Stage runtime actions:
- uploaded all feature files and rebuilt assets
- installed package `minishlink/web-push:^9.0`
- generated VAPID keys on stage
- set stage env:
  - `WEB_PUSH_PUBLIC_KEY`
  - `WEB_PUSH_PRIVATE_KEY`
  - `WEB_PUSH_SUBJECT=mailto:task@shoplink.sk`
- ran migrations:
  - `2026_08_13_110000_create_push_subscriptions_table`
  - `2026_08_13_110100_add_web_push_task_setting_to_users_table`
- cleared caches:
  - `php artisan view:clear`
  - `php artisan cache:clear`
  - `php artisan config:clear`

Verification:
- `manifest.webmanifest` returns HTTP `200`
- `sw.js` returns HTTP `200`
- stage route list contains:
  - `manifest.webmanifest`
  - `sw.js`
  - `POST push/subscriptions`
  - `DELETE push/subscriptions`
- PHP syntax checks passed on stage for:
  - `PushSubscriptionController.php`
  - `WebPushService.php`
  - `TaskController.php`
  - `SettingsController.php`
  - `routes/web.php`

Verification gap:
- real iPhone delivery still needs a live device test from a Home Screen installed PWA with granted notification permission

## 2026-08-13 / Task Center snapshot sync

Scope:
- keep Task Center list and edit panel synchronized without manual refresh
- reflect remote/mobile task changes on already open `/tasks`

Changed files:
- `platform/app/Http/Controllers/TaskController.php`
- `platform/routes/web.php`
- `platform/resources/views/tasks/index.blade.php`
- `platform/resources/js/app.js`
- `platform/public/build/manifest.json`

Implementation:
- added `GET /tasks/snapshot`
- returns lightweight per-task snapshot for visible task ids
- added DOM hooks for task title, status, due date, and comments in list + edit panel
- frontend polls every `15s`, skips hidden tab, refreshes on visibility return

Stage runtime actions:
- rebuilt assets:
  - `public/build/assets/app-xalE3_95.css`
  - `public/build/assets/app-CH0aJJYF.js`
- uploaded app files and build assets
- ran:
  - `php artisan view:clear`
  - `php artisan cache:clear`

Verification:
- `php artisan route:list` contains `tasks/snapshot`
- `https://shoplinkstage.1web.sk/build/assets/app-xalE3_95.css` returns `200`
- `https://shoplinkstage.1web.sk/build/assets/app-CH0aJJYF.js` returns `200`
- unauthenticated `https://shoplinkstage.1web.sk/tasks/snapshot` redirects to `/login`

Verification gap:
- live authenticated browser behavior still needs click-check with open `/tasks`

## 2026-08-13 / User admin edit visibility + push setting

Scope:
- fix `/users/{id}/edit` admin form fields that were hard to see after the light UI transition
- allow admin to edit the user's `web_push_notif_new_task` preference from user administration

Changed files:
- `platform/app/Http/Controllers/UserController.php`
- `platform/resources/views/users/index.blade.php`

Implementation:
- user create/update now validates and saves `web_push_notif_new_task`
- edit/create user forms now show `Push pri novom tasku`
- role and notification blocks use light readable text/background classes

Stage runtime actions:
- uploaded changed files to staging
- ran `php -l` on `UserController.php`
- ran `php artisan view:clear`
- ran `php artisan cache:clear`

Verification:
- staging controller contains `web_push_notif_new_task` validation/save
- staging Blade contains role, email notification, and push notification fields
- `GET /users/1/edit` redirects to `/login` when unauthenticated, so route/render is not throwing a public 500

Follow-up:
- root cause was visual, not data: authenticated HTML already rendered `checked`, but checkbox UI was too weak
- added shared `.sl-admin-checkbox` with stronger visible checked state
- rebuilt/deployed assets:
  - `public/build/assets/app-BxSAR8cE.css`
  - `public/build/assets/app-3mbFW2C7.js`
- authenticated HTML check confirms `roles[]`, `email_notif_new_task`, `email_notif_overdue_daily`, and `web_push_notif_new_task` render as `checked` with `sl-admin-checkbox`

## 2026-08-13 / Email pri novej poznámke

Scope:
- add missing user setting for task comment/note email notifications

Changed files:
- `platform/app/Models/User.php`
- `platform/app/Http/Controllers/UserController.php`
- `platform/app/Http/Controllers/TaskController.php`
- `platform/app/Notifications/NewTaskCommentNotification.php`
- `platform/database/migrations/2026_08_13_130000_add_task_comment_email_setting_to_users_table.php`
- `platform/resources/views/users/index.blade.php`

Implementation:
- added `users.email_notif_task_comment`, default `true`
- user admin create/edit now shows `Email pri novej poznámke`
- new task comment sends email to assigned user and task creator, excluding the comment author and duplicates

Stage:
- files uploaded
- migration ran successfully
- view/cache cleared
- authenticated `/users/1/edit` HTML confirms `email_notif_task_comment` is checked

## 2026-08-16 / Product Stats supplier ORDER sorting

Scope:
- when Product Stats is filtered by supplier, keep products sorted by `ORDER` / `last_order_at` newest first

Changed files:
- `platform/app/Http/Controllers/ProductStatController.php`

Implementation:
- supplier group items now sort by `last_order_at` descending when a supplier filter is active
- priority-only mode still limits to top 20, supplier filter does not

Stage:
- controller uploaded
- PHP syntax OK
- cache cleared
- authenticated Leidi check showed first ORDER dates descending: `10.08.2026`, `10.08.2026`, `08.08.2026`, `06.08.2026`

Follow-up:
- supplier-filter sorting changed to purchase priority:
  - `Objednať`
  - `Dopyt`
  - recommended quantity descending
  - S1 empty first
  - then ORDER date newest first
- authenticated Leidi check starts with `Objednať`, `Objednať`, then `Dopyt`

## 2026-08-16 / Product Stats continue row marker

Scope:
- help continue supplier/product checking after opening another tab or supplier site

Changed files:
- `platform/resources/views/product-stats/index.blade.php`

Implementation:
- clicking any Product Stats row stores the active row in browser `localStorage`
- active row is highlighted with a subtle orange background and rose left marker
- filter bar has `Pokračovať` button which scrolls back to the saved row

Stage:
- Blade uploaded
- view cache cleared
- authenticated Leidi HTML confirms `Pokračovať`, row markers, and script are rendered

## 2026-08-25 / Product Stats preparation report

Scope:
- add first Sales Intelligence powered preparation/forecast report inside Product Stats

Changed files:
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/resources/views/product-stats/index.blade.php`

Implementation:
- Product Stats now links current product SKUs to `sales_order_lines.model`
- one grouped query calculates:
  - `S90`
  - `S180`
  - previous-year `Q4`
  - last sale date
- report computes `Forecast`, recommended quantity, risk score and reason
- report shows top 30 products needing preparation/order attention

Stage:
- files uploaded
- PHP syntax OK
- view/cache cleared
- authenticated `/product-stats` render check passed
- report rendered with rows such as `TEC376162`, `TEC691609`, `TEC413663`

## 2026-08-20 / Product Stats supplier settings

Scope:
- centralize supplier portal links and target order days

Changed files:
- `platform/app/Models/ProductStatSupplierSetting.php`
- `platform/database/migrations/2026_08_20_120000_create_product_stat_supplier_settings_table.php`
- `platform/app/Http/Controllers/SettingsController.php`
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/resources/views/settings/index.blade.php`
- `platform/routes/web.php`

Implementation:
- Settings now has editable Product Stats supplier rows with link template and `Cieľ dní`
- Product Stats supplier portal URLs use the DB template with `{code}`
- Product Stats recalculates `OBJ` per supplier using `target_coverage_days`

Stage:
- files uploaded to `shoplinkstage`
- migration executed successfully
- view cache cleared
- route `settings.product-stats-suppliers.update` exists
- seeded supplier setting count: `7`

## 2026-08-25 / Sales line XLS marketplace source

Scope:
- support new `Export_Objednavok.xlsx` line export with `Ext` and `Portál`

Changed files:
- `platform/app/Services/MobdataOrderLineImportService.php`

Implementation:
- line import accepts `Odesláno` status
- `portal_country` now comes from line `Portál`
- `ext` now comes from line `Ext`
- empty `Ext` imports as `channel_type = own_web` and display `Mobilego {country}`
- `MALL + SL` displays as `MIMOVRSTE`
- `MALL + HR` displays as `MALL.HR`

Stage:
- service uploaded to `shoplinkstage`
- PHP syntax OK locally
- sample mapping checked from `/Users/zatykopeter/Downloads/Export_Objednavok.xlsx`

## 2026-08-25 / Sales line-only import and replacement

Scope:
- remove operational need for `Objednávky XLS`
- prevent duplicated data when importing an already covered period

Changed files:
- `platform/app/Services/MobdataOrderLineImportService.php`
- `platform/app/Http/Controllers/SalesIntelligenceController.php`
- `platform/resources/views/sales/index.blade.php`

Implementation:
- line import no longer filters by fixed status list; user exports only valid non-cancelled rows
- before inserting, line import deletes existing `sales_order_lines` between min/max `Dátum` from the XLS
- Sales overview KPIs, channel/country/marketplace/month stats now aggregate from `sales_order_lines`
- `Objednávky XLSX` import form was removed from the Sales UI

Stage:
- files uploaded to `shoplinkstage`
- PHP syntax OK locally
- Blade cache OK locally
- staging view cache cleared

## 2026-08-25 / Sales categories and monthly channel mix

Scope:
- clean category names from Google Merchant product type
- add a separate monthly marketplace vs e-shop comparison

Changed files:
- `platform/app/Services/HeurekaProductCatalogImportService.php`
- `platform/app/Http/Controllers/SalesIntelligenceController.php`
- `platform/resources/views/sales/index.blade.php`

Implementation:
- future product catalog imports strip leading `ESHOP >` from `g:product_type`
- Sales category rows also strip `ESHOP >` at display time for existing data
- added `monthlyChannelMix` aggregation from `sales_order_lines`
- added standalone Sales overview box `Marketplace vs e-shop po mesiacoch`

Stage:
- files uploaded to `shoplinkstage`
- PHP syntax OK locally
- Blade cache OK locally
- staging view cache cleared
- one-time DB cleanup ran; exact `ESHOP >%` rows found: `0`

## 2026-08-25 / Sales category levels

Scope:
- report category performance by wider Google category segments

Changed files:
- `platform/app/Http/Controllers/SalesIntelligenceController.php`
- `platform/resources/views/sales/index.blade.php`

Implementation:
- Sales category report no longer falls back to Mobdata order-line `Vetva`
- category report uses only Google Merchant `product_type`
- added category level switch: `2. úroveň`, `3. úroveň`, `posledná`
- default category level is `3`

Stage:
- files uploaded to `shoplinkstage`
- PHP syntax OK locally
- Blade cache OK locally
- staging view cache cleared

## 2026-08-25 / Product Stats preparation report removed from main view

Scope:
- restore Product Stats main list so the experimental preparation report does not alter the normal screen

Changed files:
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/resources/views/product-stats/index.blade.php`

Implementation:
- removed `preparationReport` payload from the main Product Stats index
- removed the `Predikcia / Príprava zásob` table from the Product Stats page
- kept the existing Product Stats supplier table and columns unchanged

Stage:
- files uploaded to `shoplinkstage`
- PHP syntax OK
- view/cache cleared
- authenticated `/product-stats` HTML confirms normal Product Stats columns render and the preparation section is absent

## 2026-08-25 / Product Stats standalone preparation report

Scope:
- add preparation/forecast report as a separate page without changing the main Product Stats table

Changed files:
- `platform/routes/web.php`
- `platform/app/Http/Controllers/ProductStatController.php`
- `platform/resources/views/product-stats/index.blade.php`
- `platform/resources/views/product-stats/preparation.blade.php`

Implementation:
- added `GET /product-stats/preparation`
- main Product Stats now only has a small `Príprava` link
- standalone report calculates top 30 products using Product Stats stock + incoming goods + Sales Intelligence `S90`, `S180`, previous-year `Q4`
- SKU sales query is chunked to avoid SQLite parameter limits

Stage:
- files uploaded to `shoplinkstage`
- PHP syntax OK
- route exists
- view/cache cleared
- authenticated `/product-stats/preparation` returns HTTP 200

## 2026-08-28 / Task Center two-state completion

Scope:
- simplify the whole Task Center to a two-state completion workflow

Implementation:
- all tasks now use only `Nová` and `Vybavená`
- every row now has an instant completion checkbox before the title
- the separate desktop `Status` column was removed
- completion updates list and edit state without refresh

Stage:
- Blade compilation passed
- completion API verified as `done / Vybavená`, then test task restored to `new / Nová`
- new CSS and JS assets return HTTP 200

## 2026-08-31 / Product Stats latest ORDER sorting

Scope:
- show newest selling products first in Product Stats

Implementation:
- changed Product Stats sorting so `ORDER` date is primary
- purchase priority remains as the next sorting rule
- applied both to the full filtered list and supplier-group product rows

Stage:
- deployed to `shoplinkstage`
- PHP syntax OK
- caches cleared
- authenticated `/product-stats` check showed the first 10 visible `ORDER` dates as `31.08.2026`

## 2026-09-03 / Product Stats order status supplier link

Scope:
- open the supplier portal directly from every row's status

Implementation:
- every status badge is now a supplier portal link when the supplier has a configured URL template
- it uses the same product-specific URL as the supplier shortcut badge

Stage:
- deployed to `shoplinkstage`
- Blade cache rebuilt successfully
- authenticated Product Stats HTML contains product-specific FDD supplier URLs on `Objednať` badges

## 2026-09-04 / Product Stats S1 equals P30 reorder rule

Scope:
- recommend ordering when current S1 stock would be exhausted by the last 30 days' sales

Implementation:
- `effective S1 <= P30` now yields `Objednať`
- incoming stock remains included in effective S1 before this comparison

Stage:
- deployed to `shoplinkstage`
- PHP syntax passed and cache cleared
- verified with SKU `TEC308736`: S1 `5`, P30 `5`, status `Objednať`

## 2026-09-14 / Product Stats table emphasis

- highlighted the P30 column with a light blue background
- darkened the selected product row
- deployed to staging; CSS/JS assets returned HTTP 200 and the result was browser-verified

## 2026-09-14 / Product Stats selected row tone

- lightened the selected product row from dark slate to medium-light slate
- deployed to staging and cleared the Laravel view cache

## 2026-09-28 / Settings task category wording

- clarified the right-side task category creation form labels and button
- deployed to staging, cleared the view cache, and browser-verified

## 2026-09-28 / Settings exchange-rate input sizing

- reduced EUR exchange-rate input cards to a compact width suitable for values up to 10 characters
- deployed to staging, cleared the view cache, and browser-verified

## 2026-09-28 / Settings supplier card alignment

- corrected `Objednávny tovar` to `Objednaný tovar`
- aligned supplier names with the input left edges
- changed mini uppercase field labels to the default form label style
- unified supplier card padding and inner spacing
- deployed to staging, cleared the view cache, and browser-verified

## 2026-09-28 / Settings EUR exchange-rate layout

- moved each currency label after its value input
- placed all five exchange-rate controls on one compact row on desktop
- built and deployed `app-LcClJ1ft.css` / `app-D8T4qs8Q.js`
- verified both asset URL variants and browser-rendered staging output

## 2026-09-28 / Settings supplier heading size

- aligned Aktívni dodávatelia with the default form label size
- deployed to staging and cleared the view cache
## 2026-09-20 / Category KPI assortment expansion report

Scope:
- add a separate Sales Intelligence report for the six requested category KPIs

Implementation:
- added `GET /sales/category-report`
- added date filtering and 2nd-level / 3rd-level / leaf category aggregation
- calculates revenue, profit, weighted margin rate, catalog SKU count, revenue per SKU, and SKU concentration needed for 80% of category revenue
- includes zero-sales catalog SKUs in the SKU denominator so the report can support assortment expansion decisions
- linked the report from Sales Overview and Sales Detail

Stage:
- uploaded changed routes, controller, views, manifest, CSS, and JS assets to `shoplinkstage`
- cleared Laravel view, cache, and config caches
- PHP syntax and Blade cache passed locally
- browser verified `/sales/category-report` with 617 3rd-level rows and the 2nd-level filter recalculation
- assets: `app-vQK-Wqh6.css`, `app-D8T4qs8Q.js`

## 2026-09-20 / Category KPI channel filter

- added `Všetko`, `Len marketplace`, and `Bez marketplace` channel options to the category KPI report
- channel selection now filters the underlying sales aggregation before all six KPIs and 80% SKU concentration are calculated
- deployed the controller fix to staging, cleared caches, and browser-verified the own-web view with changed KPI values and selected `Bez marketplace`
## 2026-09-21 / Toptel Product Stats product link

Scope:
- add Toptel product links to Product Stats rows

Implementation:
- added the Toptel supplier URL template using `{code}` for the row EAN
- kept the existing shared supplier-link rendering and URL encoding logic

Stage:
- migration file uploaded and PHP syntax checked
- full `migrate --force` was not run because unrelated older migrations were pending on staging
- inserted/updated only the Toptel supplier setting directly in the staging DB
- browser verified Product Stats filtered to Toptel; links resolved to URLs containing the row EAN, for example `5900217505143`

## 2026-09-21 / Partnertele Product Stats product link

- added the Partnertele supplier URL template using `{code}` for the row EAN
- inserted/updated only the Partnertele supplier setting directly in the staging DB because unrelated older migrations were pending
- browser verified Product Stats filtered to Partnertele; EAN `5903396400070` resolves to `partnertele.com/products/search?query=5903396400070`

## 2026-09-28 / Hurtel XML stock import

- Added `HurtelStockImportService`, `ProductStatSupplierStock`, and migration `2026_09_28_120000_create_product_stat_supplier_stocks_table`.
- Hurtel IdoSell feed parser maps `<product id>` to `Dod.kód.` and normalizes positive stock to `in_stock`, otherwise `out_of_stock`.
- Added `/product-stats/import-hurtel-stock`, import panel control, and `DOD sklad` table column.
- Deployed to staging, ran migration and cache clears.
- Imported `stock_light_export_for_midinetit2.xml`: `21,153` rows.
- Browser verified staging `/product-stats`: Hurtel panel shows `21153 produktov` and import timestamp.

## 2026-09-28 / Supplier feed controls grouped in Settings

- Moved Hurtel XML and FDD CSV upload controls into Settings → Product Stats → Dodávatelia a objednávanie.
- Removed supplier-specific feed blocks from Product Stats; the page now keeps only the general XLSX import and `DOD sklad` status column.
- Browser verified Settings shows Hurtel (`21153 ks`) and FDD feed controls beside supplier rows; Product Stats has no Hurtel import block.

## 2026-09-28 / Product Stats supplier code display correction

- Restored the original table display: `Sklad. kód` remains the main stock code and the secondary line remains the supplier EAN/helper code extracted from `Dodávateľ`.
- `Dod.kód.` remains internal matching key for Hurtel XML only and no longer replaces the displayed supplier EAN.
- Deployed controller fix and cleared staging view/cache.

## 2026-09-28 / Product Stats supplier code visibility

- Added `Dod.kód.` as a secondary line under SKU.
- Kept supplier EAN/helper code in the existing secondary line under `Sklad. kód`.
- Browser verified Hurtel rows show both values separately.

## 2026-09-28 / Long Product Stats codes

- Constrained SKU, `Dod.kód.`, and `Sklad. kód` secondary lines to their fixed table-column widths with ellipsis and tooltip.
- Deployed the Blade-only change, cleared view cache, and browser-verified the Hurtel table layout.

## 2026-09-28 / Product Stats EAN and supplier stock filter

- Removed truncation from `Sklad. kód`; the full EAN remains visible and copyable.
- Added GET filter `DOD sklad`: Všetky, Skladom, Vypredané, Neznáme.
- Deployed controller and Blade changes, passed remote PHP syntax check, cleared view cache, and browser-verified Hurtel `Skladom` filter (`311` products).

## 2026-09-28 / Supplier feed distinction in Settings

- Renamed the FDD feed display to `Objednateľný tovar`; it remains stored in the incoming-items table.
- Kept Hurtel under `Sklad dodávateľa` and added a separate FDD supplier-stock placeholder for the future stock parser.
- Browser verified Settings shows FDD `91 / 258 ks` under incoming and `Bez feedu – pripraví sa samostatne` under supplier stock; Hurtel shows `21153 ks` under supplier stock.

## 2026-09-28 / Product Stats P30 highlight position

- Corrected the blue highlight from `T30` back to `P30` after the `DOD sklad` columns shifted the table indexes.
- Deployed Blade-only change, cleared view cache, and browser-verified Product Stats.

## 2026-09-28 / Settings layout for task categories, rates, and supplier feeds

- Narrowed the task-category editor and its add-category panel.
- Removed repeated `1 EUR =` labels above individual rate inputs; the explanatory text remains.
- Widened the supplier settings panel and split feed status into separate `Objednateľný tovar` and `Sklad dodávateľa` columns.
- Active imports now appear only where implemented: FDD incoming CSV and Hurtel stock XML; future FDD stock remains inactive.
- Deployed Blade-only change, cleared view cache, and browser-verified `/settings`.

## 2026-09-28 / Settings top layout and visible rate inputs

- Moved `Task okno a push` beside `Kategórie taskov` in the upper Settings section.
- Kept the category add form below the task settings box.
- Confirmed rate inputs remain rendered and editable for Ft, Kč, Zł, lei, and €.
- Deployed the Blade changes, passed remote PHP syntax check, cleared view cache, and browser-verified `/settings` visually and through the accessibility tree.

## 2026-09-28 / Settings three-box layout and category deletion

- Split the upper Settings area into separate boxes: `Kategórie taskov`, `Task okno a push`, and adding a new category by name/order.
- Added visible supplier table headers: `Dodávateľ`, `URL link`, `Cieľ dní`, `Objednateľný tovar`, `Sklad dodávateľa`.
- Added `Zmazať` action with confirmation for task categories; related tasks remain and lose only their category because the foreign key uses `nullOnDelete`.
- Deployed, passed remote PHP syntax checks, cleared view cache, and browser-verified `/settings`.

## 2026-09-28 / Removed exchange-rate explanation box

- Removed the bottom `Ako sa kurzy používajú` information box from Settings.
- Kept all exchange-rate inputs and save action unchanged.
- Deployed, cleared view cache, and browser-verified `/settings`.

## 2026-09-28 / FDD supplier stock CSV import

- Added a separate FDD supplier-stock importer for ZIP/CSV feeds.
- Parser reads `id` as the FDD supplier code and `ilość produktów` as stock quantity; positive quantity becomes `Skladom`, zero becomes `Vypredané`.
- Imported the supplied `flavourdesign.csv.zip` on staging: `3858` products, `3719` in stock, `139` out of stock.
- FDD stock import is available in Settings beside the existing FDD orderable CSV import as `Import ZIP/CSV`.
- Generalized Product Stats supplier-stock matching for FDD and Hurtel; browser verified FDD Product Stats statuses and `DOD sklad` filter.
## 2026-09-28 / Supplier stock URL refresh

- Added persistent Sklad feed URL per supplier setting.
- FDD and Hurtel now show a one-click Aktualizovať z URL action beside the upload action.
- URL refresh downloads the feed, uses the supplier-specific parser, replaces that supplier's stock snapshot, and stores the URL as the latest source.
- Settings now shows imported row count and last stock update timestamp for FDD and Hurtel.
- Added and migrated stock_feed_url; staging syntax checks passed and /settings browser verification shows both URL fields, buttons, counts, and timestamps.
## 2026-09-28 / Supplier import layout

- Replaced the dense supplier table with a supplier card layout: supplier/product URL/stock feed URL/target days on the first row, separate orderable and stock import panels on the second row.
- Added FDD orderable CSV import from URL with its own stored URL and last import metadata.
- FDD and Hurtel stock panels keep both file import and URL import.
- Added incoming_feed_url migration and deployed/browser-verified Settings.
## 2026-09-28 / Compact supplier cards

- Reworked supplier settings into compact two-column cards.
- Left panel contains supplier name, supplier URL, and target days.
- Right panel contains separate orderable and stock feed sections with aligned URL fields, import buttons, and last-import metadata.
- Darkened supplier labels and status text for readability; Blade cache cleared and browser-verified.
## 2026-09-28 / Supplier feed panels side-by-side

- Kept supplier basics in the left box and moved orderable/stock controls into two compact side-by-side panels in the right box.
- Reduced URL field width and vertical spacing.
- Deployed and browser-verified Settings after clearing Blade cache.
## 2026-09-28 / Fixed permanent two-column supplier layout

- Removed responsive breakpoints that caused supplier boxes to stack vertically.
- Supplier basics remain on the left; orderable and stock panels remain on the right, side-by-side.
- Deployed, rebuilt Blade cache, and browser-verified Settings.
## 2026-09-28 / Supplier card grid fix

- Forced the supplier basics box and feed box into a stable side-by-side grid with inline columns.
- Forced orderable and stock panels to stay side-by-side inside the feed box.
- Deployed, rebuilt Blade cache, and browser-verified Settings.
## 2026-09-28 / Final supplier card proportions

- Widened the left supplier basics box.
- Kept orderable feed above supplier stock feed in the right box.
- Deployed, rebuilt Blade cache, and browser-verified Settings.
## 2026-09-28 / Hide empty suppliers

- Supplier Settings now emphasizes the supplier name.
- Suppliers with a configured portal/feed URL or imported data are shown by default.
- Empty suppliers are hidden and can be revealed with Zobraziť všetkých dodávateľov, showing 15 hidden suppliers on staging.
- Deployed, cleared Blade cache, and browser-verified Settings.
## 2026-09-28 / Supplier typography

- Removed the standalone Dodávateľ label above supplier names.
- Supplier names now use a dark background and larger standard text.
- Increased small supplier labels and import metadata to standard text sizing.
- Deployed, cleared Blade cache, and browser-verified Settings.
## 2026-09-29 / Keep exchange rates above suppliers

- Removed the 2xl two-column layout that placed Kurzy voči EUR beside Dodávatelia a objednávanie.
- Exchange rates now occupy their own full-width row above the supplier section.
- Deployed and rebuilt Blade cache.
## 2026-09-29 / Compact Settings action buttons

- Removed full-width layout from rate and supplier save buttons.
- Buttons now keep content width and align right.
- Deployed and rebuilt Blade cache.
## 2026-09-29 / Unified Settings button style

- Standardized positive action buttons in Settings to the same sl-btn-primary design/color as Uložiť dashboard okno.
- Applied to category add, rates save, supplier save, show-all, and supplier import actions.
- Kept category deletion as a separate destructive style.
- Deployed and rebuilt Blade cache.
## 2026-09-29 / Simplified supplier cards

- Removed the nested right-side border and inner gray feed boxes.
- Supplier cards now use one main container, one gray basics panel on the left, and flat feed sections on the right separated by one divider.
- Deployed and rebuilt Blade cache.

## 2026-10-08 / Supplier feed delete actions

- added confirmation-protected Settings actions to delete FDD incoming data and FDD/Hurtel supplier-stock data
- preserved feed URL settings after deletion
- deployed routes, controller, and Settings view; cleared view/route caches
- browser-verified delete buttons; no destructive action executed

## 2026-10-13 / Product Stats table render optimization

- reverted the summary-only Product Stats view because the full supplier tables must remain visible
- kept only `content-visibility` and intrinsic-size hints on the table wrapper
- deployed the Blade view and cleared the staging view cache
- browser-verified that the full FDD table remains visible with 1,708 product rows

## 2026-10-13 / Product Stats pagination and indexes

- added 100-row server-side pagination per supplier while preserving supplier totals and filters
- added query indexes for Product Stats import/supplier/sales ordering and incoming supplier/EAN lookups
- preserved full filtered supplier export behavior across paginated pages
- deployed controller, view, and migration; migration completed and Laravel caches were cleared
- browser-verified FDD page 1 and page 2 with 100 rendered rows per page

## 2026-10-13 / Product Stats supplier filter speedup

- pushed supplier, text-search, and S1/S2 stock filters into the Product Stats database query before enrichment and pagination
- deployed the controller and cleared staging view/route caches
- browser-verified the FDD filter returns only FDD (1,166 products) and keeps 100-row pagination

## 2026-10-13 / Product Stats pagination removed

- removed server-side pagination from the Product Stats tables at the user's request
- kept database-level supplier/search/stock filtering and horizontal table scrolling
- staging browser verified FDD shows the full filtered table without pagination controls
## 2026-10-12 / Q4 S1-S2 channel split

- Updated `ProductStatController::buildPreparationPayload()` to aggregate own-web, marketplace, and Alza sales for S90/S180/Q4.
- Added separate S1/S2 forecasts and recommendations; S1 subtracts incoming quantities, S2 uses S2 stock.
- Updated preparation table headers and values to show `S1 / Alza` and `S1 / S2`.
- Deployed controller and Blade view to staging; `php -l`, `view:clear`, and `view:cache` passed.
- Browser verified authenticated `/product-stats/preparation` with 30 rows and split columns.
- Follow-up needed: current Alza listing availability check/feed, separate from historical sales classification.

## 2026-10-12 / Alza marketplace value fix

- Staging marketplace value is `ALZA.CZ`, so the exact `ALZA` condition returned zero Alza values.
- Changed S90/S180/Q4 matching to `ALZA%`.
- Deployed, ran `php -l` and `php artisan optimize:clear`, then verified populated S2/Alza values in the browser.

