/**
 * Global search filter behavior:
 * - Initializes custom multi-select dropdown filters
 * - Renders active filter pills from selected values
 * - Pill removal and reset work via URL navigation
 * - Mobile toggle to show/hide filter sections
 */

import MultiSelectDropdown from "./MultiSelectDropdown";

function svgIcon(name: string): string {
  const existing = document.querySelector<SVGUseElement>(".bsi-icon use");
  const base =
    existing?.getAttribute("href")?.replace(/#.*$/, "") ??
    "/themes/custom/bsi_bund/dist/icons.svg";
  return `<svg class="bsi-icon" aria-hidden="true" focusable="false"><use href="${base}#${name}"></use></svg>`;
}

export default function SearchFilter(): void {
  applySearchFilter();
  const behaviors = Drupal?.behaviors;

  if (typeof Drupal !== "undefined" && behaviors) {
    Drupal.behaviors.bsiSearchFilter = {
      attach() {
        applySearchFilter();
      }
    };
  }
}

function applySearchFilter(): void {
  const view = document.querySelector(".view-display-id-block_search_global");
  if (!view) return;

  MultiSelectDropdown();
  relocateSort(view);
  initActiveFilterPills(view);
  initMobileToggle(view);
}

/**
 * Move .sort-wrapper from inside the grey filter box to the sort anchor
 * between the filter box and results. Adds a change handler that
 * navigates via URL (same pattern as filter pills).
 */
function relocateSort(view: Element): void {
  const sortWrapper = view.querySelector<HTMLElement>(".sort-wrapper");
  const anchor = view.querySelector<HTMLElement>(".js-sort-anchor");
  if (!sortWrapper || !anchor) return;
  if (anchor.contains(sortWrapper)) return; // already relocated

  anchor.appendChild(sortWrapper);

  // Navigate on sort change (select is now outside the form)
  const select = sortWrapper.querySelector<HTMLSelectElement>("select");
  if (!select || select.dataset.sortInit) return;
  select.dataset.sortInit = "true";

  select.addEventListener("change", () => {
    const url = new URL(window.location.href);
    url.searchParams.set("sort_bef_combine", select.value);
    url.searchParams.delete("page"); // reset to first page
    window.location.href = url.toString();
  });
}

/**
 * Build a search URL from the current select state, optionally
 * excluding a specific filter value.
 */
function buildSearchUrl(
  view: Element,
  excludeKey?: string,
  excludeValue?: string
): string {
  const url = new URL(window.location.pathname, window.location.origin);

  // Preserve the search term
  const currentParams = new URLSearchParams(window.location.search);
  const searchTerm = currentParams.get("search");
  if (searchTerm) {
    url.searchParams.set("search", searchTerm);
  }

  // Preserve sort if present
  const sortBy = currentParams.get("sort_by");
  const sortOrder = currentParams.get("sort_order");
  if (sortBy) url.searchParams.set("sort_by", sortBy);
  if (sortOrder) url.searchParams.set("sort_order", sortOrder);

  // Preserve page if present (but reset to page 0 when filters change)
  // Don't preserve page — changing filters should go back to page 1

  // Read current filter state from actual <select> elements
  const selects = view.querySelectorAll<HTMLSelectElement>(
    ".bsi-searchfilter__top .js-multiselect select"
  );

  selects.forEach(select => {
    const container = select.closest<HTMLElement>(".js-multiselect");
    const key = container?.dataset.filterKey ?? "";
    if (!key) return;

    Array.from(select.options).forEach(option => {
      if (!option.selected || !option.value || option.value === "All") return;

      // Skip the value being removed
      if (key === excludeKey && option.value === excludeValue) return;

      url.searchParams.append(key, option.value);
    });
  });

  return url.toString();
}

function initActiveFilterPills(view: Element): void {
  const bottom = view.querySelector(".js-searchfilter-bottom");
  if (!bottom) return;

  const selects = view.querySelectorAll<HTMLSelectElement>(
    ".bsi-searchfilter__top .js-multiselect select"
  );

  const activeFilters: { label: string; key: string; value: string }[] = [];

  selects.forEach(select => {
    const container = select.closest<HTMLElement>(".js-multiselect");
    const key = container?.dataset.filterKey ?? "";

    Array.from(select.options).forEach(option => {
      if (
        option.selected &&
        option.value &&
        option.value !== "" &&
        option.value !== "All"
      ) {
        activeFilters.push({
          label: option.textContent?.trim() || option.value,
          key,
          value: option.value
        });
      }
    });
  });

  if (activeFilters.length === 0) {
    bottom.innerHTML = "";
    return;
  }

  const pillsContainer = document.createElement("div");
  pillsContainer.className = "bsi-searchfilter__pills";

  activeFilters.forEach(({ label, key, value }) => {
    const pill = document.createElement("button");
    pill.type = "button";
    pill.className =
      "bsi-button bsi-button--tertiary bsi-pill bsi-pill--default";
    pill.setAttribute("aria-label", `${label} entfernen`);
    pill.innerHTML = `<span class="bsi-button__text">${label}</span>${svgIcon("cross")}`;

    pill.addEventListener("click", () => {
      // Navigate to search URL with this specific filter removed
      window.location.href = buildSearchUrl(view, key, value);
    });

    pillsContainer.appendChild(pill);
  });

  const resetBtn = document.createElement("button");
  resetBtn.type = "button";
  resetBtn.className =
    "bsi-button bsi-button--tertiary bsi-searchfilter__reset";
  resetBtn.innerHTML = `${svgIcon("cross")}<span class="bsi-button__text">Alle Filter zurücksetzen</span>`;

  resetBtn.addEventListener("click", () => {
    // Navigate with only the search term, no filters
    const url = new URL(window.location.pathname, window.location.origin);
    const searchTerm = new URLSearchParams(window.location.search).get(
      "search"
    );
    if (searchTerm) url.searchParams.set("search", searchTerm);
    window.location.href = url.toString();
  });

  bottom.innerHTML = "";
  bottom.appendChild(pillsContainer);
  bottom.appendChild(resetBtn);
}

function initMobileToggle(view: Element): void {
  const toggle = view.querySelector<HTMLButtonElement>(
    ".js-searchfilter-toggle"
  );
  if (!toggle || toggle.dataset.initialized) return;

  toggle.dataset.initialized = "true";
  toggle.addEventListener("click", () => {
    const expanded = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", String(!expanded));
  });
}

declare const Drupal: {
  behaviors: Record<
    string,
    { attach: (context?: Element, settings?: unknown) => void }
  >;
};
