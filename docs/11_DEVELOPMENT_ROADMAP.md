# DEVELOPMENT_ROADMAP

Status: Active Draft

## 1. Cél

Ez a dokumentum a SHOPLINK Intelligence fejlesztési fázisait, sorrendjét és minimális mérföldköveit rögzíti.

## 2. Roadmap alapelvek

- először a napi működéshez szükséges képességek készüljenek el;
- minden fázis önmagában is üzleti értéket adjon;
- új modul csak akkor induljon, ha az előző fázis alapjai stabilak;
- a dokumentáció, adatmodell és szerepkörök minden új modulnál és nagyobb workflow-nál meg kell előzzék a fejlesztést;
- a 2026-07-28-án létrehozott Laravel alapot és Task Center MVP-t aktuális kiinduló állapotnak kell tekinteni.

## 3. Phase 0: Dokumentációs alap lezárása

Cél:

- build-ready specifikációs minimum elérése.

Kimenetek:

- USER_ROLES első végleges verzió;
- MVP_SCOPE első végleges verzió;
- FUNCTIONAL_SPEC MVP modulokra kidolgozva;
- DATA_MODEL MVP entitásokra kidolgozva;
- SYSTEM_ARCHITECTURE első döntési verzió.

Kilépési feltétel:

- a core MVP-re nincs nyitott stratégiai ellentmondás.

## 4. Phase 1: Platform Foundation

Cél:

- technikai és jogosultsági alap megteremtése.

Fókusz:

- auth;
- felhasználók;
- szerepkörök;
- admin alapok;
- alap layout és navigation;
- audit minimum.

Kilépési feltétel:

- role-alapú belépés és hozzáférés működik;
- admin képes usert és role-t kezelni.

## 5. Phase 2: Import Foundation

Cél:

- megbízható Mobdata adatbehozatal.

Fókusz:

- fájlimport;
- parsing;
- validáció;
- deduplikáció;
- import naplózás;
- hibakezelés.

Kilépési feltétel:

- ismételhető importfolyamat működik;
- hibás input nem teszi használhatatlanná a rendszert.

## 6. Phase 3: Dashboard és Task Center

Cél:

- napi operatív és vezetői belépési pont kialakítása.

Fókusz:

- dashboard KPI-k;
- figyelmeztetések;
- saját task lista;
- automatikus task generálás első szabályai.

Kilépési feltétel:

- a szerepkörök saját napi munkanézetet kapnak;
- a rendszer képes akcióvá alakítani egyes üzleti eseményeket.

## 7. Phase 4: RMA Operational Module

Cél:

- napi használatra alkalmas reklamációkezelés.

Fókusz:

- meglévő Laravel RMA felmérése;
- integráció vagy migrációs döntés;
- RMA lista, részletező, státuszok;
- határidők és dokumentumhiány kezelése;
- task integráció.

Kilépési feltétel:

- az ügyfélszolgálat napi munkára tudja használni a modult.

## 8. Phase 5: Sales és Product alapréteg

Cél:

- az importált adatokból érdemi üzleti nézetek képzése.

Fókusz:

- sales összesítések;
- SKU szintű nézetek;
- trendek;
- készlet és értékesítés közös értelmezése.

Kilépési feltétel:

- vezetői és beszerzési szempontból hasznos alap elemzések elérhetők.

## 9. Phase 6: Inventory és beszerzési támogatás

Cél:

- készletkockázat és utánrendelési logika támogatása.

Fókusz:

- out-of-stock kockázat;
- overstock jelzések;
- beszállítói és beszerzési nézetek;
- újrarendelési javaslatok.

## 10. Phase 7: Marketing és Finance bővítés

Cél:

- üzleti eredmény teljesebb képének felépítése.

Fókusz:

- marketing költés vs eredmény;
- marginok és díjak;
- jóváírási és pénzügyi kontrollnézetek.

## 11. Phase 8: AI Decision Center

Cél:

- napi vezetői ajánlások és anomáliafigyelés.

Fókusz:

- automatikus összefoglalók;
- kockázatok;
- lehetőségek;
- human-in-the-loop jóváhagyás.

## 12. Fázisok közötti függőségek

1. Phase 0 eredeti célja a dokumentációs alap lezárása volt; a Laravel alapfejlesztés 2026-07-28-án elindult, ezért Phase 0 most párhuzamos dokumentációs stabilizálásként kezelendő
2. Phase 1 szükséges minden további modulhoz
3. Phase 2 szükséges minden adatvezérelt modulhoz
4. Phase 3 alapot ad az operatív használhatósághoz
5. Phase 4 az első teljes operatív domain modul
6. Phase 5-8 fokozatos üzleti mélyítés

## 13. Minden fázis kötelező definíciója

Minden fázishoz külön rögzíteni kell:

- scope;
- non-scope;
- érintett modulok;
- érintett szerepkörök;
- függőségek;
- acceptance criteria;
- technikai kockázatok;
- üzleti kockázatok.

## 14. Azonnali következő lépések

1. USER_ROLES véglegesítése
2. MVP_SCOPE véglegesítése
3. FUNCTIONAL_SPEC kidolgozása a Dashboard, Task Center és RMA modulokra
4. DATA_MODEL MVP entitásainak részletezése
5. SYSTEM_ARCHITECTURE stack döntéseinek meghozása
