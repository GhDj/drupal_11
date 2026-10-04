# Annual Report Search — Technical Documentation

## User Stories

> As a website visitor, I want to be able to search the annual report using a
> search box so that I can quickly find relevant sections without having to
> scroll through the report manually.

> As a website visitor, I want to see a clear list of results after my search
> and be able to jump directly to a specific result so that I can quickly find
> relevant sections in the annual report and read them directly in the correct
> section.

## Overview

A full-width search bar is placed on annual report pages (`entry_page` and
`report_page` content types), allowing visitors to search within that specific
report. The search is scoped to only the current annual report (not other
reports) via the book module's book ID.

The search bar appears on any `entry_page` or `report_page` node that is part
of a book. It is injected programmatically via `hook_node_view()` in the
`bsi_report` module. The book ID is resolved via the `book.manager` service
since `$node->book` may not be populated in all contexts.

Search results are displayed as a list of clickable cards using the
`bsi-card--search` design pattern from the Storybook component library.
Each result shows a topline (content type + date), linked heading, and
excerpt text. Clicking a result navigates directly to that report section.

## Architecture

```
┌─────────────────────────────────────────────────────┐
│  entry_page / report_page (part of a book)          │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │  Lagebericht durchsuchen                      │  │
│  │  ┌────────────────────────────────────┬────┐  │  │
│  │  │ Suchbegriff eingeben               │ 🔍 │  │  │
│  │  └────────────────────────────────────┴────┘  │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │  bsi-search-result                            │  │
│  │  ┌─────────────────────────────────────────┐  │  │
│  │  │  Result header (X to Y of Z results)    │  │  │
│  │  ├─────────────────────────────────────────┤  │  │
│  │  │  bsi-card--search                       │  │  │
│  │  │  ┌ Topline: "Report page · 25.09.2026"  │  │  │
│  │  │  ├ Heading: linked title ──────→ node   │  │  │
│  │  │  └ Body: search excerpt                 │  │  │
│  │  ├─────────────────────────────────────────┤  │  │
│  │  │  bsi-card--search                       │  │  │
│  │  │  ┌ Topline  ├ Heading  └ Body           │  │  │
│  │  ├─────────────────────────────────────────┤  │  │
│  │  │  Pagination                             │  │  │
│  │  └─────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │  field_paragraphs (page sections)             │  │
│  └───────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
```

### Data Flow

```
User input → Views exposed filter (search_api_fulltext)
           → Search API query on "default" Solr index
           → Filtered by: node_book = current book ID (contextual argument)
                          node_bundle = entry_page OR report_page
                          language = content language
                          node_status = published
           → Sorted by: relevance DESC, created DESC
           → Results rendered in "search_result" view mode
           → Each result rendered as bsi-card--search
           → AJAX response replaces view container
```

## Components

### Backend

#### Custom Module: `bsi_report`

**`SearchHooks`** (`src/Hook/SearchHooks.php`) provides two hooks:

- **`hook_node_view()`**: Injects the `search_yearly_report` view into both
  `entry_page` and `report_page` nodes that are part of a book. Uses
  `BookManagerInterface::loadBookLink()` to resolve the book ID. Renders the
  view via `Views::getView()->buildRenderable()` with the book ID as a
  contextual argument.

- **`hook_form_views_exposed_form_alter()`**: Applies searchbar theming to the
  yearly report search exposed form. Overrides the form wrapper, input, and
  submit button templates so the form renders as a styled searchbar with a
  magnifying glass icon instead of the default "Apply" button.

  | Override | Template | Effect |
  |---|---|---|
  | `#theme_wrappers` | `form__search_global` | Wraps form in `bsi-searchbar` div |
  | `search.#theme` | `input__search` | Styled search input with placeholder |
  | `submit.#theme_wrappers` | `input__submit_search` | Magnifying glass icon button |

#### View: `search_yearly_report`

- **Config:** `config/sync/views.view.search_yearly_report.yml`
- **Base table:** `search_api_index_default` (Solr)
- **Display:** `block_search_yearly_reports` (block plugin)
- **Exposed filter:** `search_api_fulltext` (identifier: `search`, not required)
- **Contextual argument:** `node_book` — passed explicitly via
  `buildRenderable()` from the book ID resolved by `book.manager`
- **Language filter:** `***LANGUAGE_language_content***` (content language,
  not interface language — important since content is German but admin UI
  may be English)
- **AJAX:** Enabled — results load inline without page reload
- **Pager:** Full, 10 items per page
- **Autocomplete:** Enabled via `search_api_solr_terms` suggester (min 1 char,
  max 10 suggestions, auto-submit on selection)

#### Search API Index: `default`

- **Config:** `config/sync/search_api.index.default.yml`
- **Server:** Solr Cloud (localhost:8983, core `bsi`)
- **Relevant indexed fields:**
  - `node_book` (integer) — book ID for scoping
  - `node_bundle` (string) — content type filter
  - `node_label` (fulltext, boost 2.0) — node title
  - `node_body` (fulltext) — body field
  - `node_summary` (fulltext, boost 1.3) — body summary
  - `aggregated_paragraph_text_boost_*` — paragraph text at various boost levels
  - `paragraph_heading` (fulltext, boost 1.5) — paragraph section titles
- **Processors:** `paragraph_aggregator` (custom, walks paragraph references),
  `highlight` (256 char excerpt)
- **Important:** After adding nodes to a book, reindex is required:
  `drush search-api:reset-tracker default && drush search-api:index default`

#### Custom Module: `bsi_search` (pre-existing, unchanged)

- **`BlockHooks`**: Restricts the `search_yearly_report` view block to only
  appear in Layout Builder on nodes that are part of a book.
- **`ViewsHooks`**: Appends the search term to the results summary header.
- **`ParagraphAggregator`**: Recursively indexes paragraph text into
  boost-grouped fields.

#### Autocomplete

- **Config:** `config/sync/search_api_autocomplete.search.search_yearly_report.yml`
- **Suggester:** `search_api_solr_terms` (Solr terms component)
- **Settings:** min_length=1, limit=10, autosubmit=true

### Frontend

#### Search Result Design (Storybook components)

The search results use the `search-result` and `card--search` Storybook
components:

| Component | Path | Purpose |
|---|---|---|
| `search-result` | `src/components/02-molecules/search-result/` | Results list wrapper with `bsi-search-result__inner` (max-width 960px, centered) and `bsi-search-result__list-item` (blue top border separator) |
| `card--search` | `src/components/02-molecules/card/_card.scss` | Search result card variant with larger topline, black body text, and underlined CTA link |
| `heading--linked` | `src/components/01-atoms/heading/_heading.scss` | Heading with link — inherits text color, no underline |

#### View Template

- **File:** `templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig`
- **Purpose:** Based on `views--search.html.twig` pattern. Renders exposed
  search form, then wraps results in `bsi-search-result` / `bsi-search-result__inner`
  container (centered, max-width 960px).

#### List Template

- **File:** `templates/views/views-view-list--search-yearly-report.html.twig`
- **Purpose:** Renders the HTML list with `bsi-search-result__list` class and
  each item with `bsi-search-result__list-item` (blue top border separator).

#### Node Templates (search result view mode)

- **File:** `templates/content/node/node--report-page--search-result.html.twig`
- **File:** `templates/content/node/node--entry-page--search-result.html.twig`
- **Purpose:** Render each search result as a `bsi-card--search` card:
  - **Topline:** Content type label + date (`node.type.entity.label · node.getCreatedTime()|date('d.m.Y')`)
  - **Heading:** Linked title using `bsi-heading--linked` (inherits color, no underline). Falls back to `node.label()` if `label` variable is empty.
  - **Body:** Search API excerpt (highlighted), falls back to body summary

#### Block Template

- **File:** `templates/block/block--views-block--search-yearly-report.html.twig`
- **Purpose:** Fallback block template (for Layout Builder placement). Wraps the
  view block with `bsi-report-search` class and heading.

#### Form Templates (shared with global search)

| Template | Purpose |
|---|---|
| `form--search-global.html.twig` | Wraps exposed form in `bsi-searchbar` div |
| `input--search.html.twig` | Search input with `bsi-searchbar__input` class and placeholder |
| `input--submit-search.html.twig` | Magnifying glass icon-only button (replaces "Apply") |
| `form-element--search-api-autocomplete.html.twig` | Strips Drupal form classes from autocomplete wrapper |

#### Styles

| File | Purpose |
|---|---|
| `src/components/02-molecules/searchbar/_searchbar.scss` | Base searchbar styling (input, button, focus states) |
| `src/components/02-molecules/search-result/_search-result.scss` | Results list layout (centered, max-width, blue separators) |
| `src/components/02-molecules/card/_card.scss` | Card component + `card--search` variant |
| `src/components/04-templates/search/_search.scss` | `bsi-report-search` full-width layout + autocomplete dropdown styles |

## Search Behavior

| Trigger | Action |
|---|---|
| Click magnifying glass icon | Submits the form via AJAX |
| Press Enter in input | Submits the form via AJAX |
| Empty search submitted | Returns all published content within the current report book |
| Autocomplete suggestion selected | Auto-submits (configured via `autosubmit: true`) |
| Click on result heading | Navigates to the report section page |
| Click anywhere on result card | Navigates to the report section (full card is clickable via CSS overlay) |

### Scoping

The book ID is resolved via `BookManagerInterface::loadBookLink()` and passed
explicitly to the view via `buildRenderable()`. This ensures that only content
belonging to the **current** annual report is searched — other annual reports
are excluded.

### Visibility

The search bar only appears on `entry_page` and `report_page` nodes that are
part of a book. If a node of these types is not assigned to a book, the search
bar is not shown (the view requires a book ID to scope results).

## Translation

| String | German (de) |
|---|---|
| "Search annual report" (heading) | "Lagebericht durchsuchen" |
| "Enter a search term" (placeholder) | "Suchbegriff eingeben" |
| "Your search did not match any results" | "Es wurden keine Ergebnisse gefunden." |
| "@start to @end of @total results" | "@start bis @end von @total Ergebnissen" |

Translations are managed in the project's translation files and imported via
`drush locale:import`.

## Files Changed

| File | Change |
|---|---|
| `web/modules/custom/bsi_report/src/Hook/SearchHooks.php` | **New** — `hook_node_view()` injects search; `hook_form_views_exposed_form_alter()` applies searchbar theming |
| `web/themes/custom/bsi_bund/templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig` | **New** — View template with `bsi-search-result` wrapper |
| `web/themes/custom/bsi_bund/templates/views/views-view-list--search-yearly-report.html.twig` | **New** — List style template with `bsi-search-result__list` classes |
| `web/themes/custom/bsi_bund/templates/content/node/node--report-page--search-result.html.twig` | **New** — Report page search result as `bsi-card--search` |
| `web/themes/custom/bsi_bund/templates/content/node/node--entry-page--search-result.html.twig` | **New** — Entry page search result as `bsi-card--search` |
| `web/themes/custom/bsi_bund/templates/block/block--views-block--search-yearly-report.html.twig` | **New** — Block template with heading and layout wrapper |
| `web/themes/custom/bsi_bund/src/components/02-molecules/search-result/` | **New** — Search result Storybook component (template, SCSS, stories) |
| `web/themes/custom/bsi_bund/src/components/02-molecules/card/_card.scss` | **Modified** — Added `card--search` variant |
| `web/themes/custom/bsi_bund/src/components/04-templates/search/_search.scss` | **Modified** — Added `bsi-report-search` styles and autocomplete dropdown |
| `config/sync/views.view.search_yearly_report.yml` | **Modified** — Changed language filter from interface to content language |

## Deployment Steps

1. Reindex search: `drush search-api:reset-tracker default && drush search-api:index default`
2. Clear caches: `drush cr`
3. Import translations: `drush locale:import de <path-to-po-file> --type=customized --override=none`
4. Rebuild theme: `npm run build --prefix web/themes/custom/bsi_bund`
5. Verify on an `entry_page` or `report_page` that is part of a book

## Testing Checklist

### Search Bar
- [ ] Search box appears on `entry_page` nodes that are part of a book
- [ ] Search box appears on `report_page` nodes that are part of a book
- [ ] Search box does NOT appear on nodes not part of a book
- [ ] Heading "Lagebericht durchsuchen" displays (German)
- [ ] Search input is full width within the content area
- [ ] Magnifying glass icon is shown (no "Apply" button)
- [ ] Clicking the magnifying glass icon triggers the search
- [ ] Pressing Enter triggers the search
- [ ] Empty search returns all report content

### Search Results
- [ ] Results appear below the search box via AJAX
- [ ] Results show only content from the current report (not other reports)
- [ ] Each result displays as a card with topline (type + date), heading, and excerpt
- [ ] Result headings are clickable links to the report section
- [ ] Heading links use dark text color (no blue, no underline)
- [ ] Full card is clickable (CSS overlay from heading link)
- [ ] Results list items have blue top border separator
- [ ] Results are centered with max-width 960px
- [ ] Pagination appears when results exceed 10 items

### Autocomplete
- [ ] Autocomplete suggestions appear after typing 1+ characters
- [ ] Selecting an autocomplete suggestion auto-submits the search

### Results Header
- [ ] Results summary shows "X to Y of Z results"
- [ ] After searching, header shows "for your search for 'term'"
