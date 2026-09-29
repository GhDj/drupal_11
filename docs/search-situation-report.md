# Annual Report Search — Technical Documentation

## User Story

> As a website visitor, I want to be able to search the annual report using a
> search box so that I can quickly find relevant sections without having to
> scroll through the report manually.

## Overview

A full-width search bar is placed on annual report pages (`entry_page` and
`report_page` content types), allowing visitors to search within that specific
report. The search is scoped to only the current annual report (not other
reports) via the book module's book ID.

The search bar appears on any `entry_page` or `report_page` node that is part
of a book. It is injected programmatically via `hook_node_view()` in the
`bsi_report` module.

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
│  │  [Autocomplete dropdown with results]         │  │
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
                          language = current interface language
                          node_status = published
           → Sorted by: relevance DESC, created DESC
           → Results rendered in "search_result" view mode
           → AJAX response replaces view container
```

## Components

### Backend

#### Custom Module: `bsi_report`

**`SearchHooks`** (`src/Hook/SearchHooks.php`) provides two hooks:

- **`hook_node_view()`**: Injects the `search_yearly_report` view into both
  `entry_page` and `report_page` nodes that are part of a book. Renders a
  container with class `bsi-report-search`, a translatable heading, and the
  search view at weight -10 (above page content).

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
- **Contextual argument:** `node_book` with default `top_level_book` —
  automatically resolves to the book ID of the current page, scoping results
  to that single report
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

#### View Template

- **File:** `web/themes/custom/bsi_bund/templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig`
- **Purpose:** Renders the exposed search form and, after AJAX submission, the
  results (header, content/empty, pager) below it.

#### Block Template

- **File:** `web/themes/custom/bsi_bund/templates/block/block--views-block--search-yearly-report.html.twig`
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
| `src/components/04-templates/search/_search.scss` | `bsi-report-search` full-width layout + autocomplete dropdown styles |

The `bsi-report-search` class provides:
- Full width with no horizontal padding
- `position: relative` for autocomplete dropdown positioning
- Autocomplete dropdown: white background, bordered, flex layout per item
  (suggestion label left, description right), neutral-50 hover state

## Search Behavior

| Trigger | Action |
|---|---|
| Click magnifying glass icon | Submits the form via AJAX |
| Press Enter in input | Submits the form via AJAX |
| Empty search submitted | Returns all published content within the current report book |
| Autocomplete suggestion selected | Auto-submits (configured via `autosubmit: true`) |

### Scoping

The `node_book` contextual argument uses the `top_level_book` default argument
plugin, which resolves to the book ID (bid) of the top-level book page. This
ensures that only content belonging to the **current** annual report is
searched — other annual reports are excluded.

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
| `web/modules/custom/bsi_report/src/Hook/SearchHooks.php` | **New** — `hook_node_view()` injects search into report pages; `hook_form_views_exposed_form_alter()` applies searchbar theming |
| `web/themes/custom/bsi_bund/templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig` | **New** — View template rendering form + AJAX results |
| `web/themes/custom/bsi_bund/templates/block/block--views-block--search-yearly-report.html.twig` | **New** — Block template with heading and layout wrapper |
| `web/themes/custom/bsi_bund/src/components/04-templates/search/_search.scss` | **Modified** — Added `bsi-report-search` full-width styles and autocomplete dropdown |
| `config/sync/core.entity_view_display.node.entry_page.full.yml` | **Unchanged** — Search block is allowlisted in Layout Builder restrictions (pre-existing) |

## Deployment Steps

1. Clear caches: `drush cr`
2. Import translations: `drush locale:import de <path-to-po-file> --type=customized --override=none`
3. Rebuild theme (if SCSS changed): `npm run build --prefix web/themes/custom/bsi_bund`
4. Verify on an `entry_page` or `report_page` that is part of a book

## Testing Checklist

- [ ] Search box appears on `entry_page` nodes that are part of a book
- [ ] Search box appears on `report_page` nodes that are part of a book
- [ ] Search box does NOT appear on nodes not part of a book
- [ ] Heading "Lagebericht durchsuchen" displays (German)
- [ ] Heading "Search annual report" displays (English)
- [ ] Placeholder text "Suchbegriff eingeben" / "Enter a search term" is shown
- [ ] Search input is full width within the content area
- [ ] Magnifying glass icon is shown (no "Apply" button)
- [ ] Clicking the magnifying glass icon triggers the search
- [ ] Pressing Enter triggers the search
- [ ] Empty search returns all report content
- [ ] Search results appear below the search box via AJAX
- [ ] Results show only content from the current report (not other reports)
- [ ] Autocomplete suggestions appear after typing 1+ characters
- [ ] Selecting an autocomplete suggestion auto-submits the search
- [ ] Pagination works within search results
- [ ] Results summary header shows "X to Y of Z results for your search for 'term'"
- [ ] Search result items render in `search_result` view mode with highlighted excerpts
