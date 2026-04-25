# wordpress-club-meta-box

Meta-Box- und **MB Views**-JSON-Exporte für WordPress: Feldgruppen, Einstellungsseiten, Views, Beziehungen, Taxonomien. Aufbau und Pflege einer **Vereins-Website** (Amateurfußball) mit dem [Meta-Box-Ökosystem](https://metabox.io/).

Die technischen Bezeichnungen nutzen ein **generisches Club-Schema** (z. B. Option `club-einstellungen`, neutrale Slugs), damit die Konfiguration wiederverwendbar bleibt.

## Verein & Live-Seite

| | |
|---|---|
| **Verein** | [FC Königsfeld 1954 e.V.](https://fc-koenigsfeld.de/) |
| **Website** | <https://fc-koenigsfeld.de/> |

Die Exporte in diesem Repository wurden **für diese Website** entwickelt. Vereins- und Ortsbezeichnungen stehen in der **README** bzw. in eurer fachlichen Doku; im übrigen Code und in den Migrations-Beispielen bleibt die Benennung bewusst neutral.

## Inhalt (Ordner)

| Ordner | Inhalt (Kurz) |
|--------|----------------|
| `MB_FieldGroups/` | Feldgruppen (CPT, Kontakt, Spielplan, …) |
| `MB_Views/` | MB Views (Twig), u. a. Spielplan, Logos, Adressen |
| `MB_Settings_Page/` | Einstellungsseite (Club-Daten, Spielplan) |
| `MB_Post_Types/`, `MB_Taxonomies/`, `MB_Relationships/` | Post Types, Taxonomien, Beziehungen |
| `docs/` | Technische Doku, Migration, SSH-Checklisten |
| `archive-old/` | ältere Export-Sicherungen (Referenz) |

## Doku in diesem Repo

- [MB Views: Spielplan (Twig)](docs/mb-views-spielplan-twig.md)
- [Club-Schema-Mapping (Migration)](docs/migration/club-schema-mapping.md)
- [SSH / DB-Migration](docs/migration/ssh-migration-checklist.md)

## Migration von einer älteren WordPress-Installation

Wenn die Einstellungsseite in der DB noch unter einem **alten Options-Slug** steckt: In der ursprünglichen zu diesem Projekt gehörenden Installation lautete der frühere `option_name` typischerweise

`fc-koenigsfeld` → Ziel: `club-einstellungen`

Vor `wp search-replace` **Backup** anlegen und echte Schlüssel in `wp_options` prüfen. In den Doku-Beispielen steht dafür der Platzhalter `OLD_OPTION_SLUG`.

## Voraussetzungen

- WordPress, [Meta Box](https://metabox.io/) (je nach Setup Meta Box Pro, AIO, MB Views, …) gemäß eurer lokalen/Server-Konfiguration
- Lizenzen der genutzten Produkte und Themes einhalten

## Lizenz

Nutzung im Vereinskontext; Anpassung und Wiederverwendung im Rahmen eurer rechtlichen Möglichkeiten. Es gibt kein besonderes Open-Source-Lizenzmodell für dieses Repo, sofern nicht später ergänzt.
