# Project Principles

Tento dokument je "ustava" projektu SHOPLINK Intelligence.

## Rozhodovacie pravidla

Kazda nova funkcia alebo modul musi prejst tymito otazkami:

- Zvysuje to zisk alebo kvalitu rozhodovania?
- Setri to cas pri kazdodennej praci?
- Znizuje to chyby alebo riziko zabudnutia?
- Je to jednoduchsie ako aktualny sposob prace?

Ak odpoved nie je jasne ano aspon v jednej dolezitej oblasti, funkcia nema ist do MVP.

## Povinne principy

- Business first.
- Jedno data source of truth pre kazdy typ udaja.
- Automatizacia vsade, kde ma realny prinos.
- Jednoduchost pred komplikovanostou.
- Modulovy navrh s moznostou rastu.
- Rola urcuje, co pouzivatel vidi a co moze robit.
- Kazda cast systemu musi vediet samostatne vytvarat hodnotu.

## Co patri do MVP

Do MVP patri to, co:

- riesi realny denny problem vo firme
- vie byt pouzivane hned po nasadeni
- ma jasne meratelny prinos
- nevyzaduje neprimerane komplexne integracie na start

## Kedy povedat nie

Nova myslienka sa nema zaradit do aktualnej fazy, ak:

- je zaujimava, ale nema priameho vlastnika
- neprinasa okamzitu prakticku hodnotu
- pridava viac nastavovania ako uzitku
- zdvojuje funkciu, ktoru uz system ma inde
- patri skor do "nice to have" vrstvy ako do operativneho jadra

## Kvalitativne minimum pre kazdy modul

Kazdy modul musi mat:

- jasny ucel
- citatelne a rychle rozhranie
- rolu a opravnenia
- zrozumitelne stavy
- historiu alebo audit tam, kde to dava zmysel
- moznost neskorsieho rozsirenia bez rozbitia jadra

## Dokumentacna disciplina

Dokumentacia musi odrazat realny stav projektu.

Ak sa meni architektura, moduly alebo prevadzkove principy, dokumentacia sa ma upravit spolu so zmenou, nie neskor.
