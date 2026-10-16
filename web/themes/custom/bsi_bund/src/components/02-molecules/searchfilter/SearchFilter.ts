/**
 * Global search filter behavior:
 * - Initializes custom multi-select dropdown filters
 * - Renders active filter pills from selected values
 * - Pill removal and reset work by URL manipulation (reload without filters)
 * - Mobile toggle to show/hide filter sections
 */

import MultiSelectDropdown from "./MultiSelectDropdown";

// SVG icon helper — uses the theme's icon sprite
function svgIcon(name: string): string {
  const existing = document.querySelector<SVGUseElement>(".bsi-icon use");
  const base = existing?.getAttribute("href")?.replace(/#.*$/, "")
    || "/themes/custom/bsi_bund/dist/icons.svg";
  return `<svg class="bsi-icon" aria-hidden="true" focusable="false"><use href="${base}#${name}"></use></svg>`;
}

export default function SearchFilter(): void {
  applySearchFilter();

  if (typeof Drupal !== "undefined" && Drupal.behaviors) {
    Drupal.behaviors.bsiSearchFilter = {
      attach() {
        applySearchFilter();
      },
    };
  }
}

function applySearchFilter(): void {
  const view = document.querySelector(".view-display-id-block_search_global");
  if (!view) return;

  MultiSelectDropdown();
  initActiveFilterPills(view);
  initMobileToggle(view);
}

/**
 * Get current filter state from URL query parameters.
 * BEF facets use identifiers like audiences[], topics[], year[], type[].
 */
function getActiveFiltersFromUrl(): Map<string, { values: string[]; labels: Map<string, string> }> {
  const params = new URLSearchParams(window.location.search);
  const filterKeys = ["type", "topics", "audiences", "year"];
  const result = new Map<string, { values: string[]; labels: Map<string, string> }>();

  filterKeys.forEach(key => {
    const values = params.getAll(`${key}[]`).concat(params.getAll(key));
    if (values.length > 0) {
      result.set(key, { values, labels: new Map() });
    }
  });

  return result;
}

function initActiveFilterPills(view: Element): void {
  const bottom = view.querySelector(".js-searchfilter-bottom");
  if (!bottom) return;

  // Collect from both URL params and select state
  const selects = view.querySelectorAll<HTMLSelectElement>(
    ".bsi-searchfilter__top .js-multiselect select"
  );

  const activeFilters: { label: string; key: string; value: string }[] = [];

  selects.forEach(select => {
    const container = select.closest<HTMLElement>(".js-multiselect");
    const key = container?.dataset.filterKey || "";

    Array.from(select.options).forEach(option => {
      if (option.selected && option.value && option.value !== "" && option.value !== "All") {
        activeFilters.push({
          label: option.textContent?.trim() || option.value,
          key,
          value: option.value,
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
    pill.className = "bsi-button bsi-button--tertiary bsi-pill bsi-pill--default";
    pill.setAttribute("aria-label", `${label} entfernen`);
    pill.innerHTML = `<span class="bsi-button__text">${label}</span>${svgIcon("cross")}`;

    pill.addEventListener("click", () => {
      // Remove this specific filter value from URL and reload
      const url = new URL(window.location.href);
      const allValues = url.searchParams.getAll(`${key}[]`);
      url.searchParams.delete(`${key}[]`);
      url.searchParams.delete(key);
      allValues.filter(v => v !== value).forEach(v => url.searchParams.append(`${key}[]`, v));
      window.location.href = url.toString();
    });

    pillsContainer.appendChild(pill);
  });

  const resetBtn = document.createElement("button");
  resetBtn.type = "button";
  resetBtn.className = "bsi-button bsi-button--tertiary bsi-searchfilter__reset";
  resetBtn.innerHTML = `${svgIcon("cross")}<span class="bsi-button__text">Alle Filter zurücksetzen</span>`;

  resetBtn.addEventListener("click", () => {
    // Keep only the search term, remove all filter params
    const url = new URL(window.location.href);
    const searchTerm = url.searchParams.get("search");
    const newUrl = new URL(window.location.pathname, window.location.origin);
    if (searchTerm) {
      newUrl.searchParams.set("search", searchTerm);
    }
    window.location.href = newUrl.toString();
  });

  bottom.innerHTML = "";
  bottom.appendChild(pillsContainer);
  bottom.appendChild(resetBtn);
}

function initMobileToggle(view: Element): void {
  const toggle = view.querySelector<HTMLButtonElement>(".js-searchfilter-toggle");
  if (!toggle || toggle.dataset.initialized) return;

  toggle.dataset.initialized = "true";
  toggle.addEventListener("click", () => {
    const expanded = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", String(!expanded));
  });
}

declare const Drupal: {
  behaviors: Record<string, { attach: (context?: Element, settings?: unknown) => void }>;
};
