# SHOPLINK Intelligence – GitHub és staging workflow

## Források és útvonalak

- GitHub: `git@github.com:gudimusic-dotcom/shoplink-intelligence.git`
- Helyi projekt: `$HOME/Projects/Shoplink Intelligence`
- Laravel alkalmazás helyben: `platform/`
- Staging: `https://shoplinkstage.1web.sk`
- A Websupport staging nem Git checkout; a GitHub és a staging külön célpont.
- GitHub push után a staging nem frissül automatikusan.

## Parancsok, amelyek elégségesek a Codex kéréséhez

### Munka kezdése bármely gépen

Git csak az aktuális terminálmappa `.git` könyvtárából tudja, melyik projekten dolgozol. Ezért mindig először lépj be a projektbe, majd frissítsd a távoli állapotot:

```bash
cd "$HOME/Projects/Shoplink Intelligence"
git fetch origin
git pull --ff-only origin main
```

Ha a `pull --ff-only` megáll, ne dolgozz régi verzióból és ne használj `git push --force` parancsot.

### „Stiahni zmeny”

```bash
cd "$HOME/Projects/Shoplink Intelligence"
git status --short --branch
git fetch origin
git pull --ff-only origin main
```

Ha van helyi módosítás vagy konfliktusveszély, állj meg és jelezd; ne használj `reset --hard` parancsot.

### „Uploadni zmeny” / „Töltsd fel GitHubra”

Ellenőrzés után checkpoint commit és GitHub push:

```bash
cd "$HOME/Projects/Shoplink Intelligence"
./scripts/finish-work.sh "Rövid változásleírás"
git push origin main
git status --short --branch
git log -1 --oneline
```

Normál esetben a `git push` régi lokális verziót automatikusan elutasít. `git push --force` tilos; csak külön megerősített helyreállításkor használható.

### „Nasadiť na staging” / „Uploadni na Websupport”

Ez külön művelet a GitHub push után. Stagingre csak akkor tölts, ha ezt külön kéred.

Először száraz futtatás:

```bash
cd "$HOME/Projects/Shoplink Intelligence"
./scripts/deploy-shoplinkstage.sh --dry-run
```

Ezután a deploy scriptet használd; backupot készít, feltölt és Laravel cache-t töröl:

```bash
cd "$HOME/Projects/Shoplink Intelligence"
./scripts/deploy-shoplinkstage.sh
```

Ha a script hibát jelez, ne használj `--force` opciót ellenőrzés nélkül.

## GitHub SSH otthoni gépen

Ha a `git clone` `Permission denied (publickey)` hibát ad, a kulcsot explicit módon kell megadni:

```bash
mkdir -p "$HOME/Projects"
GIT_SSH_COMMAND="ssh -i $HOME/.ssh/github_rma_ed25519 -o IdentitiesOnly=yes" \
git clone git@github.com:gudimusic-dotcom/shoplink-intelligence.git \
"$HOME/Projects/Shoplink Intelligence"
```

A `~` idézőjelben nem bővül ki; ezért használjuk a `$HOME` változót.

Ha a célmappa már létezik és nem üres, nem kell újra klónozni:

```bash
cd "$HOME/Projects/Shoplink Intelligence"
git pull --ff-only origin main
```

## Websupport SSH adatok

```bash
ssh -i "$HOME/.ssh/rma_websupport_ed25519" \
  -p 27498 uid118160@shell.r5.websupport.sk
```

- Staging app root: `/data/5/f/5f2292be-4c6a-4169-bdd2-f289e9ba12b2/1web.sk/sub/shoplinkstage`
- A távoli app rootban nincs `platform/` almappa.
- Blade/PHP módosítás után Laravel cache-t kell törölni.
- Staging ellenőrzése: `https://shoplinkstage.1web.sk`.

## Amit soha ne commitolj

- `.env`
- jelszavak, tokenek és privát SSH-kulcsok
- `vendor/`, `node_modules/`, runtime adatok és logok
- staging adatbázis vagy feltöltött importfájlok
