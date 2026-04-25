# SSH Migration Checkliste (DB + Dynamic Tags)

## Zweck

Diese Checkliste ist fuer den Umzug auf das neue Club-Schema gedacht.  
Sie basiert auf `docs/migration/club-schema-mapping.md`.

## Voraussetzungen

- SSH-Zugriff auf Staging/Backup-System
- WP-CLI verfuegbar (`wp --info`)
- Aktuelles DB-Backup vorhanden
- Neue JSON-Exporte bereits importiert (oder vorbereitet)

## 0) In WordPress Root wechseln

```bash
cd /pfad/zu/deiner/wordpress-installation
wp --info
```

## 1) Sicherheits-Backup vor Search/Replace

```bash
mkdir -p ./migration-backups
wp db export ./migration-backups/pre-club-schema-$(date +%Y%m%d-%H%M%S).sql
```

Optional mit Kompression:

```bash
gzip ./migration-backups/pre-club-schema-*.sql
```

## 2) Erst nur Dry-Run (Pflicht)

### 2.1 Settings Page / Options

```bash
# OLD_OPTION_SLUG: bisheriger option_name (README / wp_options) – nicht blind kopieren
wp search-replace 'OLD_OPTION_SLUG' 'club-einstellungen' --all-tables --precise --recurse-objects --dry-run
```

### 2.2 Spielplan alte Team-Keys -> neue Team-Keys

```bash
wp search-replace 'erste_mannschaft' 'team_1_matches' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'zweite_mannschaft' 'team_2_matches' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'date_nextmatch_erste' 'match_datetime_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'date_nextmatch_zweite' 'match_datetime_team_2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'Liga_erste' 'league_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'Liga_zweite' 'league_team_2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'heimmannschaft_erste' 'home_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'heimmannschaft_zweite' 'home_team_2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'auswaerts_erste' 'away_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'auswaerts_zweite' 'away_team_2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_heim_erste' 'home_score_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_heim_zweite' 'home_score_team_2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_auswarts_erste' 'away_score_team_1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_auswarts_zweite' 'away_score_team_2' --all-tables --precise --recurse-objects --dry-run
```

### 2.3 Einheitlicher Spielplan

```bash
wp search-replace 'spieltage_teams' 'club_matchdays' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'date_nextmatch' 'match_datetime' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'liga' 'league' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'heimmannschaft' 'home_team' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'auswaerts' 'away_team' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_heim' 'home_score' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'ergebnis_auswaerts' 'away_score' --all-tables --precise --recurse-objects --dry-run
```

### 2.4 Weitere zentrale Keys

```bash
wp search-replace 'adresse_sportplatz' 'sports_ground_address' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'kontaktinformationen' 'club_contact' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'offizielle_vereinsadresse' 'official_address' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'adresse_offiziell' 'official_address_text' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'vereinsheim' 'clubhouse' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'adresse_vereinsheim' 'adresse_clubhouse' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'soziale_medien' 'social_links' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'mannschaft_kategorie' 'team_category' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spielerfoto' 'player_photo' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spieler_switch' 'is_player' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'vorstand_switch' 'is_board_member' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'vorstandsfoto' 'board_member_photo' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'vorstandsposition' 'board_role' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'vorstand_adresse' 'board_address' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'email_vorstand' 'board_email' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'telefon_vorstand' 'board_phone' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'mobil_vorstand' 'board_mobile' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'sponsoren_logo' 'partner_logo' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'sponsorentyp' 'partner_type' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'link_webseite' 'website_url' --all-tables --precise --recurse-objects --dry-run
```

### 2.5 Post Types / Taxonomies / Relationship IDs

```bash
wp search-replace 'vereine' 'clubs' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'sponsoren' 'partners' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spieler' 'players' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'mannschaft' 'teams' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spielplan-erste' 'schedule-team-1' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spielplan-zweite' 'schedule-team-2' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spielplan-teams' 'schedule-clubs' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'mannschaft-spieler' 'teams-players' --all-tables --precise --recurse-objects --dry-run
wp search-replace 'spielerposition' 'playerposition' --all-tables --precise --recurse-objects --dry-run
```

Hinweis: Taxonomie-Slugs sind case-sensitiv. Falls in der DB noch `Sponsorentyp` (Grossschreibung) vorkommt, zusaetzlich:

```bash
wp search-replace 'Sponsorentyp' 'partner_type' --all-tables --precise --recurse-objects --dry-run
```

## 3) Live-Lauf (nach Dry-Run-Pruefung)

Wenn die Dry-Run-Zahlen plausibel sind, dieselben Befehle ohne `--dry-run` ausfuehren.

Tipp: In Bloecken arbeiten (2.1, 2.2, 2.3, ...), nicht alles auf einmal.

## 4) Cache leeren

```bash
wp cache flush
```

Falls im Einsatz: Object Cache / Page Cache / CDN zusaetzlich leeren.

## 5) Technische Nachkontrolle per CLI

Resttreffer auf alte Schluessel pruefen:

```bash
wp db query "SELECT COUNT(*) AS c FROM wp_postmeta WHERE meta_key IN ('erste_mannschaft','zweite_mannschaft','date_nextmatch_erste','date_nextmatch_zweite','Liga_erste','Liga_zweite','heimmannschaft_erste','heimmannschaft_zweite','auswaerts_erste','auswaerts_zweite');"
```

Optional Resttreffer in `post_content`/Builder-Daten:

```bash
wp db query "SELECT ID, post_type, post_status FROM wp_posts WHERE post_content LIKE '%erste_mannschaft%' OR post_content LIKE '%zweite_mannschaft%' LIMIT 50;"
```

## 6) Fachliche Nachkontrolle im Frontend/Backend

- Teamseiten Team 1 / Team 2 / zusaetzliche Teams
- Hero-Widget `Nächstes Spiel`
- Weitere/Letzte Spiele Listen
- Kontakt/Club-Daten
- Partner/Sponsoren Bereich
- Vorstand/Board-Rollen
- Elementor/Builder Dynamic Tags

## 7) Rollback (wenn etwas nicht passt)

```bash
wp db import ./migration-backups/pre-club-schema-YYYYMMDD-HHMMSS.sql
wp cache flush
```

## 8) Empfohlene Reihenfolge fuer produktiven Umzug

1. Auf Staging testen
2. Dry-Run Ergebnisse dokumentieren
3. Live-Lauf auf Staging
4. Fachlicher Test + Abnahme
5. Gleiches Vorgehen Produktion in Wartungsfenster
