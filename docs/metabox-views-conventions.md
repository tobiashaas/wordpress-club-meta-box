# Meta Box & MB Views – Konventionen (Wartbarkeit)

Diese Notizen stützen sich auf die offizielle Meta-Box-Dokumentation ([docs.metabox.io](https://docs.metabox.io/), u. a. Index [Context7 / Meta Box](https://context7.com/websites/metabox_io)).

## Switch-Feld (z. B. `abgesagt`)

- Laut [Switch field – Data](https://docs.metabox.io/fields/switch): Speicherung als **1** (an) bzw. **0** (aus).
- Die Doku zeigt die Auswertung per **truthy check** mit `rwmb_meta()` (analog in Twig: `{% if clone.abgesagt %}`), nicht zwingend `=== 1`.
- In den Spielplan-Views ist deshalb `{% if clone.abgesagt %}` mit kurzem Verweis-Kommentar auf die Doku-URL gesetzt.

## `rwmb_meta` / Rückgabewerte

- [rwmb_meta – Returned value](https://docs.metabox.io/functions/rwmb-meta): Das Format hängt vom Feldtyp ab; bei einfachen Werten entspricht es `rwmb_get_value()`.

## Skalierbarkeit (Kurzüberblick)

| Thema | Empfehlung |
|--------|------------|
| **Ein View pro Team** vs. **Parameter** | Für wenige Teams: Duplikate mit nur `team_slug` sind oft klarer als ein komplexer „Universal-View“. Viele Teams: zentrale Steuerung (z. B. Kontext-Feld auf der Seite), sofern MB/Builder das sauber hergibt. |
| **Wiederholtes Markup (Logos)** | Optional ein `{% macro logo(team) %}` in einem gemeinsamen View-Part-Template, **wenn** eure MB-Views-Version Imports/Makros zuverlässig unterstützt. |
| **Feld-IDs** | Stabil halten; Umbenennungen nur mit DB-Migration / `search-replace`. |

## Weiterführend

- MB Views mit Twig: z. B. [Tutorial mit Elementor / Twig in Views](https://docs.metabox.io/tutorials/create-simple-listing-with-elementor) (Beispiele mit `mb.rwmb_meta(...)`).
