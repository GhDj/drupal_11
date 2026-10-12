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
`report_page` content types) via Layout Builder, allowing visitors to search
within that specific report. The search is scoped to only the current annual
report (not other reports) via the book module's book ID.

On **report pages**, the search slot shows only the form — submitting it
redirects to the **entry page** (book root) with the search parameter, where
results are displayed. Each book has exactly one entry page.

Search results are displayed as a list of clickable cards using the
`bsi-card--search` design pattern from the Storybook component library.

## History

### Phase 1 — Initial Implementation (hook-based injection)

The search was originally built using `hook_node_view()` in `bsi_report`
module's `SearchHooks` class. This hook programmatically injected the
`search_yearly_report` view into both `entry_page` and `report_page` nodes
that were part of a book.

**What was built (backend, still in use):**
- View `search_yearly_report` with Solr-backed search scoped to book ID
- Contextual argument `node_book` with `top_level_book` default
- Filters: `node_bundle` (entry_page, report_page), `search_api_language`,
  `node_status`, `search_api_fulltext` (exposed)
- AJAX-enabled results with pagination (10 per page)
- Autocomplete via `search_api_solr_terms` suggester
- `BlockHooks` in `bsi_search` to restrict view blocks to book-linked nodes
- `ViewsHooks` in `bsi_search` to append search term to result headers
- `ParagraphAggregator` search processor for indexing paragraph text
- Search result view mode templates for `entry_page` and `report_page`
- Bold result count: `<strong>@start bis @end</strong>&nbsp;von&nbsp;<strong>@total</strong>&nbsp;Ergebnissen`

### Phase 2 — Refactor to block-based placement (current)

The hook-based injection was replaced with a custom block plugin, matching
how the global search slot (`TopicExposedFilterBlock`) works. The block is
placed differently depending on the content type:

- **entry_page:** Placed via **Layout Builder** (LB is already enabled)
- **report_page:** Placed via **Block Layout** (`admin/structure/block`),
  between "Primary admin actions" and "Main page content" — report_page
  does NOT use Layout Builder

**What changed:**
- **Removed:** `hook_node_view()` from `SearchHooks` (the `formAlter` hook
  remains for styling the results page exposed form)
- **Added:** `ReportSearchSlotBlock` plugin in `bsi_report` — a custom block
  that renders a form-only search slot and redirects submissions to the
  entry page
- **Added:** `block_search_report_slot` display on the `search_yearly_report`
  view, with its own filter overrides (node_bundle, language, fulltext,
  node_status, node_status_1 for unpublished)
- **Added:** Form-only Twig template for the search slot display
- **Added:** Block placement config (`block.block.bsi_bund_report_search.yml`)
  for report_page via Block Layout, with `entity_bundle:node` visibility
  restricted to `report_page`
- **Changed:** Search slot block placed in `entry_page.full` layout via LB
- **Changed:** LB allowlists on entry_page updated to include
  `bsi_report_search_slot`
- **Changed:** `BlockHooks` updated to conditionally show the new block only
  on book-linked nodes in Layout Builder
- **Changed:** Main page content block weight adjusted from -12 to -11 to
  make room for the search slot at -12

**What did NOT change (backend, pre-existing):**
- The `search_yearly_report` view itself (query, filters, sorts, index)
- The `block_search_yearly_reports` display (results + form on entry page)
- Search API index configuration and Solr setup
- Autocomplete configuration
- Node search result templates (`node--*--search-result.html.twig`)
- View list template and results wrapper template
- All frontend components (searchbar, search-result, card--search)
- The `formAlter` hook for theming the exposed form
- `report_page.full` view display (no Layout Builder)

## Architecture

### Search Slot (report_page → entry_page redirect)

```
┌─ report_page (Block Layout, NO Layout Builder) ─────┐
│                                                      │
│  [Primary admin actions]         (weight -13)        │
│                                                      │
│  ┌─ ReportSearchSlotBlock ────────────────────────┐  │
│  │  Lagebericht durchsuchen      (weight -12)     │  │
│  │  ┌──────────────────────────────────────┬────┐ │  │
│  │  │ Suchbegriff eingeben                 │ 🔍 │ │  │
│  │  └──────────────────────────────────────┴────┘ │  │
│  │  form action → /entry-page-url?search=term     │  │
│  │  visibility: report_page only                  │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  [Main page content]             (weight -11)        │
│  ┌─ field_paragraphs ────────────────────────────┐   │
│  │  (page content sections)                      │   │
│  └───────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────┘
```

### Results Display (entry_page)

```
┌─ entry_page (Layout Builder) ───────────────────────┐
│                                                      │
│  ┌─ ReportSearchSlotBlock ────────────────────────┐  │
│  │  (same search form, redirects to self)         │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  ┌─ views_block: search_yearly_report ────────────┐  │
│  │  bsi-search-result                             │  │
│  │  ┌─────────────────────────────────────────┐   │  │
│  │  │  Result header (X bis Y von Z)          │   │  │
│  │  ├─────────────────────────────────────────┤   │  │
│  │  │  bsi-card--search                       │   │  │
│  │  │  ┌ Topline: "Report page · 25.09.2026"  │   │  │
│  │  │  ├ Heading: linked title ──────→ node   │   │  │
│  │  │  └ Body: search excerpt                 │   │  │
│  │  ├─────────────────────────────────────────┤   │  │
│  │  │  Pagination                             │   │  │
│  │  └─────────────────────────────────────────┘   │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  ┌─ field_paragraphs ────────────────────────────┐   │
│  │  (page content sections)                      │   │
│  └───────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────┘
```

### Data Flow

```
report_page: User input → form GET → entry_page?search=term
entry_page:  search param → Views exposed filter (search_api_fulltext)
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

#### Block Plugin: `ReportSearchSlotBlock`

- **File:** `web/modules/custom/bsi_report/src/Plugin/Block/ReportSearchSlotBlock.php`
- **ID:** `bsi_report_search_slot`
- **Category:** "BSI Bericht"
- **Purpose:** Renders the search form scoped to the current book. Redirects
  form submission to the entry page (book root node).

**How it works:**
1. Gets the current node from the route
2. Loads the book link via `BookManagerInterface::loadBookLink()`
3. If the node is in a book, loads the `search_yearly_report` view's
   `block_search_report_slot` display
4. Passes the book ID as a contextual argument
5. Sets `$view->override_url` to the entry page URL (book root = `$bid`)
6. Applies searchbar theming via `preRender` callback
7. Returns `NULL` if node is not in a book (block hidden)

#### Hook: `SearchHooks::formAlter()`

- **File:** `web/modules/custom/bsi_report/src/Hook/SearchHooks.php`
- **Purpose:** Applies searchbar theming to the yearly report search exposed
  form on the results display (`block_search_yearly_reports`).

  | Override | Template | Effect |
  |---|---|---|
  | `#theme_wrappers` | `form__search_global` | Wraps form in `bsi-searchbar` div |
  | `search.#theme` | `input__search` | Styled search input with placeholder |
  | `submit.#theme_wrappers` | `input__submit_search` | Magnifying glass icon button |

#### View: `search_yearly_report`

- **Config:** `config/sync/views.view.search_yearly_report.yml`
- **Base table:** `search_api_index_default` (Solr)
- **Displays:**
  - `block_search_yearly_reports` — Full results block (form + results + pager)
  - `block_search_report_slot` — Form-only search slot (no results rendered),
    with its own filter overrides (node_bundle, language, fulltext,
    node_status + node_status_1 for unpublished content)
- **Exposed filter:** `search_api_fulltext` (identifier: `search`, not required)
- **Contextual argument:** `node_book` — default type `top_level_book`,
  overridden by `ReportSearchSlotBlock` with the resolved book ID
- **Language filter:** `***LANGUAGE_language_content***` (content language,
  not interface language)
- **AJAX:** Enabled on results display
- **Pager:** Full, 10 items per page

#### Block Placement

The search slot is placed differently per content type:

**entry_page — Layout Builder:**
- **Config:** `config/sync/core.entity_view_display.node.entry_page.full.yml`
- Placed as a default component at the top of the LB layout
- Allowlisted blocks: `bsi_report_search_slot`, `views_block:search_yearly_report-block_search_yearly_reports`

**report_page — Block Layout:**
- **Config:** `config/sync/block.block.bsi_bund_report_search.yml`
- Placed in the `content` region at weight `-12` (between "Primary admin
  actions" at `-13` and "Main page content" at `-11`)
- Visibility restricted to `report_page` nodes via `entity_bundle:node`
- report_page does NOT use Layout Builder

#### Block Visibility

- **File:** `web/modules/custom/bsi_search/src/Hook/BlockHooks.php`
- The `bsi_report_search_slot` and `search_yearly_report` view blocks are
  only available in Layout Builder on nodes that are linked to a book.
  `hook_plugin_filter_block__layout_builder_alter()` removes them from the
  block list when the entity has no book link.
- On report_page (Block Layout), the `ReportSearchSlotBlock` itself returns
  `NULL` if the node has no book link, so it is automatically hidden.

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

#### Autocomplete

- **Config:** `config/sync/search_api_autocomplete.search.search_yearly_report.yml`
- **Suggester:** `search_api_solr_terms` (Solr terms component)
- **Settings:** min_length=1, limit=10, autosubmit=true

### Frontend

#### Search Slot Template (form-only)

- **File:** `templates/views/views-view--search-yearly-report--block-search-report-slot.html.twig`
- **Purpose:** Renders only `{{ exposed }}` — no results, no pager.

#### Results View Template

- **File:** `templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig`
- **Purpose:** Renders exposed form, then wraps results in
  `bsi-search-result` / `bsi-search-result__inner` container.

#### List Template

- **File:** `templates/views/views-view-list--search-yearly-report.html.twig`
- **Purpose:** Renders results list with `bsi-search-result__list` class.

#### Node Templates (search result view mode)

- **File:** `templates/content/node/node--report-page--search-result.html.twig`
- **File:** `templates/content/node/node--entry-page--search-result.html.twig`
- **Purpose:** Render each result as a `bsi-card--search` card.

#### Styles

| File | Purpose |
|---|---|
| `src/components/02-molecules/searchbar/_searchbar.scss` | Searchbar styling |
| `src/components/02-molecules/search-result/_search-result.scss` | Results layout + `bsi-report-search` wrapper |
| `src/components/02-molecules/card/_card.scss` | Card component + `card--search` variant |

## Search Behavior

| Trigger | Action |
|---|---|
| Submit on **report_page** | Redirects to entry_page with `?search=term` |
| Submit on **entry_page** | Searches within book via AJAX |
| Empty search submitted | Returns all published content within the book |
| Autocomplete suggestion selected | Auto-submits the search |
| Click on result heading | Navigates to the report section page |

### Scoping

The book ID is resolved via `BookManagerInterface::loadBookLink()`. On the
search slot, it is passed explicitly to the view via `setArguments()`. On the
results display, the `top_level_book` default argument plugin resolves it
from the current page context. Only content belonging to the **current**
annual report is searched.

### Visibility

The search blocks only appear on `entry_page` and `report_page` nodes that
are part of a book. This is enforced at three levels:
1. The `ReportSearchSlotBlock` returns `NULL` if the node has no book link
2. `BlockHooks` removes the blocks from Layout Builder's block list when
   the entity is not linked to a book (entry_page only)
3. Block Layout visibility condition restricts the block to `report_page`
   nodes (report_page only)

## Translation

| String | German (de) |
|---|---|
| "Search annual report" (heading) | "Lagebericht durchsuchen" |
| "Enter a search term" (placeholder) | "Suchbegriff eingeben" |
| "Your search did not match any results" | "Es wurden keine Ergebnisse gefunden." |
| "@start to @end of @total results" | `<strong>@start bis @end</strong>&nbsp;von&nbsp;<strong>@total</strong>&nbsp;Ergebnissen` |

## Files

### Backend

| File | Status |
|---|---|
| `web/modules/custom/bsi_report/src/Plugin/Block/ReportSearchSlotBlock.php` | **New** — Search slot block plugin |
| `web/modules/custom/bsi_report/src/Hook/SearchHooks.php` | **Modified** — Removed `hook_node_view()`, kept `formAlter()` |
| `web/modules/custom/bsi_search/src/Hook/BlockHooks.php` | **Modified** — Added `bsi_report_search_slot` to book-conditional blocks |
| `config/sync/views.view.search_yearly_report.yml` | **Modified** — Added `block_search_report_slot` display with full filter overrides |
| `config/sync/block.block.bsi_bund_report_search.yml` | **New** — Block Layout placement for report_page (content region, weight -12) |
| `config/sync/block.block.bsi_bund_content.yml` | **Modified** — Weight changed from -12 to -11 |
| `config/sync/language/de/views.view.search_yearly_report.yml` | **Modified** — Added German translation for new display |
| `config/sync/core.entity_view_display.node.entry_page.full.yml` | **Modified** — Added search slot block to LB layout |

### Frontend

| File | Status |
|---|---|
| `templates/views/views-view--search-yearly-report--block-search-report-slot.html.twig` | **New** — Form-only template |
| `templates/views/views-view--search-yearly-report--block-search-yearly-reports.html.twig` | Unchanged |
| `templates/views/views-view-list--search-yearly-report.html.twig` | Unchanged |
| `templates/content/node/node--report-page--search-result.html.twig` | Unchanged |
| `templates/content/node/node--entry-page--search-result.html.twig` | Unchanged |
| `src/components/02-molecules/search-result/_search-result.scss` | Unchanged |
| `src/components/02-molecules/card/_card.scss` | Unchanged |

## Deployment Steps

1. Pull latest code (ensure `ReportSearchSlotBlock.php` is present)
2. Import config:
   ```bash
   # Import individually if full config:import fails:
   cat > /tmp/import.php << 'EOF'
   <?php
   $source = new \Drupal\Core\Config\FileStorage('/var/www/html/config/sync');
   $configs = [
     'views.view.search_yearly_report',
     'block.block.bsi_bund_report_search',
     'block.block.bsi_bund_content',
     'core.entity_view_display.node.report_page.full',
     'core.entity_view_display.node.entry_page.full',
   ];
   foreach ($configs as $name) {
     \Drupal::service('config.storage')->write($name, $source->read($name));
     echo "Imported: $name\n";
   }
   $lang_source = new \Drupal\Core\Config\FileStorage('/var/www/html/config/sync/language/de');
   \Drupal::languageManager()->getLanguageConfigOverride('de', 'views.view.search_yearly_report')
     ->setData($lang_source->read('views.view.search_yearly_report'))->save();
   echo "Imported: de/views.view.search_yearly_report\n";
   EOF
   drush php:script /tmp/import.php
   ```
3. Clear caches: `drush cr`
4. Reindex search: `drush search-api:reset-tracker default && drush search-api:index default`
5. Verify search slot appears on book-linked report pages
6. Verify form submission from report_page redirects to entry_page
7. Verify results display on entry_page

## Testing Checklist

### Search Slot on report_page (Block Layout)
- [ ] Search slot appears on `report_page` nodes that are part of a book
- [ ] Search slot does NOT appear on `report_page` nodes not in a book
- [ ] Search slot does NOT appear on other content types
- [ ] Block is visible at `admin/structure/block` between "Primary admin
      actions" and "Main page content"
- [ ] Heading "Lagebericht durchsuchen" displays (German)
- [ ] Magnifying glass icon is shown (no "Apply" button)
- [ ] Submitting redirects to entry_page with `?search=term`

### Search Slot on entry_page (Layout Builder)
- [ ] Search slot appears on `entry_page` nodes that are part of a book
- [ ] Search slot block is available in LB for book-linked entry pages
- [ ] Search slot block is NOT available in LB for non-book entry pages
- [ ] Submitting searches inline (redirects to self with `?search=term`)

### Search Results (entry_page)
- [ ] Results appear on entry_page after search
- [ ] Results show only content from the current report book
- [ ] Each result displays as a card with topline, heading, and excerpt
- [ ] Result headings are clickable links to the report section
- [ ] Result count shows bold numbers: **1 bis 2** von **10** Ergebnissen
- [ ] Pagination appears when results exceed 10 items
- [ ] Results view block can be added/repositioned via LB on entry_page
