# Code-basierte Registrierung (mu-plugin)

Dieses Repo ist die **Source of Truth** für das Club-Schema. Die Feldgruppen und
die Einstellungsseite werden **per Code** registriert — der von Meta Box offiziell
empfohlene Weg für versionierbare Konfiguration
(siehe <https://docs.metabox.io/creating-fields-with-code>), statt den Zustand nur
im Meta Box Builder (DB) zu halten.

## Loader

`mu-plugin/fck-club-meta-box.php` registriert beim Laden:

- **Einstellungsseite(n)** aus `MB_Settings_Page/*.json` via `mb_settings_pages`
- **alle Feldgruppen** aus `MB_FieldGroups/*.json` via `rwmb_meta_boxes`
  (REST wird pro Feld erzwungen: `hide_from_rest = false`)

Der Loader findet die JSON-Verzeichnisse automatisch in zwei Layouts:

1. **Server (mu-plugin):** `wp-content/mu-plugins/fck-club-meta-box/MB_FieldGroups/…`
2. **Repo:** `MB_FieldGroups/…` relativ zum Repo-Root

## Deployment

```bash
# Loader nach mu-plugins kopieren
cp mu-plugin/fck-club-meta-box.php   WP/wp-content/mu-plugins/fck-club-meta-box.php
# JSON-Bundle daneben legen
mkdir -p WP/wp-content/mu-plugins/fck-club-meta-box
cp -r MB_FieldGroups MB_Settings_Page WP/wp-content/mu-plugins/fck-club-meta-box/
```

mu-plugins werden automatisch geladen — keine Aktivierung nötig.

## Feldgruppen (Stand)

| Datei | Gruppe | Ziel (post_type / settings_page) |
|------|--------|----------------------------------|
| `teams.json` | teams (Mannschaft) | `mannschaften` (inkl. `fussball_de_team_id`) |
| `fussball-de-sync.json` | fussball.de Sync-Daten | `mannschaften` (`fd_*`) |
| `players.json` | players (Mitglieder) | `mitglieder` |
| `clubs.json` | clubs (Vereine) | `vereine` |
| `partners.json` | partners (Sponsoren) | `sponsor` |
| `club-contact.json` | Kontakt/Branding/Social | Settings `club-einstellungen` (Tab Kontakt) |
| `schedule-clubs.json` | Spielplan | Settings `club-einstellungen` (Tab Spielplan) |

## Wichtig: Live-CPT-Slugs sind deutsch

Die im alten Migrationsplan vorgesehene CPT-Umbenennung (`mannschaften→teams`,
`vereine→clubs`) wurde **nie ausgeführt**. Die `post_types` in den JSONs müssen
daher die **echten** Slugs treffen: `mannschaften`, `vereine`, `sponsor`,
`mitglieder` — sonst meldet die Meta-Box-REST-API `field_not_exists`.

## Migrationshinweis (2026-06-06)

Die zuvor im **Meta Box Builder (DB)** gepflegten Gruppen (teams/players/clubs/
partners) sowie zwei Einzel-mu-plugins wurden durch diesen Loader ersetzt; die
DB-Gruppen wurden nach Voll-Backup entfernt. Logo + Social-Media-Daten wurden aus
dem alten Options-Backup (`_fck_backup_fc_koenigsfeld`) nach `club-einstellungen`
übernommen.
