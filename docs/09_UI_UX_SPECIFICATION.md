# UI_UX_SPECIFICATION

Status: Active Draft

## 1. Cél

Ez a dokumentum a SHOPLINK Intelligence felhasználói élményének és vizuális rendszerének működési alapjait rögzíti.

Nem csak esztétikai irányt ad, hanem képernyőtervezési és interakciós szabályokat is.

## 2. Alap UX elvek

- a rendszer munkára készüljön, ne bemutatóra;
- a fontos információk az első képernyőn látszódjanak;
- minden nézetből legyen világos a következő teendő;
- szerepkörönként más hangsúlyt kell kapnia a felületnek;
- a gyorsaság fontosabb, mint a díszítettség;
- az operatív táblázatoknak erős szűrési és keresési képességet kell adni.

## 3. Vizuális irány

- prémium SaaS élmény;
- sötét design;
- magas kontrasztú információs hierarchia;
- visszafogott, funkcionális színek;
- erős tipográfiai rendszer;
- állapotokhoz kötött konzisztens színkódolás.

## 4. Információs hierarchia

Minden fő képernyőn egyértelműen el kell különülni:

- elsődleges döntési információknak;
- figyelmeztetéseknek;
- operatív munkalistáknak;
- részletező adatoknak;
- háttérinformációknak.

Az oldal ne kezeljen minden adatot azonos vizuális súllyal.

## 5. Navigációs elvek

- a fő navigáció modulalapú legyen;
- a szerepkör számára irreleváns modul ne jelenjen meg;
- a dashboard legyen az elsődleges landing;
- lista és részletező nézetek között gyors váltás legyen;
- kritikus riasztásokból közvetlen átjárás vezessen a megoldási felületre.

## 6. Dashboard szabályok

- legfeljebb néhány elsődleges KPI legyen kiemelve;
- a KPI mellé trend vagy kontextus tartozzon;
- a figyelmeztetések prioritás szerint rendeződjenek;
- a dashboardból a napi munkahelyzet legyen leolvasható 10 másodperc alatt;
- a dashboard ne váljon általános widget-gyűjteménnyé.

## 7. Táblázatos operációs nézetek

Kötelező képességek:

- gyors keresés;
- több szűrő;
- rendezés;
- mentális prioritást segítő státuszjelölés;
- bulk műveletek, ha üzletileg indokolt;
- oszlophierarchia, amely kiemeli a fontos adatokat.

Kerülendő:

- túl sok oszlop alapértelmezetten;
- egyenrangú vizuális kezelés minden mezőnek;
- Excel-szerű túlzsúfoltság.

## 8. Részletező oldalak

Minden részletező oldalnak tartalmaznia kell:

- rekordazonosítást;
- fő státuszt;
- kulcsidőpontokat;
- kapcsolódó taskokat;
- kapcsolódó rekordokat;
- audit vagy eseménytörténetet, ha szükséges;
- jól látható fő műveleteket.

## 9. Állapotok és visszajelzések

Kötelező UI állapotok:

- loading;
- empty;
- error;
- success;
- permission denied;
- archived vagy inactive, ha releváns.

Minden művelet után a rendszernek egyértelmű visszajelzést kell adnia.

## 10. Szín- és státuszlogika

Példairány:

- piros: kritikus hiba vagy sürgős kockázat;
- narancs: figyelmeztetés;
- zöld: rendben vagy lezárt;
- kék: információ vagy navigációs fókusz;
- szürke: inaktív vagy másodlagos információ.

A végleges design systemnek pontos tokeneket kell adnia.

## 11. Reszponzivitás

- desktop az elsődleges célplatform;
- kisebb laptop és tablet nézet támogatása szükséges;
- mobilon legalább olvasható és alapműveletekre alkalmas állapot kell;
- kritikus táblázatoknál mobilon alternatív kártyás vagy szűkített nézet kellhet.

## 12. Kötelező kidolgozandó UI anyagok

- navigációs térkép;
- dashboard wireframe szerepkörönként;
- listaoldal minták;
- részletező oldal minták;
- státusz- és badge-rendszer;
- űrlapminták;
- üres és hibaállapot minták.

## 13. Inspirációs referencia

- Linear
- Notion
- Stripe Dashboard
- Vercel Dashboard
- Shopify Admin
- Asana

Ezek inspirációk, nem másolandó minták.
