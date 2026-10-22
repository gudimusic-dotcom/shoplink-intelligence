# BUSINESS_OBJECTS

## 1. Dokumentum célja

Ez a dokumentum a SHOPLINK Intelligence alapvető üzleti objektumait írja le.

Ezek nem végleges adatbázistáblák, hanem üzleti fogalmak. A későbbi adatmodellnek és moduloknak ezekből kell kiindulniuk.

Új modul csak akkor vezessen be új üzleti objektumot, ha a meglévők között nincs megfelelő fogalom.

## 2. Alap üzleti objektumok

### User

A rendszer felhasználója, jellemzően MobileGo alkalmazott.

Példák:

- CEO;
- raktári dolgozó;
- marketing specialista;
- ügyfélszolgálatos;
- beszerzési vezető;
- adminisztrátor.

### Role

Jogosultságot és láthatóságot meghatározó szerepkör.

Példák:

- CEO;
- raktár;
- marketing;
- pénzügy;
- ügyfélszolgálat;
- beszerzés;
- adminisztrátor.

### Marketplace

Értékesítési csatorna vagy piactér.

Példák:

- Alza;
- Mall;
- Amazon;
- eMAG;
- eBay;
- saját webshop;
- későbbi marketplace-ek.

### Store

Webshop vagy üzleti egység.

Egy vállalat több store-t is működtethet.

### Product

Eladható termék.

Jellemző adatok:

- SKU;
- EAN;
- név;
- márka;
- kategória;
- státusz;
- beszerzési ár;
- eladási ár;
- aktuális készlet;
- életciklus;
- AI pontszám.

### Product Variant

Egy termék opcionális változata.

Példák:

- szín;
- kapacitás;
- méret;
- kiszerelés.

### Brand

Gyártó vagy termékmárka.

### Category

Hierarchikus termékkategória.

### Supplier

Beszállító.

Jellemző adatok:

- beszállítói információk;
- szállítási idő;
- fizetési feltételek;
- teljesítménymutatók.

### Inventory

Aktuális készletinformáció.

Követett értékek:

- aktuális készlet;
- foglalt készlet;
- elérhető készlet;
- beérkező készlet;
- biztonsági készlet.

### Stock Movement

Minden készletváltozást leíró esemény.

Példák:

- beszerzés;
- eladás;
- visszáru;
- reklamáció;
- kézi korrekció;
- leltár.

### Purchase

Beszállítói rendelés.

### Purchase Item

Beszállítói rendelés egy konkrét terméksora.

### Customer

Vevőadat, amennyiben az adott folyamatnál szükséges.

Az MVP-ben különösen figyelni kell arra, hogy csak indokolt és szükséges vevőadat kerüljön a rendszerbe.

### Order

Vevői rendelés.

Elsődleges forrása az MVP-ben a Mobdata.

Jellemző adatok:

- marketplace;
- ország;
- dátum;
- fizetés;
- szállítás;
- státusz;
- profit.

### Order Item

Egy rendelésen belüli konkrét terméksor.

### Shipment

Szállítási információ.

### Invoice

Kereskedelmi vagy pénzügyi dokumentum, csak ha az adott folyamat indokolja.

A SHOPLINK Intelligence nem könyvelési rendszer. A könyvelés külső rendszerben marad.

### Complaint / RMA

Reklamáció vagy RMA-ügy.

Teljes életciklust kezel:

- beérkezett;
- ellenőrzés;
- gyártóra vár;
- megoldva;
- elutasítva;
- lezárva.

### Task

Konkrét teendő.

Feladat keletkezhet:

- manuálisan;
- automatikusan;
- AI javaslat alapján.

Forrásai lehetnek:

- Inventory;
- RMA;
- Sales;
- határidők;
- rendszerfigyelmeztetések.

### Notification

Rendszerértesítés vagy riasztás.

Példák:

- alacsony készlet;
- magas reklamációs arány;
- hiányzó adat;
- sikertelen import;
- fontos KPI-változás.

### AI Insight

AI által generált üzleti javaslat vagy felismerés.

Példák:

- ár emelése;
- termék újrarendelése;
- reklamációs arány vizsgálata;
- készlet csökkentése;
- marketingköltés növelése vagy csökkentése.

### Import Job

Adatimport futása.

Követett adatok:

- indulási idő;
- befejezési idő;
- hibák;
- figyelmeztetések;
- importált rekordok;
- kihagyott rekordok.

### Report

Üzleti riport.

Lehet:

- ütemezett;
- manuálisan generált;
- exportált;
- megosztott.

## 3. Kapcsolati alapelv

Minden későbbi modulnak ezeket az üzleti objektumokat kell újrahasználnia.

Nem szabad ugyanarra az üzleti fogalomra párhuzamos, más nevű objektumot bevezetni.

Ez a dokumentum a későbbi adatbázis-architektúra fogalmi alapja.

