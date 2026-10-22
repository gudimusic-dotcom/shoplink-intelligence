# PROJECT_PRINCIPLES

## 1. A dokumentum célja

Ez a dokumentum a SHOPLINK Intelligence projekt alkotmánya.

Nem funkciókat ír le, hanem azokat a döntési szabályokat, minőségi elvárásokat és fejlesztési alapelveket, amelyek alapján a projekt minden későbbi része készül.

A dokumentum célja, hogy a rendszer hosszú távon is következetes, érthető, üzletileg hasznos és bővíthető maradjon.

## 2. Legfontosabb projektelv: Business first

A SHOPLINK Intelligence nem technológiai kísérlet, hanem üzleti operációs rendszer.

Minden funkciónak világos üzleti célhoz kell kapcsolódnia:

- időt takarít meg;
- hibát előz meg;
- döntést gyorsít;
- bevételt növel;
- költséget csökkent;
- átláthatóbbá tesz egy folyamatot;
- automatizál egy manuális feladatot.

Ha egy funkció üzleti haszna nem magyarázható el egyszerűen, akkor nem kerülhet be az MVP-be.

## 3. MVP-döntési szabályok

Az MVP-be csak olyan funkció kerülhet be, amely legalább az egyik feltételnek megfelel:

- szükséges a napi működéshez;
- szükséges a vezetői áttekintéshez;
- szükséges az RMA-folyamat használhatóvá tételéhez;
- szükséges a Mobdata exportok feldolgozásához;
- szükséges a szerepkör-alapú működéshez;
- közvetlenül csökkenti a manuális munkát;
- nélkülözhetetlen alapja egy későbbi kritikus modulnak.

Az MVP-ből ki kell hagyni minden olyan funkciót, amely:

- csak kényelmi extra;
- ritkán használt különleges esetet kezel;
- nem kapcsolódik közvetlenül a napi működéshez;
- jelentősen növeli a komplexitást mérhető üzleti haszon nélkül;
- később is könnyen hozzáadható.

## 4. Mikor mondunk nemet egy új ötletre?

Egy új ötletre nemet kell mondani, vagy későbbi fázisba kell sorolni, ha:

- nem old meg konkrét üzleti problémát;
- nem világos, ki és milyen gyakran használná;
- csak azért merül fel, mert technikailag érdekes;
- több bonyolultságot hoz, mint amennyi értéket teremt;
- megdupláz egy meglévő funkciót;
- gyengíti az adatforrások egyértelműségét;
- lassítja az MVP elkészülését.

A nemet mondás nem elutasítás, hanem fókuszvédelem. Az elhalasztott ötleteket későbbi backlogba lehet tenni, ha később üzletileg indokolttá válnak.

## 5. Egy adatnak egy forrása van

A rendszerben minden fontos üzleti adatnak egyértelmű elsődleges forrással kell rendelkeznie.

Ez különösen fontos az alábbi területeken:

- termékadatok;
- készletadatok;
- értékesítési adatok;
- reklamációk;
- RMA-státuszok;
- felhasználói jogosultságok;
- pénzügyi mutatók;
- üzleti KPI-ok.

Ha ugyanaz az adat több helyen jelenik meg, akkor is egyértelműnek kell lennie, honnan származik, mikor frissült, és melyik rendszer vagy import felel érte.

## 6. Automatizálás minden lehetséges ponton

A SHOPLINK Intelligence célja, hogy ne csak megjelenítse az adatokat, hanem csökkentse a kézi munkát.

Automatizálni kell minden olyan ismétlődő folyamatot, amely:

- szabály alapján eldönthető;
- importált adatokból kiszámítható;
- emberi ellenőrzést igényelhet, de emberi előkészítést nem;
- rendszeres figyelmeztetésként vagy feladatként megjeleníthető.

Az automatizálás nem veheti el a kontrollt a felhasználótól. A rendszer javasolhat, előkészíthet és figyelmeztethet, de kritikus üzleti döntések esetén a végső jóváhagyásnak embernél kell maradnia.

## 7. Szerepkör-alapú működés

A rendszer minden modulját szerepkörök szerint kell tervezni.

Egy felhasználó csak azt lássa:

- amire a munkájához szüksége van;
- amiért felelősséggel tartozik;
- amit jogosult módosítani;
- amihez üzleti indoka van hozzáférni.

A túl sok információ ugyanolyan káros lehet, mint a túl kevés. A felületnek minden szerepkör számára célzott, tiszta és munkára kész nézetet kell adnia.

## 8. Feladatközpontú működés

A rendszer nem pusztán adatbázis és riportfelület.

Minden modulnál törekedni kell arra, hogy az adatokból konkrét teendők szülessenek:

- sürgős reklamáció;
- lejárt határidejű RMA;
- alacsony készlet;
- romló értékesítési trend;
- kiugróan magas visszaküldési arány;
- hiányzó adat;
- pénzügyi vagy működési figyelmeztetés.

A jó képernyő nem csak megmutatja, mi történt, hanem segít eldönteni, mit kell tenni.

## 9. Minőségi követelmények minden modulra

Minden modulnak meg kell felelnie az alábbi minimumkövetelményeknek:

- egyértelmű üzleti cél;
- jól meghatározott célfelhasználó;
- szerepkör-alapú hozzáférés;
- kereshető, szűrhető vagy rendezhető fő adatok;
- világos státuszok;
- egyértelmű üres állapotok;
- hibakezelés és visszajelzés;
- auditálható fontos módosítások;
- későbbi bővíthetőség;
- modern, prémium SaaS élmény.

Ha egy modul ezek közül több pontnak nem felel meg, akkor nem tekinthető késznek.

## 10. Felhasználói élmény alapelvei

A rendszer felhasználói felülete legyen:

- gyors;
- letisztult;
- munkára optimalizált;
- szerepkör szerint személyre szabott;
- kevés kattintással használható;
- táblázatoknál jól szűrhető és áttekinthető;
- dashboardoknál döntéstámogató;
- vizuálisan modern és prémium hatású.

Kerülni kell:

- a túlzsúfolt képernyőket;
- a felesleges dekorációt;
- az öncélú animációkat;
- a túl sok egyszerre látható mutatót;
- a nem egyértelmű státuszokat;
- a rejtett vagy nehezen megtalálható fő műveleteket.

## 11. AI használatának elvei

Az AI a rendszerben döntéstámogató és automatizálást segítő réteg.

Az AI feladata lehet:

- trendek felismerése;
- kockázatok jelzése;
- napi ajánlások készítése;
- reklamációs minták felismerése;
- készlet- vagy értékesítési anomáliák kiemelése;
- vezetői összefoglalók készítése.

Az AI nem lehet fekete doboz kritikus üzleti döntésekben. A felhasználónak látnia kell, milyen adatok alapján készült egy javaslat, és a végső döntésnek emberi jóváhagyással kell történnie.

## 12. Moduláris architektúra

A rendszert modulárisan kell tervezni, hogy hosszú távon bővíthető legyen.

Minden modulnak legyen jól körülhatárolt felelőssége. Egy modul ne próbáljon túl sok különböző üzleti problémát megoldani.

Elsődleges modulok várhatóan:

- vezetői dashboard;
- RMA és reklamációkezelés;
- feladatkezelés;
- Mobdata import és adatfeldolgozás;
- készlet- és értékesítési elemzés;
- szerepkör- és jogosultságkezelés;
- AI ajánlások;
- riportok és exportok.

## 13. Dokumentáció elsőbbsége

A projektben először a dokumentációt kell felépíteni, és csak utána kezdődhet a fejlesztés.

A fejlesztés előtt legalább az alábbi dokumentumok szükségesek:

- PROJECT_BRIEF.md
- PROJECT_PRINCIPLES.md
- FUNCTIONAL_SPEC.md
- DATA_MODEL.md
- USER_ROLES.md
- MVP_SCOPE.md
- ROADMAP.md

Fejlesztési munka csak akkor indulhat, ha az adott modul célja, felhasználói köre, adatmodellje és MVP-határai dokumentáltak.

## 14. Változtatási szabály

Ha egy későbbi döntés ellentmond ennek a dokumentumnak, akkor előbb ezt a dokumentumot kell tudatosan módosítani.

Nem szabad csendben eltérni az alapelvektől.

Minden nagyobb változtatásnál fel kell tenni a kérdést:

- segíti a napi működést?
- csökkenti a manuális munkát?
- gyorsítja a döntéshozatalt?
- egyszerűbbé teszi a rendszert?
- illeszkedik az MVP céljához?
- megőrzi az egy adat, egy forrás elvet?

Ha a válaszok többsége nem, akkor az ötletet nem szabad az aktuális fejlesztési körbe beemelni.

