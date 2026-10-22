# MVP_SCOPE

Status: Active Draft

## 1. Cél

Ez a dokumentum meghatározza, mi kerül be a SHOPLINK Intelligence első működő verziójába, és mi marad későbbi fázisokra.

Az MVP célja nem a teljes vízió megvalósítása, hanem egy napi használatra alkalmas, üzletileg értékes első rendszerverzió létrehozása.

## 2. MVP sikerdefiníció

Az MVP akkor tekinthető sikeresnek, ha:

- a Mobdata exportok importálhatók;
- az importált adatokból működő operatív nézetek épülnek;
- a vezetői dashboard napi használatra alkalmas;
- az RMA folyamat kezelhető a rendszerben;
- a Task Center képes manuális és automatikus feladatok kezelésére;
- az alap szerepkörök működnek;
- a felhasználók napi munkájuk egy releváns részét a rendszerben végzik.

## 3. Kötelezően benne lévő elemek

### 3.1 Adatimport

- Mobdata Excel import;
- import futásnapló;
- alap validáció;
- duplikációvédelem;
- hibás sorok és figyelmeztetések kezelése;
- import időbélyegek és forrásazonosítás.

### 3.2 Felhasználók és szerepkörök

- alap user kezelés;
- alap role kiosztás;
- modulonkénti hozzáférés-szabályozás;
- saját dashboard szerepkör szerint.

### 3.3 Dashboard

- vezetői napi áttekintés;
- fő KPI blokkok;
- sürgős figyelmeztetések;
- nyitott RMA-k;
- kiemelt taskok;
- import állapot röviden.

### 3.4 Task Center

- manuális task létrehozás;
- automatikus task generálás alap szabályok alapján;
- státuszok;
- felelős kijelölés;
- határidők;
- szűrés és prioritás.

### 3.5 RMA

- reklamáció létrehozása és nyilvántartása;
- alap státuszfolyamat;
- dokumentumhiány jelzése;
- határidőfigyelés;
- kapcsolódó task generálás;
- napi operatív használhatóság.

### 3.6 Alap üzleti figyelmeztetések

- alacsony készlet;
- lejárt vagy csúszó RMA;
- sikertelen import;
- hiányzó kötelező adat;
- kiemelt vezetői figyelmeztetés.

## 4. Opcionálisan bekerülhet, ha nem veszélyezteti a fókuszt

- egyszerű Sales összesítések;
- alap Product nézet SKU szinten;
- egyszerű export funkciók;
- kezdeti AI-alapú szöveges összefoglaló.

Ezek csak akkor kerülhetnek be, ha a core működés már stabil.

## 5. Kifejezetten nem MVP

- teljes Marketing Intelligence;
- teljes Finance modul;
- fejlett AI Decision Center;
- komplex forecasting;
- fejlett beszállítói scorecard;
- részletes workflow builder;
- külső API integrációk széles köre;
- teljes mobilalkalmazás;
- minden modul teljes analitikai mélysége.

## 6. MVP döntési szűrő

Egy új funkció csak akkor kerülhet be az MVP-be, ha legalább egy igaz:

- napi működéshez szükséges;
- vezetői áttekintéshez szükséges;
- importfolyamat működéséhez szükséges;
- RMA használhatóságához szükséges;
- manuális munka számottevő csökkentését adja;
- szerepköralapú működéshez szükséges.

Ha egy funkció inkább kényelmi, ritka vagy demonstrációs értékű, akkor nem MVP.

## 7. MVP elfogadási minimumok

Az első verzió csak akkor tekinthető kiadhatónak, ha:

- az import ismételhető és auditálható;
- a fő képernyők betöltése elfogadható sebességű;
- a kritikus üzleti státuszok egyértelműek;
- a szerepkörök nem látnak indokolatlan adatot;
- a rendszer alap hibakezelése működik;
- a kulcsfolyamatokhoz van üres állapot és hibaállapot megjelenítés;
- a napi operatív taskok nem vesznek el.

## 8. Függőségek

Az MVP megvalósításának sorrendjében az alábbi függőségek kritikusak:

1. adatforrás és importstruktúra
2. user és role modell
3. alap adatmodell
4. dashboard és task alapok
5. RMA operációs folyamat
6. figyelmeztetések és automatizmusok

## 9. Nyitott döntések

- a meglévő Laravel RMA modul integrálódik vagy újraépül;
- az első verzióban mennyi Sales és Product adat kerül be;
- kell-e már MVP-ben több store vagy több marketplace-specifikus logika;
- mely KPI-ok minősülnek kötelező vezetői KPI-nak.
