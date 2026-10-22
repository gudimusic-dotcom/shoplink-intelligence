# MASTER_VISION

## 1. Dokumentum célja

Ez a dokumentum a SHOPLINK Intelligence projekt fő víziódokumentuma.

Nem végleges funkcionális specifikáció, hanem a projekt alapköve: rögzíti, miért épül a rendszer, milyen modulokból áll, milyen üzleti elvek mentén kell fejleszteni, milyen adatforrásokra épül, és milyen hosszú távú irányt kell követnie.

Erre a dokumentumra épülnek később:

- System Architecture
- Database Design
- UI/UX Specification
- Business Rules
- Development Standards
- Coding Standards
- API Specification
- Roadmap
- Sprint Planning

Ha ez az alap stabil, akkor a projekt hónapokon keresztül következetesen fejleszthető anélkül, hogy a rendszer iránya szétesne.

## 2. Project Vision

### Küldetés

A SHOPLINK Intelligence egy vállalati operációs rendszer, amely egyetlen felületen biztosítja a MobileGo teljes működésének áttekintését.

A cél nem egy statisztikai rendszer fejlesztése.

A cél:

- gyorsabb döntések;
- nagyobb profit;
- kevesebb manuális munka;
- kevesebb elfelejtett feladat;
- teljes átláthatóság;
- minden fontos információ egy helyen.

A rendszer nem egyszerűen adatokat mutat. A rendszer döntéseket támogat.

## 3. Core Principles

Minden fejlesztésnek az alábbi alapelveket kell követnie:

- egy adatnak egy forrása van;
- automatizálás mindenhol, ahol lehetséges;
- szerepkör-alapú működés;
- modern SaaS felület;
- sötét design;
- gyors működés;
- moduláris felépítés;
- minden funkciónak üzleti haszna legyen.

Minden új funkciónál kötelező kérdések:

- növeli a profitot?
- időt takarít meg?
- csökkenti a hibákat?
- egyszerűbbé teszi a munkát?

Ha a válasz nem, akkor a funkció nem kerülhet az első verzióba.

## 4. System Modules

Minden modulnak önállóan is értéket kell adnia. A rendszernek úgy kell épülnie, hogy egy-egy elkészült modul már önmagában is hasznos legyen, és ne kelljen megvárni, amíg minden modul elkészül.

### 4.1 Dashboard

A Dashboard a rendszer napi kezdőközpontja.

Feladata:

- szerepkör szerint mutatni a napi teendőket;
- kiemelni a sürgős riasztásokat;
- összefoglalni a fontos KPI-okat;
- belépési pontot adni a felhasználó napi munkájához.

Ez nem általános kezdőlap, hanem munkára optimalizált operációs központ.

### 4.2 AI Decision Center

Az AI Decision Center a rendszer tanácsadó modulja, a SHOPLINK Intelligence döntéstámogató agya.

Minden reggel választ kell adnia az alábbi kérdésekre:

- mire kell ma figyelni?
- mi a legfontosabb teendő?
- hol van veszély?
- hol van lehetőség?
- mely döntések igényelnek vezetői figyelmet?

Az AI Decision Center nem helyettesíti a vezetői döntést, hanem előkészíti azt.

### 4.3 Intelligence

Az Intelligence modul elemzéseket és javaslatokat készít.

Feladata:

- összefüggések keresése az adatok között;
- üzleti minták felismerése;
- kockázatok kiemelése;
- lehetőségek azonosítása;
- ajánlások megfogalmazása.

Ez a modul adja a rendszer elemző rétegét, amely a nyers adatokból üzleti következtetéseket készít.

### 4.4 RMA

Az RMA modul a reklamációs életciklust kezeli végtől végig.

Fontos kiindulópont: ez a modul Laravelben már körülbelül elkészült, ezért a későbbi fejlesztési tervben külön kell dönteni arról, hogy integrálás, újraírás vagy fokozatos migráció történjen.

Alap folyamat:

- ügyfél;
- beérkezett reklamáció;
- ellenőrzés;
- gyártó;
- jóváírás;
- lezárás.

Az RMA modulnak napi használatra alkalmasnak kell lennie, nem csupán nyilvántartásként kell működnie.

### 4.5 Task Center

A Task Center a feladatok központja.

Nem egyszerű feladatlista, hanem olyan operációs modul, amely manuális és automatikusan generált teendőket is kezel.

Automatikusan generálható feladatok például:

- reklamáció hiányzó dokumentummal;
- sürgős rendelés;
- készlethiány;
- lejárt RMA-határidő;
- adathiány;
- vezetői figyelmet igénylő üzleti esemény.

A Task Center célja, hogy kevesebb fontos munka maradjon el vagy vesszen el a rendszerek között.

### 4.6 Sales Intelligence

A Sales Intelligence az értékesítési teljesítményt elemzi.

Elemzési dimenziók:

- országok;
- marketplace-ek;
- webshop;
- csatornák;
- SKU-k;
- trendek;
- időszakok.

A cél nem csak az, hogy megmutassa, mennyi volt az értékesítés, hanem az is, hogy feltárja, hol javul vagy romlik az üzleti teljesítmény.

### 4.7 Product Intelligence

A Product Intelligence termékszintű teljes képet ad.

Minden SKU-ról láthatóvá kell tenni:

- profit;
- készlet;
- reklamáció;
- fogyási sebesség;
- beszerzés;
- életciklus;
- AI pontszám.

Ez a modul segít eldönteni, mely termékek erősek, kockázatosak, kifutók, túlkészletezettek vagy újrarendelésre érdemesek.

### 4.8 Inventory

Az Inventory modul a készlet és beszerzés operációs központja.

Fő témák:

- kifogyási veszély;
- túl sok készlet;
- újrarendelési javaslatok;
- beszállítók;
- készletkockázatok;
- beszerzési döntések támogatása.

A cél a készlethelyzet üzleti értelmezése, nem pusztán a darabszámok megjelenítése.

### 4.9 Marketing Intelligence

A Marketing Intelligence a hirdetési teljesítményt üzleti nézőpontból mutatja.

Első fókusz:

- Google Ads adatok;
- költés;
- profit;
- ROAS;
- üzleti ROI.

Később API-alapú integrációval bővíthető.

A modul célja, hogy a marketing ne csak kampányszámok alapján legyen értékelve, hanem valódi üzleti eredmény alapján.

### 4.10 Finance

A Finance modul pénzügyi betekintést ad.

Fő témák:

- profit;
- marzsok;
- jutalékok;
- marketplace díjak;
- költségek;
- pénzügyi kockázatok.

Ez nem teljes könyvelési rendszer, hanem vezetői pénzügyi áttekintés és döntéstámogatás.

### 4.11 User Management

A User Management modul kezeli a felhasználókat, szerepköröket és jogosultságokat.

Példaszerepkörök:

- CEO;
- Marketing;
- Beszerzés;
- Ügyfélszolgálat;
- Raktár;
- Pénzügy;
- Adminisztrátor.

Minden felhasználó csak azt láthatja, ami a munkájához szükséges.

### 4.12 Settings

A Settings modul kezeli a rendszerbeállításokat, integrációkat és importokat.

Fő területek:

- beállítások;
- integrációk;
- importkonfigurációk;
- API-k;
- rendszerparaméterek.

Ez a modul legyen egyszerű, kontrollált és adminisztrátoroknak szánt.

## 5. Data Sources

Az első verzió adatforrása Excel import.

Az MVP-ben a Mobdata lesz az egyetlen elsődleges adatforrás.

Várható Mobdata exportok:

- eladások;
- készlet;
- beszerzési árak;
- marketplace adatok.

Későbbi adatforrások:

- Google Ads API;
- további API-k;
- egyéb üzleti rendszerek integrációi.

Az MVP-ben kiemelten fontos az egyszerűség és az adatforrások egyértelműsége.

## 6. Import Engine

Az Import Engine a rendszer egyik kritikus alapmodulja.

Az importnak képesnek kell lennie arra, hogy:

- felismerje a rendelésazonosítót;
- ne importáljon duplán;
- csak az új rekordokat töltse be;
- később frissítéseket is tudjon kezelni;
- visszajelzést adjon a sikeres és hibás importokról;
- jelezze az adatminőségi problémákat.

A cél az, hogy elég legyen mindig az utolsó hét vagy hónap exportját feltölteni. A rendszernek magától kell felismernie, mi új, mi ismétlődés, és mit kell frissíteni.

## 7. User Experience

A cél nem az, hogy a rendszer Excel legyen a böngészőben.

A cél egy prémium SaaS rendszer.

Inspirációk:

- Linear
- Notion
- Stripe Dashboard
- Vercel Dashboard
- Shopify Admin
- Asana

UX alapelvek:

- letisztult;
- modern;
- nagyon gyors;
- sötét design;
- animációkkal támogatott, de nem túlmozgó;
- szerepkör szerint személyre szabott;
- munkára optimalizált;
- döntéstámogató.

A felületnek professzionálisnak, gyorsnak és napi használatra kényelmesnek kell lennie.

## 8. Development Roadmap

### Phase 1: Projekt alap

- felhasználók;
- jogosultságok;
- import;
- dashboard.

### Phase 2: RMA

- meglévő Laravel RMA modul felmérése;
- integrációs vagy migrációs döntés;
- napi használatra alkalmas RMA-folyamat.

### Phase 3: Sales Intelligence

- értékesítési adatok feldolgozása;
- ország, marketplace, webshop és SKU szerinti elemzések;
- trendek és figyelmeztetések.

### Phase 4: Product Intelligence

- SKU-szintű üzleti kép;
- profit, készlet, reklamáció, fogyási sebesség;
- termékéletciklus és AI pontszám.

### Phase 5: Marketing Intelligence

- marketingköltés és üzleti eredmény összekapcsolása;
- Google Ads irány;
- ROAS és üzleti ROI.

### Phase 6: AI Decision Center

- napi döntési összefoglalók;
- figyelmeztetések;
- lehetőségek;
- AI ajánlások.

## 9. Long-Term Vision

A végső cél nem egy dashboard.

A végső cél a MobileGo saját operációs rendszere.

Reggel minden alkalmazott ugyanabba a rendszerbe lép be. Mindenki csak azt látja, ami rá tartozik.

A rendszer automatikusan mutatja:

- feladatok;
- problémák;
- lehetőségek;
- KPI-ok;
- AI ajánlások.

Így nem kell 10 különböző alkalmazást használni. A SHOPLINK Intelligence lesz a központi munkafelület, ahol a napi működés, vezetői áttekintés és döntéstámogatás egy helyen történik.

