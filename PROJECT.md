# BSI Drupal Project

Website for **BSI (Bundesamt für Sicherheit in der Informationstechnik)** — the German Federal Office for Information Security.

- **Drupal version:** 11.x
- **PHP:** >= 8.3
- **Base theme:** Claro (core admin theme)
- **Custom theme:** `bsi_bund`
- **Search backend:** Apache Solr (via search_api_solr)
- **Multilingual:** Yes (German primary, with translation infrastructure)
- **Composer repositories:** Private Artifactory at `artifacts.init.de` (no public packagist)

---

## Project Structure

```
├── composer.json / composer.lock
├── config/sync/                  # 1,346 config files
├── patches/                      # 43 patches across 24 contrib modules
├── web/
│   ├── modules/custom/           # 17 custom modules (all prefixed bsi_*)
│   └── themes/custom/bsi_bund/   # Custom theme (Vite + Storybook)
```

---

## Content Types (9)

| Machine name | Purpose |
|---|---|
| `page` | Standard pages |
| `article` | News articles |
| `event` | Events |
| `job` | Job postings |
| `certificate` | IT security certificates |
| `it_security_label` | IT security labels |
| `entry_page` | Entry/landing pages |
| `inventory` | Inventory items |
| `report_page` | Annual IT security report pages (book-based) |

## Paragraph Types (17)

`accordion`, `banner`, `contact`, `definition_item`, `downloads`, `facts_figures`, `introduction`, `key_facts`, `media`, `quick_overview`, `quote`, `sequence`, `sequence_item`, `tabs`, `teaser_single`, `text_section`, `view`

## Media Types (11)

`audio`, `bits`, `chart`, `contact`, `download`, `gallery`, `image`, `publication`, `video` + custom remote file source

## Taxonomy Vocabularies (10)

`abbreviations`, `assurance`, `audiences`, `certification_type`, `contact_topics`, `dictionary`, `it_sik_categories`, `media_types`, `tags`, `topics`

## Group Types (2)

`administration`, `csn` — used for access control and content organization

## Workflows (2)

`nodes` (content moderation), `registration`

## User Roles (7)

`admin`, `anonymous`, `authenticated`, `bsi_admin`, `csn_admin`, `csn_deh`, `editor`

## Webforms (3)

`cart`, `contact`, `user_registration`

---

## Custom Modules

### Core BSI Modules

| Module | Description |
|---|---|
| `bsi_custom` | Main customization module — blocks (jump links, to-top, teaser tiles, link boxes, contact, config_pages field), field formatters, conditions, menu links, viewsreference settings, address subscriber, many hooks |
| `bsi_access` | Access control — moderation-state-based delete access, entity operation restrictions, route alterations |
| `bsi_cert` | CERT security announcements — fetches external feed, builds teasers, injects into views tabs |
| `bsi_editor` | CKEditor 5 enhancements — abbreviation autocomplete, language select plugin, filters (media view mode, figcaption, abbr), glossify integration |
| `bsi_media` | Media customization — Address media source, download permissions per type, rabbit hole integration, thumbnail hooks |
| `bsi_search` | Search API processors (year field, paragraph aggregator), topic exposed filter block, search theme hooks |
| `bsi_report` | Annual IT security report — extends Book entity, custom book outline, report URI hooks |
| `bsi_social` | Mastodon feed integration block, social media settings |
| `bsi_charts` | Chart media source using Charts module |
| `bsi_document_tables` | Complex document paragraph table structures (frontend display) |

### Form & User Modules

| Module | Description |
|---|---|
| `bsi_webform` | Webform service overrides, custom element manager |
| `bsi_user_registration` | Webform-based user registration with helper/hooks |
| `webform_entity_cart` | Entity cart for webforms — cookie-based cart with add/remove controller and block |

### Utility Modules

| Module | Description |
|---|---|
| `short_url` | Short URL generation via redirect module — generator controller, helper, block, extra fields |
| `markup_decorator` | Text filters — external link decorator (rel, icons, ARIA) and download link decorator (file type/size) |
| `media_remote_file` | Remote file media source (no oEmbed) with video/audio formatters |
| `user_import_migrate` | CSV-based user import via Migrate — config-only module with editor migration |

---

## Custom Theme: `bsi_bund`

### Build Stack

- **Bundler:** Vite (ES2019 target, Terser)
- **CSS:** Sass (sass-embedded) + PostCSS (autoprefixer, pxtorem)
- **Component dev:** Storybook (HTML/Vite, port 6006)
- **Testing:** Vitest + Playwright (visual snapshots)
- **Linting:** ESLint, Stylelint, Prettier, TypeScript

### JS Dependencies

- `@splidejs/splide` — slider/carousel
- `plyr` — accessible audio/video player
- `countup.js` — animated number counters
- `@init/linkeffects` — animated link effects

### Architecture

```
src/
├── applications/default/     # Vite entry → dist/index.js + dist/styles.css
├── assets/default/           # fonts, icons (→ sprite), logos
├── components/               # Atomic Design (00-base → 05-pages)
│   ├── 00-base/              # colors, fonts, grid, icons, spacing
│   ├── 01-atoms/             # copyright, heading, icon, image, text
│   ├── 02-molecules/         # button, card, breadcrumb, link, badge, menu, searchbar...
│   ├── 03-organisms/         # header, footer, accordion, video, banner, hero-slider, form, table...
│   ├── 04-templates/         # page-grid, search
│   └── 05-pages/             # contact, content, home, jobs, media, report
├── globals/
│   ├── config/               # Design token JSON → SCSS/TS generation
│   ├── foundation/           # Base SCSS
│   ├── mixins/               # Shared SCSS mixins
│   ├── styles/               # Global styles (reset, RTE, Drupal overrides)
│   └── utils/                # TypeScript utilities
templates/                    # 130+ Twig overrides
├── block/                    # 27 block templates
├── content/node/             # Node view mode templates
├── content/media/            # Media templates
├── paragraphs/               # Paragraph type templates
├── field/                    # 40+ field templates
├── menu/                     # Main, footer, header, social menus
├── views/                    # Search, news, explorer views
└── ...
```

### Build Commands

```bash
npm install
npm run tokens:generate   # Generate SCSS/TS from design tokens
npm run build             # Vite production build → dist/
npm run storybook         # Storybook dev server on :6006
npm run test              # Vitest + Playwright
```

### Output

Single library `bsi_bund/global` loading `dist/styles.css` + `dist/index.js`.

### Breakpoints

| Name | Min-width |
|---|---|
| mobile | 0px |
| narrow | 576px |
| standard | 992px |
| wide | 1224px |

---

## Patches (43 total)

Heavy patching — 24 contrib modules patched. Notable:

- **Drupal core (7 patches):** Config translation for entity view modes, options translation, link autocomplete for non-node entities, taxonomy description display, field UI bundleless access, media library CSS, text formatter empty skip
- **Group (4):** Permission form fix, relationship save fix, views argument translation, role ordering
- **Default Content (4):** Export action, manual imports, Layout Builder support, file overwrite
- **Book (3):** Entity URL, weight visibility, whitepage fix

All patches defined in `composer.json` `extra.patches` using `cweagans/composer-patches`.

---

## Custom Features

### Annual Report Search

Search bar on annual report pages (`entry_page` and `report_page`) that allows
visitors to search within a specific annual report. See
[docs/search-situation-report.md](docs/search-situation-report.md) for full
technical documentation.

- **Module:** `bsi_report` (`SearchHooks.php`)
- **View:** `search_yearly_report` (Solr-backed, book-scoped)
- **Visibility:** Only on nodes that are part of a book
- **Theming:** Full-width searchbar with magnifying glass icon, autocomplete
- **Placement on `entry_page`:** Also available via Layout Builder under
  "Lists (Views)" > "Search Yearly Report"

---

## Key Infrastructure

- **Solr search** with autocomplete, facets, and custom processors
- **Group module** for access control (administration + CSN groups)
- **Content moderation** workflows for nodes and registrations
- **Klaro** cookie consent manager (36 configs)
- **Rabbit Hole** redirects per bundle (21 configs)
- **74 image styles**, 18 responsive image styles
- **Pathauto** URL alias patterns
- **Simple Sitemap** generation
- **Metatag + Schema.org** (schema_metatag)
- **Layout Builder** with restrictions and string translation (layout_builder_st)

---

## Setup (after unpacking)

```bash
composer install                    # Restore vendor/ and contrib
npm install --prefix web/themes/custom/bsi_bund  # Theme dependencies
npm run build --prefix web/themes/custom/bsi_bund # Build theme assets

# Database & Drupal
# Configure DB in web/sites/default/settings.local.php
drush config:import                 # Import config
drush cr                            # Clear cache
```
