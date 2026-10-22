# FUNCTIONAL_SPEC

Status: Active Draft

## 1. Cél

Ez a dokumentum a SHOPLINK Intelligence moduljainak részletes funkcionális specifikációs keretét adja meg.

Ez a verzió a modulonkénti kidolgozás kötelező szerkezetét és az MVP szempontjából prioritást rögzíti.

## 2. Kötelező modul-specifikációs sablon

Minden modul végleges specifikációjának tartalmaznia kell:

- üzleti célt;
- célfelhasználókat;
- fő képernyőket;
- fő user flow-kat;
- fő műveleteket;
- használt adatobjektumokat;
- státuszokat és állapotokat;
- automatizmusokat;
- szerepkör-jogokat;
- hibaállapotokat és üres állapotokat;
- acceptance criteria pontokat;
- non-goal elemeket.

## 3. MVP prioritású modulok

### Dashboard

Üzleti cél:

- napi vezetői és operatív áttekintés biztosítása.

Kötelező tartalom:

- KPI blokkok;
- sürgős figyelmeztetések;
- nyitott taskok;
- nyitott RMA-k;
- import állapot;
- szerepkör szerinti testreszabás.

Fő user flow:

- bejelentkezés után a felhasználó azonnal látja a napi legfontosabb információkat;
- a kritikus elemekből egy kattintással résznézetbe tud lépni.

### RMA

Üzleti cél:

- reklamációk teljes operatív követése és napi kezelhetősége.

Kötelező tartalom:

- új RMA létrehozás;
- lista és szűrők;
- részletező nézet;
- státuszváltások;
- hiányzó dokumentum figyelmeztetés;
- kapcsolódó feladatok;
- határidők.

Fő user flow:

- ügyfélszolgálat rögzíti az esetet;
- rendszer ellenőrzési vagy hiánypótlási taskot generál;
- eset státuszai végigkövethetők lezárásig.

### Task Center

Üzleti cél:

- minden operatív és rendszer által generált teendő egy helyen kezelhető legyen.

Kötelező tartalom:

- feladatlista;
- prioritás;
- határidő;
- felelős;
- státusz;
- forrásobjektum kapcsolat;
- manuális létrehozás;
- automatikus létrehozás.

Fő user flow:

- felhasználó a saját teendőit látja;
- egy task mögötti üzleti ok egyértelműen elérhető;
- lezárt task auditálható marad.

### User Management

Üzleti cél:

- felhasználók és hozzáférések kontrollált kezelése.

Kötelező tartalom:

- user lista;
- role hozzárendelés;
- aktív/inaktív állapot;
- alap jogosultsági mátrix.

### Settings

Üzleti cél:

- rendszer szintű konfigurációk kezelése.

Kötelező tartalom:

- import konfigurációk;
- alap rendszerparaméterek;
- admin-only nézetek;
- auditálható módosítások.

## 4. Második hullámos modulok

### Sales Intelligence

- értékesítés trendek és bontások;
- marketplace, ország, store, SKU szintű elemzés;
- romló és javuló teljesítmény jelzése.

### Product Intelligence

- termékszintű üzleti kép;
- profit, készlet, reklamáció, forgási sebesség;
- AI pontszám vagy minősítés.

### Inventory

- készletkockázat;
- újrarendelési javaslat;
- túlstock és out-of-stock jelzések.

## 5. Későbbi fázisú modulok

### Marketing Intelligence

- marketingköltség és üzleti eredmény összekapcsolása;
- kampányhatékonyság üzleti értelmezése.

### Finance

- marginok, díjak, költségterhelések;
- vezetői pénzügyi kontrollnézet.

### AI Decision Center

- napi összefoglaló;
- javaslatok;
- kockázati kiemelések;
- emberi jóváhagyást igénylő ajánlások.

### Intelligence

- mélyebb keresztmodulos mintafelismerés és döntéstámogatás.

## 6. Modulonként kötelező állapotkezelés

Minden modulnak definiálnia kell:

- `empty_state`
- `loading_state`
- `error_state`
- `success_feedback`
- `permission_denied_state`

## 7. Modulonként kötelező acceptance criteria minta

Példák:

- a felhasználó a saját szerepkörének megfelelő adatot látja;
- egy rekord fő státusza egyértelmű és konzisztens;
- a rendszer validálja a kötelező mezőket;
- a fő lista szűrhető és rendezhető;
- a kapcsolódó taskok elérhetők;
- a fontos módosítások auditálhatók.

## 8. Nyitott kidolgozási sorrend

1. Dashboard
2. RMA
3. Task Center
4. User Management
5. Settings
6. Sales Intelligence
7. Product Intelligence
8. Inventory
9. Marketing Intelligence
10. Finance
11. AI Decision Center
12. Intelligence
