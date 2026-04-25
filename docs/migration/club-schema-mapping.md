# Club Schema Migration Mapping

## Ziel

Dieses Dokument beschreibt das technische Mapping von einem älteren, installationspezifischen Schema auf ein neutrales Club-Schema, damit Dynamic Tags, MetaBox-Exporte und Datenbankinhalte konsistent migriert werden koennen.

## Backup-Stand

- `MB_Views-backup-20260425-221500`
- `MB_FieldGroups-backup-20260425-221500`

Diese Backups sind der Rueckfallpunkt vor der technischen Umstellung.

## 1) Settings Page

- `OLD_OPTION_SLUG` (bisheriger `option_name` in `wp_options`; Referenz: README) -> `club-einstellungen`
- `option_name: OLD_OPTION_SLUG` -> `option_name: club-einstellungen`

Datei:
- `MB_Settings_Page/club-einstellungen.json`

## 2) Field Groups (Dateien)

- `kontaktinformationen.json` -> `club-contact.json`
- `vereine.json` -> `clubs.json`
- `sponsoren.json` -> `partners.json`
- `spieler.json` -> `players.json`
- `mannschaft.json` -> `teams.json`
- `spielplan-erste.json` -> `schedule-team-1.json`
- `spielplan-zweite.json` -> `schedule-team-2.json`
- `spielplan-teams.json` -> `schedule-clubs.json`

## 3) Wichtige Meta Keys / IDs

### Spielplan Alt -> Neu

- `erste_mannschaft` -> `team_1_matches`
- `zweite_mannschaft` -> `team_2_matches`
- `date_nextmatch_erste` -> `match_datetime_team_1`
- `date_nextmatch_zweite` -> `match_datetime_team_2`
- `Liga_erste` -> `league_team_1`
- `Liga_zweite` -> `league_team_2`
- `heimmannschaft_erste` -> `home_team_1`
- `heimmannschaft_zweite` -> `home_team_2`
- `auswaerts_erste` -> `away_team_1`
- `auswaerts_zweite` -> `away_team_2`
- `ergebnis_heim_erste` -> `home_score_team_1`
- `ergebnis_heim_zweite` -> `home_score_team_2`
- `ergebnis_auswarts_erste` -> `away_score_team_1`
- `ergebnis_auswarts_zweite` -> `away_score_team_2`

### Einheitlicher Spielplan Alt -> Neu

- `spieltage_teams` -> `club_matchdays`
- `date_nextmatch` -> `match_datetime`
- `liga` -> `league`
- `heimmannschaft` -> `home_team`
- `auswaerts` -> `away_team`
- `ergebnis_heim` -> `home_score`
- `ergebnis_auswaerts` -> `away_score`

### Weitere wichtige Felder Alt -> Neu

- `adresse_sportplatz` -> `sports_ground_address`
- `kontaktinformationen` -> `club_contact`
- `offizielle_vereinsadresse` -> `official_address`
- `adresse_offiziell` -> `official_address_text`
- `vereinsheim` -> `clubhouse`
- `adresse_vereinsheim` -> `adresse_clubhouse` (WYSIWYG im Gruppe `clubhouse`)
- `soziale_medien` -> `social_links`
- `mannschaft` -> `teams`
- `mannschaft_kategorie` -> `team_category`
- `spieler` -> `players`
- `spielerfoto` -> `player_photo`
- `spieler_switch` -> `is_player`
- `vorstand_switch` -> `is_board_member`
- `vorstandsfoto` -> `board_member_photo`
- `vorstandsposition` -> `board_role`
- `vorstand_adresse` -> `board_address`
- `email_vorstand` -> `board_email`
- `telefon_vorstand` -> `board_phone`
- `mobil_vorstand` -> `board_mobile`
- `sponsoren` -> `partners`
- `sponsoren_logo` -> `partner_logo`
- `sponsorentyp` -> `partner_type`
- `link_webseite` -> `website_url`
- `vereine` -> `clubs`

## 4) Taxonomies und Relationships

### Taxonomies

- `vorstandsposition` -> `board_role`
- `sponsorentyp` -> `partner_type`
- `spielerposition` -> `player_position`

Dateien:
- `MB_Taxonomies/board-role.json`
- `MB_Taxonomies/partner-type.json`
- `MB_Taxonomies/player-position.json`

### Relationship

- `mannschaft-spieler` -> `teams-players`
- `post_type: mannschaften` -> `post_type: teams`

Datei:
- `MB_Relationships/teams-players.json`

## 5) Views

Alle Views wurden auf die neuen Keys umgestellt. Besonders relevant:

- `team-1-letzte-spiele.json`
- `team-1-weitere-spiele.json`
- `team-2-letzte-spiele.json`
- `team-2-weitere-spiele.json`
- `naechstes-spiel-hero-team-1.json`
- `spielplan-letzte-spiele.json`
- `spielplan-weitere-spiele.json`
- `spielplan-naechstes-spiel-hero.json`

Hinweis:
- Settings-Zugriff erfolgt ueber `attribute(site, 'club-einstellungen')`.

## 6) DB-Migration per SSH (Vorschlag)

Vorher:
1. DB-Backup anlegen
2. Testlauf zuerst auf Staging

Danach gezielt in `wp_postmeta`, `wp_options` und ggf. `wp_posts.post_content` ersetzen.

Beispiel (WP-CLI Search/Replace, trocken):

```bash
# OLD_OPTION_SLUG = in der Zieldatenbank den echten bisherigen Options-Namen einsetzen (siehe README)
wp search-replace 'OLD_OPTION_SLUG' 'club-einstellungen' --all-tables --dry-run
wp search-replace 'erste_mannschaft' 'team_1_matches' --all-tables --dry-run
wp search-replace 'zweite_mannschaft' 'team_2_matches' --all-tables --dry-run
```

Wenn das Ergebnis plausibel ist, `--dry-run` entfernen.

## 7) Reihenfolge fuer den Umzug

1. Neue Taxonomies importieren
2. Neue Settings Page importieren
3. Neue Field Groups importieren
4. Neue Relationships importieren
5. Views importieren
6. DB Search/Replace gemaess Mapping ausfuehren
7. Frontend + Dynamic Tags pruefen (Teamseiten, Hero, Kontakt, Sponsor/Partner, Vorstand)

## 8) Review (Export-Stand) – Korrekturen & Hinweise

Diese Anpassungen sind im aktuellen Repo-Stand umgesetzt und sollten bei einer DB-Migration ggf. mitgenommen werden.

- **Spielplan-Ergebnis (Auswärts):** technischer Key einheitlich `away_score` (kein Mischform wie `ergebnis_away_*`).
- **Partnertyp-Taxonomie:** Slug in `MB_Taxonomies/partner-type.json` ist `partner_type` (nicht `Sponsorentyp`); Field Group `partners` referenziert `partner_type`.
- **Clubheim-Feld:** im Kontakt-Export lautet die ID des WYSIWYG-Felds `adresse_clubhouse` (Views: `group.clubhouse.adresse_clubhouse`).
- **Mannschaften-Field-Group:** `post_types` muss `teams` lauten (Korrektur eines Tippfehlers `teamsen`); Konsistenz mit `MB_Relationships/teams-players.json`.
- **Spieler-Position-Taxonomie:** Slug laut `player-position.json` ist `playerposition` – in `players.json` muss das Feld `position` dieselbe Taxonomie referenzieren.
- **Vorstand:** erstes WYSIWYG-Feld = Adresse, separates Feld = E-Mail (`board_email`); Labels angepasst.
- **Einstellungsseite:** `menu_title` neutral (`Club-Einstellungen`); **Tabs** nur noch `Kontakt` + `Spielplan` (keine getrennten Reiter „Erste/Zweite“ mehr).
- **MB Views Shortcodes** in `teams.json`: Platzhalter z. B. `[mbv name="spielplan-weitere-spiele"]` (vor Import prüfen, ob der **post_name** in WordPress exakt so lautet – ggf. im MB-View-Screen den Shortcode kopieren).
- **E-Mail-Label in Views:** einheitlich „E-Mail:“ statt „Email:“ in `wichtige-adressen` und `impressum`.
