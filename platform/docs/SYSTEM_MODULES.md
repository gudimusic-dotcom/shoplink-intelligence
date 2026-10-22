# System Modules

Tento dokument popisuje hlavne moduly SHOPLINK Intelligence na urovni produktu.

## 1. Dashboard

Startovacia obrazovka systemu podla roly pouzivatela.

Obsahuje:

- dnesne ulohy
- urgentne upozornenia
- klucove KPI
- aktivne problemy
- skratky do najdolezitejsich casti systemu

## 2. Intelligence

Analyticka a rozhodovacia vrstva systemu.

Jej ulohou je prepajat data a hladat:

- anomalie
- odchylky
- rizika
- prilezitosti
- odporucania pre dalsi krok

## 3. RMA

Kompletne riadenie reklamacneho cyklu od prijatia po uzavretie.

## 4. Task Center

Operativne centrum uloh.

Zahrna:

- manualne vytvorene ulohy
- automaticky generovane ulohy
- prioritizaciu
- terminy
- zodpovedne osoby

Task Center je miesto na riesenie prace. Nie je to miesto, kde sa definuju automaty alebo monitorovacie pravidla.

## 5. Sales Intelligence

Prehlad predaja podla:

- krajin
- portalov
- marketplace kanalov
- obdobia

## 6. Product Intelligence

Produktovy pohlad na SKU, vykonnost, marzu, reklamacie, zasoby a zivotny cyklus.

## 7. Inventory and Procurement

Modul pre zasoby, nakup a rozhodovanie o doobjednavkach.

## 8. Marketing Intelligence

Vyhodnotenie marketingu z obchodneho pohladu, nie len z pohladu klikov.

## 9. Finance Insight

Prehlad marzi, nakladov, poplatkov a dalsich financnych suvislosti.

## 10. Users and Permissions

Sprava pouzivatelov, roli a opravneni.

## 11. Settings and Integrations

Nastavenia systemu, importov, mapovani, kurzov, zdrojov dat a integracii.

## 12. Monitors

Monitors su samostatny modul pre male automatizovane sledovacie scenare.

Kazdy monitor ma:

- nazov
- typ monitoru
- vstupne data, napr. URL alebo identifikator produktu
- podmienku vyhodnotenia
- interval kontroly
- akciu po splneni podmienky
- historiu kontrol a vysledkov

### Priklad prveho use case

`Alza price monitor`

Pouzivatel zada URL produktu. Monitor v intervale kontroluje cenu a ak sa cena znizi oproti poslednej znamej hodnote, system vykona akciu:

- posle email
- vytvori alert
- volitelne vytvori task

### Preco je to samostatny modul

Monitors nie su obycajne tasky.

Task je vysledok alebo nasledna akcia. Monitor je mechanizmus, ktory sam sleduje stav a generuje reakciu.

### Dlhsia vizia

Rovnaky model sa neskor da pouzit aj na:

- monitoring cien konkurencie
- monitoring skladovosti
- kontrolu marketplace zmien
- sledovanie neaktivnych reklamacii
- sledovanie KPI odchylok

Monitors su teda zaklad buduceho automation engine, ale na produktovej urovni ostavaju zrozumitelne ako jednoduche sledovacie moduly.
