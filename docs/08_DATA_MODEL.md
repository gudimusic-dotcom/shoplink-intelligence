# DATA_MODEL

Status: Active Draft

## 1. Cél

Ez a dokumentum a SHOPLINK Intelligence technikai adatmodelljének első strukturált vázát adja meg.

Kiindulópontja a `04_BUSINESS_OBJECTS.md`, de ez a dokumentum már implementáció-előkészítő nézetben gondolkodik.

## 2. Adatmodellezési alapelvek

- egy üzleti fogalomnak egy elsődleges entitása legyen;
- minden rekordnak legyen stabil azonosítója;
- minden fontos rekord auditálható legyen;
- státuszokat enum vagy egyértelmű referenciaértékek kezeljenek;
- importált adatoknál mindig meg kell őrizni a forrást;
- duplikációvédelmet egyértelmű üzleti kulcsok segítsék.

## 3. Kötelező technikai mezőminták

Az alábbi mezők az entitások többségénél elvártak:

- `id`
- `created_at`
- `updated_at`
- `created_by`
- `updated_by`
- `status`
- `source_system`
- `source_reference`

Kritikus entitásoknál javasolt:

- `deleted_at` vagy archíválási logika
- `version`
- `last_synced_at`

## 4. Fő entitáscsoportok

### Identity és access

- User
- Role
- UserRole

### Commerce és catalog

- Store
- Marketplace
- Brand
- Category
- Product
- ProductVariant

### Sales

- Order
- OrderItem
- Shipment
- Invoice

### Procurement és inventory

- Supplier
- Purchase
- PurchaseItem
- InventorySnapshot
- StockMovement

### Operations

- ComplaintRma
- Task
- Notification

### Intelligence és platform

- AiInsight
- ImportJob
- ImportJobRow
- Report

## 5. Minimálisan kidolgozandó entitások MVP-hez

### User

Fő mezők:

- name
- email
- is_active
- default_role_id
- last_login_at

### Role

Fő mezők:

- code
- name
- description

### Product

Fő mezők:

- sku
- ean
- name
- brand_id
- category_id
- lifecycle_status
- purchase_price
- sell_price

### Order

Fő mezők:

- order_number
- marketplace_id
- store_id
- ordered_at
- country_code
- payment_status
- fulfillment_status
- currency
- total_amount

### OrderItem

Fő mezők:

- order_id
- product_id
- quantity
- unit_price
- unit_cost
- margin_value

### InventorySnapshot

Fő mezők:

- product_id
- available_quantity
- reserved_quantity
- incoming_quantity
- safety_stock_quantity
- snapshot_at

### ComplaintRma

Fő mezők:

- case_number
- order_id
- product_id
- customer_reference
- opened_at
- status
- reason_code
- resolution_type
- assigned_user_id
- deadline_at

### Task

Fő mezők:

- title
- description
- priority
- status
- due_at
- assigned_user_id
- source_type
- source_id
- generated_by

### ImportJob

Fő mezők:

- import_type
- file_name
- started_at
- finished_at
- status
- total_rows
- imported_rows
- skipped_rows
- warning_count
- error_count

## 6. Kulcskapcsolatok

- User több Role-hoz tartozhat
- Order egy Marketplace-hez és egy Store-hoz tartozik
- Order több OrderItem-et tartalmaz
- OrderItem egy Product-hoz kapcsolódik
- InventorySnapshot egy Product-hoz tartozik
- ComplaintRma kapcsolódhat Order-höz és Product-hoz
- Task kapcsolódhat bármely üzleti objektumhoz polymorphic vagy egységes source mezővel
- Notification kapcsolódhat User-hoz és opcionálisan üzleti objektumhoz
- AiInsight kapcsolódhat Producthoz, Orderhez, Inventory-hoz vagy összesített entitáshoz
- ImportJob több ImportJobRow rekordot tartalmazhat

## 7. Kötelező státuszok, amelyeket külön kell véglegesíteni

- user státuszok
- task státuszok
- RMA státuszok
- import státuszok
- order fulfillment státuszok
- notification severity szintek

## 8. Adatforrás és lineage követelmények

Minden importált, szinkronizált vagy számolt üzleti adatnál rögzíteni kell:

- honnan érkezett;
- mikor érkezett;
- mely import job dolgozta fel;
- felülírható-e manuálisan;
- számolt vagy forrásadat.

## 9. Nyitott döntések

- kell-e külön Customer entitás az MVP-ben;
- Inventory pillanatképes vagy eseményalapú modell lesz-e az első verzióban;
- polymorphic vagy explicit kapcsolatok legyenek a Task és Notification esetén;
- a meglévő RMA rendszerből milyen mezőket kell migrálni;
- mennyi pénzügyi adat legyen első körben fizikailag tárolva.
