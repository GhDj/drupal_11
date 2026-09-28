# Situation Report Search — Technical Documentation

## User Story

> As a website visitor, I want to be able to search the situation report using a
> search box so that I can quickly find relevant sections without having to
> scroll through the report manually.

## Overview

A search box is placed on the annual situation report entry page, allowing
visitors to search within that specific report. The search is scoped to only the
current annual report (not other reports) and returns results from both
`entry_page` and `report_page` content types that belong to the same book.

## Architecture

```
┌─────────────────────────────────────────────────────┐
│  entry_page (Layout Builder)                        │
│                                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │  Block: search_yearly_report                  │  │
│  │  ┌─────────────────────────────────────────┐  │  │
│  │  │  Heading: "Lagebericht durchsuchen"     │  │  │
│  │  │  ┌─────────────────────────┬──────┐     │  │  │
│  │  │  │ Suchbegriff eingeben    │  🔍  │     │  │  │
│  │  │  └─────────────────────────┴──────┘     │  │  │
│  │  │  [Autocomplete dropdown with results]   │  │  │
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
                          language = current interface language
                          node_status = published
           → Sorted by: relevance DESC, created DESC
           → Results rendered in "search_result" view mode
           → AJAX response replaces view container
```

## Components

### Backend

#### View: `search_yearly_report`

- **Config:** `config/sync/views.view.search_yearly_report.yml`
- **Base table:** `search_api_index_default` (Solr)
- **Display:** `block_search_yearly_reports` (block plugin)
- **Exposed filter:** `search_api_fulltext` (identifier: `search`, not required)
- **Contextual argument:** `node_book` with default `top_level_book` — automatically resolves to the book ID of the current page, scoping results to that single report
- **AJAX:** Enabled — results load inline without page reload
- **Pager:** Full, 10 items per page
- **Autocomplete:** Enabled via `search_api_solr_terms` suggester (min 1 char, max 10 suggestions, auto-submit on selection)

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
- **Processors:** `paragraph_aggregator` (custom, walks paragraph references), `highlight` (256 char excerpt)

#### Custom Module: `bsi_search`

- **`BlockHooks`** (`src/Hook/BlockHooks.php`): Restricts the `search_yearly_report` view block and exposed filter block to only appear in Layout Builder on nodes that are part of a book.
- **`ViewsHooks`** (`src/Hook/ViewsHooks.php`): Appends the search term to the results summary header (e.g., "1 to 10 of 50 results **for your search for 'term'**").
- **`ParagraphAggregator`** (`src/Plugin/search_api/processor/ParagraphAggregator.php`): Recursively indexes paragraph text into boost-grouped fields so paragraph content is searchable.

#### Autocomplete

- **Config:** `config/sync/search_api_autocomplete.search.search_yearly_report.yml`
- **Suggester:** `search_api_solr_terms` (Solr terms component)
- **Settings:** min_length=1, limit=10, autosubmit=true

### Frontend

#### Block Template

- **File:** `web/themes/custom/bsi_bund/templates/block/block--views-block--search-yearly-report.html.twig`
- **Purpose:** Wraps the view block with:
  - CSS class `bsi-search-page__search` for layout
  - Translatable heading "Search annual report" (`bsi-searchbar__title`)
  - Label display disabled (heading is hardcoded in template)

#### View Template

- **File:** `web/themes/custom/bsi_bund/templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig`
- **Purpose:** Renders the exposed search form, and after AJAX submission, the results (header, content/empty, pager) below it.

#### Form Templates (shared with global search)

| Template | Purpose |
|---|---|
| `form--search-global.html.twig` | Wraps exposed form in `bsi-searchbar` div |
| `input--search.html.twig` | Search input with `bsi-searchbar__input` class |
| `input--submit-search.html.twig` | Submit button as icon-only tertiary button with search icon |
| `form-element--search-api-autocomplete.html.twig` | Strips Drupal form classes from autocomplete wrapper |

#### Styles

| File | Purpose |
|---|---|
| `src/components/02-molecules/searchbar/_searchbar.scss` | Base searchbar styling (input, button, focus states) |
| `src/components/04-templates/search/_search.scss` | Search page layout + autocomplete dropdown styles for the report search block |

#### Autocomplete Dropdown Styling

The autocomplete dropdown within `.bsi-search-page__search` is styled with:
- White background, bordered (no top border to merge with input)
- Flex layout per item: suggestion label left, description right
- Hover state: neutral-50 background
- Absolutely positioned below the search input

### Layout Builder Placement

- **Config:** `config/sync/core.entity_view_display.node.entry_page.full.yml`
- **Block:** `views_block:search_yearly_report-block_search_yearly_reports`
- **Position:** Weight 1 (between content_moderation_control at 0 and field_paragraphs at 2)
- **Restrictions:** Block is allowlisted in `layout_builder_restrictions` for `entry_page`

## Search Behavior

| Trigger | Action |
|---|---|
| Click search icon | Submits the form via AJAX |
| Press Enter in input | Submits the form via AJAX |
| Empty search submitted | Returns all published content within the current report book |
| Autocomplete suggestion selected | Auto-submits (configured via `autosubmit: true`) |

### Scoping

The `node_book` contextual argument uses the `top_level_book` default argument
plugin, which resolves to the book ID (bid) of the top-level book page. This
ensures that only content belonging to the **current** annual report is
searched — other annual reports are excluded.

## Translation

| String | German (de) |
|---|---|
| "Search annual report" (block heading) | "Lagebericht durchsuchen" |
| "Enter a search term" (placeholder) | "Suchbegriff eingeben" |
| "Your search did not match any results" | "Es wurden keine Ergebnisse gefunden." |
| "@start to @end of @total results" | "@start bis @end von @total Ergebnissen" |
| "Apply" (submit button) | "Anwenden" |

The block heading uses `{{ 'Search annual report'|t }}` in Twig. German
translations are shipped in `translations/custom/de.po` at the project root and
imported via `drush locale:import`.

## Files Changed

| File | Change |
|---|---|
| `web/themes/custom/bsi_bund/templates/block/block--views-block--search-yearly-report.html.twig` | **New** — Block template with heading and layout wrapper |
| `web/themes/custom/bsi_bund/templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig` | **New** — View template rendering form + AJAX results |
| `web/modules/custom/bsi_report/src/Hook/SearchHooks.php` | **New** — Injects search view into `report_page` nodes via `hook_node_view()` |
| `translations/custom/de.po` | **New** — German translations for custom strings |
| `web/themes/custom/bsi_bund/src/components/04-templates/search/_search.scss` | **Modified** — Added autocomplete dropdown styles for report search |
| `config/sync/core.entity_view_display.node.entry_page.full.yml` | **Modified** — Added search view block to Layout Builder, added view dependency |

## Deployment Steps

1. Import configuration: `drush config:import`
2. Import translations: `drush locale:import de translations/custom/de.po --type=customized --override=none`
3. Rebuild theme: `npm run build --prefix web/themes/custom/bsi_bund`
4. Clear caches: `drush cr`
5. Verify on an entry_page that is part of a book — the search block should appear above the page sections

## Testing Checklist

- [ ] Search box appears on entry_page nodes that are part of a book
- [ ] Search box does NOT appear on entry_page nodes that are not part of a book
- [ ] Heading "Lagebericht durchsuchen" displays above the search input (German)
- [ ] Heading "Search annual report" displays (English)
- [ ] Placeholder text "Suchbegriff eingeben" / "Enter a search term" is shown
- [ ] Clicking the search icon triggers the search
- [ ] Pressing Enter triggers the search
- [ ] Empty search returns all report content
- [ ] Search results appear below the search box via AJAX
- [ ] Results show only content from the current report (not other reports)
- [ ] Autocomplete suggestions appear after typing 1+ characters
- [ ] Selecting an autocomplete suggestion auto-submits the search
- [ ] Pagination works within search results
- [ ] Results summary header shows "X to Y of Z results for your search for 'term'"
- [ ] Search result items render in `search_result` view mode with highlighted excerpts
