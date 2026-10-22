# USER_ROLES

Status: Active Draft

## 1. Cél

Ez a dokumentum rögzíti a SHOPLINK Intelligence szerepköreit, jogosultsági logikáját és szerepkör szerinti napi munkanézeteit.

Ez nem technikai RBAC implementációs leírás, hanem üzleti jogosultsági alapdokumentum.

## 2. Szerepkör-tervezési alapelvek

- minden felhasználó legalább egy szerepkörhöz tartozik;
- a szerepkör határozza meg az alapértelmezett dashboardot;
- a szerepkör határozza meg az elérhető modulokat;
- a szerepkör határozza meg az olvasási és szerkesztési jogosultságokat;
- érzékeny pénzügyi vagy vezetői adatok csak indokolt szerepkörök számára láthatók;
- kritikus üzleti folyamatoknál külön kell választani a megtekintési, szerkesztési és jóváhagyási jogot.

## 3. Jogosultsági modell

Minden modulban az alábbi szinteket kell tudatosan kezelni:

- `no_access`: a felhasználó nem látja a modult;
- `read`: a felhasználó megtekintheti az adatot;
- `write`: a felhasználó módosíthatja a rekordokat;
- `approve`: a felhasználó jóváhagyhat kritikus lépéseket;
- `admin`: a felhasználó konfigurálhatja a modult vagy a rendszerbeállításokat.

## 4. Tervezett szerepkörök

### CEO

Fő cél:

- teljes vállalati áttekintés;
- napi döntési támogatás;
- kivételek, kockázatok és lehetőségek gyors azonosítása.

Láthatja:

- vezetői dashboard;
- összes KPI;
- Sales, Product, Inventory, Finance összesítő nézetek;
- RMA összesített állapotok;
- AI ajánlások és figyelmeztetések;
- vezetői szintű taskok.

Módosíthatja:

- prioritások;
- vezetői státuszok;
- delegálható feladatok;
- bizonyos jóváhagyások.

Nem szükséges számára:

- alacsony szintű napi adatkarbantartás;
- tömeges adminisztrációs műveletek.

### Ügyfélszolgálat

Fő cél:

- reklamációk és ügyfélhez kapcsolódó operatív ügyek kezelése.

Láthatja:

- saját RMA-lista;
- ügyfélhez kötött feladatok;
- reklamáció státuszok;
- hiányzó dokumentumok;
- kapcsolódó rendelési alapadatok.

Módosíthatja:

- RMA rekordok operatív mezői;
- státuszfrissítések a saját folyamatlépéseiben;
- megjegyzések, csatolmányok, belső kommentek.

Nem láthatja:

- teljes pénzügyi összesítéseket;
- teljes cégszintű profit- vagy marketing riportokat.

### Raktár

Fő cél:

- készlethez, beérkezéshez és fizikai árumozgáshoz kötött napi műveletek támogatása.

Láthatja:

- készlethez kapcsolódó operatív nézetek;
- inbound és outbound feladatok;
- RMA fizikai beérkezések;
- készlethiány és eltérés figyelmeztetések.

Módosíthatja:

- raktári státuszok;
- beérkezési visszajelzések;
- ellenőrzési mezők;
- készlethez kötött operatív megjegyzések.

Nem láthatja:

- marketing teljesítményt;
- részletes pénzügyi margin riportokat.

### Beszerzés

Fő cél:

- készletkockázatok kezelése és újrarendelési döntések előkészítése.

Láthatja:

- beszállítók;
- készletfogyás;
- újrarendelési javaslatok;
- készlethiányok;
- termék- és beszállítói teljesítményhez kötött mutatók.

Módosíthatja:

- beszállítói adatok egy részét;
- beszerzési státuszokat;
- purchase rekordok operatív mezőit;
- készletreagálási feladatokat.

Nem láthatja:

- teljes HR jellegű vagy rendszerszintű admin beállításokat.

### Marketing

Fő cél:

- marketingteljesítmény üzleti eredményhez kötött értékelése.

Láthatja:

- marketing dashboard;
- kampányköltség, ROAS, ROI összesítések;
- termék- és csatornaszintű hatásmutatók;
- AI jelzések a marketingteljesítményről.

Módosíthatja:

- marketinghez kötött címkézések vagy elemzési szűrők;
- marketing megjegyzések;
- marketing feladatok státuszai.

Nem láthatja:

- teljes pénzügyi vagy adminisztrátori konfigurációkat;
- nem szükséges ügyfélszintű RMA részleteket.

### Pénzügy

Fő cél:

- pénzügyi kockázatok, marzsok, jóváírások és üzleti megtérülés követése.

Láthatja:

- finance dashboard;
- profit- és marzsadatok;
- marketplace díjak;
- jóváírások;
- RMA pénzügyi hatások;
- importok pénzügyi relevanciájú eltérései.

Módosíthatja:

- pénzügyi minősítések;
- ellenőrzési státuszok;
- egyes riportparaméterek;
- jóváírási folyamat egyes mezői.

Nem láthatja:

- feleslegesen részletes operatív warehouse munkanézeteket.

### Adminisztrátor

Fő cél:

- rendszer működésének és konfigurációjának felügyelete.

Láthatja:

- user management;
- settings;
- import konfigurációk;
- audit és hibalogikai nézetek;
- integrációs státuszok;
- háttérfolyamatok.

Módosíthatja:

- felhasználók és szerepkörök;
- rendszerparaméterek;
- import mappingek;
- technikai kapcsolatok;
- jogosultsági beállítások.

## 5. Szerepkör szerinti alap dashboardok

- CEO: összvállalati döntési központ
- Ügyfélszolgálat: RMA és ügykezelési operációs nézet
- Raktár: készlet- és árumozgás központ
- Beszerzés: készletkockázat és utánrendelési központ
- Marketing: kampány és üzleti teljesítmény nézet
- Pénzügy: profit és jóváírás fókuszú nézet
- Adminisztrátor: rendszerállapot és konfigurációs központ

## 6. Automatikusan kiosztható feladattípusok

- hiányzó RMA dokumentum;
- sürgős reklamáció;
- lejárt határidő;
- alacsony készlet;
- újrarendelési javaslat;
- import hiba;
- adathiány;
- rendellenes KPI változás;
- vezetői jóváhagyást igénylő esemény.

## 7. Nyitott döntések

Ezeket a későbbi specifikációban még véglegesíteni kell:

- lehet-e egy felhasználónak több szerepköre;
- hogyan öröklődnek a jogok több szerepkör esetén;
- kell-e rekordszintű hozzáférés ország vagy marketplace szerint;
- kell-e külön jóváhagyó szerepkör egyes pénzügyi vagy RMA folyamatokhoz;
- mely adatok számítanak érzékenynek.

## 8. Kötelező véglegesítési checklist

Ez a dokumentum akkor tekinthető késznek, ha minden szerepkörnél rögzítve van:

- látható modulok;
- olvasási jogok;
- szerkesztési jogok;
- jóváhagyási jogok;
- tiltott adatkörök;
- alap dashboard;
- automatikus feladattípusok;
- kulcs KPI-ok.
