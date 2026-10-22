# SYSTEM_ARCHITECTURE

Status: Active Draft

## 1. Cél

Ez a dokumentum a SHOPLINK Intelligence technikai architektúrájának döntési keretét adja meg.

Ez még nem végleges stack-kijelölés, hanem azokat a technikai területeket és kötelező döntéseket rögzíti, amelyek nélkül a fejlesztés nem indítható kontrolláltan.

## 2. Architektúra alapelvek

- moduláris rendszer;
- egyértelmű domain felelősségek;
- auditálható üzleti folyamatok;
- import-first adatbehozatal;
- szerepkör-alapú hozzáférés;
- automatizálható háttérfolyamatok;
- AI réteg, amely nem fekete dobozként működik.

## 3. Kötelező technikai döntési területek

### Frontend

Rögzítendő:

- választott framework;
- routing modell;
- state kezelés alapelve;
- táblázatos és dashboard komponens-stratégia;
- auth session kezelése;
- design system alapjai.

### Backend

Rögzítendő:

- alkalmazásréteg felépítése;
- modulhatárok;
- domain service és application service szabályok;
- API vagy server-rendered döntés;
- validációs és authorization réteg.

### Adatbázis

Rögzítendő:

- elsődleges adatbázis típusa;
- migrációs stratégia;
- audit és soft delete elvek;
- indexelési alapelvek;
- riportolási és operatív adatok együttélése.

### Import engine

Rögzítendő:

- file upload vagy ingest folyamat;
- parsing logika;
- validáció;
- deduplikáció;
- partial failure kezelés;
- import logolás és visszajátszhatóság.

### Jogosultsági rendszer

Rögzítendő:

- auth mechanizmus;
- RBAC modell;
- modul- és rekordszintű jogok;
- admin műveletek korlátozása.

### Háttérfolyamatok

Rögzítendő:

- queue vagy job rendszer;
- ütemezett feladatok;
- automatikus task generálás;
- riasztások előállítása;
- AI összefoglalók futtatása.

### AI réteg

Rögzítendő:

- milyen input adatokat használ;
- milyen output objektumokat hoz létre;
- hogyan lesz nyomon követhető a javaslat eredete;
- mikor kell emberi jóváhagyás.

### Integrációk

Első körben:

- Mobdata export import.

Később:

- marketplace API-k;
- marketing platformok;
- külső könyvelési vagy riporting rendszerek.

### Audit és naplózás

Rögzítendő:

- kritikus műveletek naplózása;
- import események naplózása;
- státuszváltozások követése;
- user action nyomkövetés.

### Biztonság

Rögzítendő:

- hozzáférésvédelem;
- érzékeny adatok kezelése;
- fájlfeltöltések kontrollja;
- inputvalidáció;
- audit és incidens-visszakereshetőség.

## 4. Javasolt magas szintű rendszerkép

Az MVP szinten várható fő rétegek:

1. webes felhasználói felület
2. backend alkalmazásréteg
3. relációs adatbázis
4. import feldolgozó réteg
5. background job réteg
6. notification és task generáló logika
7. AI recommendation réteg

## 5. Kritikus architekturális nyitott kérdések

- új rendszer készül teljesen, vagy meglévő Laravel komponensek újrahasznosulnak;
- monolit moduláris felépítéssel vagy korai szolgáltatás-szétválasztással érdemes indulni;
- mennyi analitikai számítás történik runtime és mennyi előfeldolgozva;
- hogyan különül el az operatív adat és a számolt insight adat;
- az AI javaslatok szinkron vagy aszinkron módon készülnek.

## 6. Fejlesztés előtti kötelező véglegesítések

- stack döntés;
- auth és RBAC döntés;
- import flow döntés;
- RMA integráció vagy migráció döntés;
- alap deployment irány;
- logging és monitoring minimum.
