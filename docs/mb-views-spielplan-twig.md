# MB Views: Spielplan (Twig) – Struktur & Wartung

Diese Seite erklärt die **logische Kette** der Spielplan-Views, die **Feld-Abhängigkeiten** und wie **Wiederverwendung** (Partial) + **dynamische Team-Zuordnung** funktionieren. Technische Meta-Box-Hintergründe: [MB Views](https://docs.metabox.io/extensions/mb-views), Template-Parts: [Creating reusable template parts](https://docs.metabox.io/tutorials/create-reusable-template-parts).

## Beteiligte Dateien (Export)

| Datei / View | Rolle |
|--------------|--------|
| `MB_Views/partial-club-team-logo.json` | **Nur** Club-Logo. Wird per `{{ include('partial-club-team-logo') }}` eingebunden. **Slug in WP muss** `partial-club-team-logo` sein (aus Titel *Partial - Club Team Logo* generiert; nach Import in WP ggf. prüfen). |
| `MB_Views/spielplan-weitere-spiele.json` | Kommende Spiele (gefiltert, sortiert) |
| `MB_Views/spielplan-letzte-spiele.json` | Vergangene Spiele mit Ergebnis |
| `MB_Views/spielplan-naechstes-spiel-hero.json` | Nächstes Spiel + optional Sportplatz |
| `MB_FieldGroups/schedule-clubs.json` | Daten: `club_matchdays` unter **Club-Einstellungen** (Option) |
| `MB_FieldGroups/teams.json` | Pro **Team-Beitrag (CPT `teams`)**: Feld **`match_team_key`** muss zu den Werten im Spielplan (Feld `team` pro Eintrag) passen. |

## Datenfluss (Kurz)

1. **Einstellungen-Seite** `club-einstellungen` → Gruppe/Clone **`club_matchdays`**: viele Spiele, jedes mit `team`, `match_datetime`, `league`, `home_team`, `away_team`, `abgesagt`, Tore, …
2. **View** liest: `group = attribute(site, 'club-einstellungen')`, dann `all_matches = group.club_matchdays`.
3. **Filter `team_slug`:** es werden nur Zeilen verarbeitet, deren `team` (Select-Wert) gleich `team_slug` ist.
4. **Woher `team_slug`?**  
   - Auf einer **Single-Team-Seite** (Post Type `teams`): aus **`post.match_team_key`** (Meta-Box-Feld, gleiche Schlüssel wie im Spielplan: `erste`, `zweite`, …).  
   - **Ohne** passenden `post` (z. B. View nur auf der Startseite): Fallback **`erste`**. Dann: **eigenes View-Duplikat** anlegen, das `team_slug` fest setzt, **oder** die Startseite anders lösen (z. B. festes zweites View pro Mannschaft).
5. **Hero + „Weitere Spiele“ (nur `erste`):** Der View `spielplan-naechstes-spiel-hero` nutzt für die 1. Mannschaft dasselbe sortierte, zukünftige Spiel (`matches|first`) wie der Beginn der Liste. In **`spielplan-weitere-spiele`** ist `start_offset = 1`, wenn `team_slug == 'erste'`, damit **dieses** erste Spiel nicht doppelt erscheint. Andere `team_slug`-Werte beginnen die Liste bei **0** (es gibt dafür keinen Hero auf derselben Seite, oder man akzeptiert ggf. eine Überschneidung — Anpassung dann nötig).

## CSS-Klassen (Spielplan) & A11y (Stand 2026)

**BEM-artig** unter dem Präfix **`match-`** (Teamzeilen, Listen, Karten):

| Block / Element | Kurzbeschreibung |
|------------------|------------------|
| `match-list-wrap` | äußerer umgebender Block für die Listen-Views |
| `match-list` | `<ul role="list">` kommende/vergangene Spiele |
| `match-list__item` | Zeile, enthält meist `match-card` |
| `match-card` / `match-card--past` | Ein Spiel; `--past` bei „Letzte Spiele“ |
| `match-card__head`, `__league`, `__when` | Kopf: Liga, Datum in `<time datetime="…">` (ISO) |
| `match-lineup` | Dreier-Zeile Heim / Mitte / Auswärts, `role="group"` + `aria-label` |
| `match-lineup__side--home` / `--away` | Spalte je Team, darin `match-lineup__name` + Logo |
| `match-lineup__center` | „vs“ oder Ergebnis (`match-lineup__score`) bzw. `…__canceled` |
| `match-team-logo` | Club-Logo (Partial), sinnvolles `alt` |
| `match-hero-wrap` | äußerer Block um den Hero-View (Layout- und Theme-Seite) |
| `match-hero` | Sektion „Nächstes Spiel“ mit `h2`, `match-hero__details`, ggf. `…--empty` + `role="status"` |

**Theme/Child-Theme:** [`assets/css/match-hero.css`](../assets/css/match-hero.css) (nächstes Spiel), [`assets/css/match-lists.css`](../assets/css/match-lists.css) (weitere/letzte Spiele) per `wp_enqueue_style` o. ä. Optional **Screen-Reader-Only** z. B. mit `.match-sr-only` { clip / absolute / 1px } ergänzen, falls ihr kompakte sichtbare Labels wollt; im Hero sind Liga/Anstoß/Ort sichtbar beschriftet.

## Partial: `partial-club-team-logo`

- **Einstieg:** in der Meta-Box-Doku heißt es `{{ include('view-slug') }}`, wobei der **Slug** dem View in WordPress entspricht.
- **Vor** jedem `include` setzen: `{% set team_post = … %}` (Club-Post-Objekt).
- **Optional:** `logo_max_width`, `logo_max_height`, `logo_loading`, `logo_decoding`, `logo_fetchpriority`, `logo_extra_class` – siehe Kommentar im Partial selbst.
- **Bilder:** `medium_large`, Fallback `medium`; kein Bild, wenn keine URL (kein kaputter `img`). Klasse **`match-team-logo`**, `alt` aus Medien-Alt oder Fallback „Vereinslogo: {Name}“.

## „Weitere“ vs. „Letzte“ Spiele – zusammenfassen?

**Empfehlung: getrennt lassen** (zwei Views).

| Getrennt (aktuell) | Eine View mit Parameter/Modus |
|--------------------|----------------------------------|
| Zwei Shortcodes, zwei klare Einsatzzonen (oben / unten, Spalte A / B). | Ein Template, aber **zwei Modi** (z. B. `zukünftig` / `vergangen`) = mehr Logik, mehr Fehlerquellen, schwerer zu lesen. |
| Unterschiedliches Markup (nur `vs` vs. Ergebnis, ggf. andere Anzahl) bleibt übersichtlich. | Wenn du „alles in einem“ willst, brauchst du Flags/Slices und Kommentare – machbar, aber **weniger** flexibel fürs reine **Platzieren in Elementor** / mehreren Stellen. |

**Semantik:** *„Letzte Spiele“* = die letzten *N* **Ergebnisse** (hier: vergangen, sortiert). *„Vergangene Spiele“* wäre sprachlich oft dasselbe; wenn ihr irgendwann *alle* vergangenen braucht (Paginierung), wäre das ein **dritter** View oder Archiv-Query – nicht zwingend mit „Letzte“ vermischen.

**Fazit:** Getrennt ist für Layout und Wartung besser; du platzierst die Shortcodes frei, wie du willst.

## Switch `abgesagt`

- Speicherung laut Doku: `1` / `0`. Prüfung: `{% if clone.abgesagt %}` (truthy) – siehe [Switch field](https://docs.metabox.io/fields/switch).

## Checkliste nach Import in WordPress

- [ ] View-Slug `partial-club-team-logo` existiert und `include('partial-club-team-logo')` wirft keinen Fehler.
- [ ] Pro Team-Beitrag `match_team_key` gesetzt und mit Spielen unter Club-Einstellungen abgestimmt.
- [ ] Shortcodes `[mbv name="spielplan-weitere-spiele"]` etc. mit **echten** View-Namen aus dem Backend abgleichen.
