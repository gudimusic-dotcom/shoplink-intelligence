# DOCUMENTATION_INDEX

## 1. Cél

Ez a dokumentum rendet tart a SHOPLINK Intelligence projekt dokumentációjában.

Kimondja, mely dokumentumok aktívak, melyik mire való, milyen sorrendben kell őket olvasni, és mi számít elsődleges forrásnak, ha két dokumentum között eltérés van.

## 2. Aktív dokumentáció

Az aktív dokumentumok a `docs/` mappában találhatók.

### CURRENT_HANDOFF.md

Leggyorsabb belépési pont másik gépen.

Ez tartalmazza:

- az aktuális staging állapotot;
- a legutóbbi módosítások lényegét;
- a stage login, SSH és DB hozzáférést;
- a jelenlegi asset neveket;
- a rövid deploy checklistet.

Ha csak gyorsan folytatni kell a munkát, ezt kell elsőként megnyitni, utána `WORKSTATE.md`, `SESSION_LOG.md`, `RESUME_PROTOCOL.md`.

### WORKSTATE.md

Elsőként olvasandó operatív állapotfájl.

Ez tartalmazza:

- a projekt aktuális implementációs állapotát;
- staging és szerver információkat;
- adatbázis és deployment kontextust;
- design döntéseket;
- következő ajánlott lépést.

Minden workstation folytatás ezt kell hogy kezdje.

### SESSION_LOG.md

Rövid, időrendi session memória.

Ez azt rögzíti:

- mi történt az utóbbi munkamenetekben;
- mi lett deployolva;
- mi maradt nyitva.

### RESUME_PROTOCOL.md

Kötelező folytatási protokoll.

Ez írja le:

- milyen sorrendben kell állapotot visszaolvasni;
- mit kell futtatni új gépen;
- hogyan kell session végén visszaírni az állapotot.

### 01_MASTER_VISION.md

Elsődleges víziódokumentum.

Ez írja le:

- miért épül a rendszer;
- mi a hosszú távú cél;
- milyen modulokból áll;
- milyen adatforrásokra épül;
- milyen UX-irányt kell követni;
- milyen fejlesztési fázisok várhatók.

Ha stratégiai kérdésben bizonytalanság van, először ezt kell olvasni.

### 02_PROJECT_BRIEF.md

Rövid projektösszefoglaló.

Ez írja le:

- projekt neve;
- projekt célja;
- megoldandó problémák;
- célfelhasználók;
- MVP sikerfeltételei;
- alapelvek röviden.

Ez a gyors belépő dokumentum új résztvevőknek.

### 03_PROJECT_PRINCIPLES.md

A projekt alkotmánya.

Ez írja le:

- hogyan hozunk fejlesztési döntéseket;
- mi kerülhet be az MVP-be;
- mikor mondunk nemet egy új ötletre;
- milyen minőségi követelményeknek kell megfelelnie minden modulnak;
- hogyan kell kezelni az adatforrásokat, AI-t, szerepköröket és automatizálást.

Ha egy funkció ötlet vitás, ezt kell döntési szűrőként használni.

### 04_BUSINESS_OBJECTS.md

Üzleti objektumok katalógusa.

Ez írja le a rendszer alapfogalmait:

- User;
- Role;
- Marketplace;
- Product;
- Supplier;
- Inventory;
- Order;
- RMA;
- Task;
- Notification;
- AI Insight;
- Import Job;
- Report.

Ez még nem adatbázis-terv, hanem üzleti fogalmi alap. A későbbi adatmodell ebből indul.

## 3. Archív dokumentáció

A régi, duplikált vagy korábbi munkaverziók a `docs/archive/` mappába kerülnek.

Az archív dokumentumok nem törlendők, de nem tekintendők aktuális forrásnak.

Ha egy archív dokumentumban hasznos rész található, azt előbb be kell emelni egy aktív dokumentumba, és csak utána lehet rá fejlesztési döntést alapozni.

## 4. Elsődlegességi sorrend

Ha operatív folytatásról, aktuális deployról, hozzáférésről, assetről vagy implementációs állapotról van szó, ez a sorrend dönt:

1. `CURRENT_HANDOFF.md`
2. `WORKSTATE.md`
3. `SESSION_LOG.md`
4. `RESUME_PROTOCOL.md`
5. aktuális kód a `platform/` mappában
6. chat history csak másodlagos kontextusként

Ha stratégiai vagy termékdöntésről van szó, ez a sorrend dönt:

1. `03_PROJECT_PRINCIPLES.md`
2. `01_MASTER_VISION.md`
3. `02_PROJECT_BRIEF.md`
4. `04_BUSINESS_OBJECTS.md`
5. archív dokumentumok, csak ha az aktív dokumentumokba előbb be lett emelve a releváns rész

A `PROJECT_PRINCIPLES` magasabb szintű szabály, mint bármely modulötlet, de nem írja felül az aktuális operatív állapotot rögzítő `CURRENT_HANDOFF.md` és `WORKSTATE.md` fájlokat.

## 5. Következő szükséges dokumentumok

A további modulfejlesztéshez és a meglévő alap stabilizálásához még legalább ezekre lesz szükség:

- `05_USER_ROLES.md`
- `06_MVP_SCOPE.md`
- `07_FUNCTIONAL_SPEC.md`
- `08_DATA_MODEL.md`
- `09_UI_UX_SPECIFICATION.md`
- `10_SYSTEM_ARCHITECTURE.md`
- `11_DEVELOPMENT_ROADMAP.md`

Ezeket csak az aktív dokumentumokkal összhangban szabad elkészíteni.

## 6. Dokumentációs szabály

Minden új dokumentumnak:

- egyértelmű célt kell adni;
- jeleznie kell, hogy aktív vagy archív;
- illeszkednie kell a master vision és principles dokumentumokhoz;
- nem ismételheti feleslegesen más dokumentum teljes tartalmát;
- döntésre használható formában kell megfogalmaznia a szabályokat.

## 7. Fejlesztési szabály

A kódolás 2026-07-28-án elindult, és a `platform/` mappában már létezik a Laravel alap, a dashboard shell és a Task Center MVP.

A projekt tehát már implementációs fázisban van, de a dokumentáció továbbra is kötelező iránytű. Új modul, nagyobb workflow vagy üzleti szabály csak akkor fejleszthető tovább, ha az érintett dokumentációban szerepel vagy előbb oda bejegyzésre kerül.

A legfrissebb operatív állapot elsődleges forrása mindig a `CURRENT_HANDOFF.md`, utána a `WORKSTATE.md`, a `SESSION_LOG.md` és a `RESUME_PROTOCOL.md`.
