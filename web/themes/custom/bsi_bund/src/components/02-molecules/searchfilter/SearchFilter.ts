/**
 * Global search filter behavior:
 * - Initializes custom multi-select dropdown filters
 * - Renders active filter pills from selected values
 * - Provides "Alle Filter zurücksetzen" reset link
 * - Mobile toggle to show/hide filter sections
 */

import MultiSelectDropdown from "./MultiSelectDropdown";

// SVG icon helper — uses the theme's icon sprite
function svgIcon(name: string): string {
  const iconPath = document.querySelector<HTMLElement>(".bsi-icon use")
    ?.getAttribute("href")?.replace(/#.*$/, "") || "/themes/custom/bsi_bund/dist/icons.svg";
  return `<svg class="bsi-icon" aria-hidden="true" focusable="false"><use href="${iconPath}#${name}"></use></svg>`;
}

export default function SearchFilter(): void {
  applySearchFilter();

  // Re-apply after Drupal AJAX updates
  if (typeof Drupal !== "undefined" && Drupal.behaviors) {
    Drupal.behaviors.bsiSearchFilter = {
      attach() {
        applySearchFilter();
      },
    };
  }
}

function applySearchFilter(): void {
  const view = document.querySelector(
    ".view-display-id-block_search_global"
  );
  if (!view) return;

  MultiSelectDropdown();
  initActiveFilterPills(view);
  initMobileToggle(view);
}

function initActiveFilterPills(view: Element): void {
  const bottom = view.querySelector(".js-searchfilter-bottom");
  if (!bottom) return;

  // Collect selected options from hidden selects inside multiselect wrappers
  const selects = view.querySelectorAll<HTMLSelectElement>(
    ".bsi-searchfilter__top .js-multiselect select"
  );

  const activeFilters: { label: string; select: HTMLSelectElement; value: string }[] = [];

  selects.forEach(select => {
    Array.from(select.options).forEach(option => {
      if (option.selected && option.value && option.value !== "" && option.value !== "All") {
        activeFilters.push({
          label: option.textContent?.trim() || option.value,
          select,
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

  activeFilters.forEach(({ label, select, value }) => {
    const pill = document.createElement("button");
    pill.type = "button";
    pill.className = "bsi-button bsi-button--tertiary bsi-pill bsi-pill--default";
    pill.setAttribute("aria-label", `${label} entfernen`);
    pill.innerHTML = `<span class="bsi-button__text">${label}</span>${svgIcon("cross")}`;

    pill.addEventListener("click", () => {
      // Deselect the option
      const option = select.querySelector<HTMLOptionElement>(
        `option[value="${CSS.escape(value)}"]`
      );
      if (option) option.selected = false;

      // Also uncheck the multi-select checkbox
      const container = select.closest(".js-multiselect");
      const checkbox = container?.querySelector<HTMLInputElement>(
        `input[type="checkbox"][value="${CSS.escape(value)}"]`
      );
      if (checkbox) checkbox.checked = false;

      // Submit form
      submitForm(view);
    });

    pillsContainer.appendChild(pill);
  });

  const resetBtn = document.createElement("button");
  resetBtn.type = "button";
  resetBtn.className = "bsi-button bsi-button--tertiary bsi-searchfilter__reset";
  resetBtn.innerHTML = `${svgIcon("cross")}<span class="bsi-button__text">Alle Filter zurücksetzen</span>`;

  resetBtn.addEventListener("click", () => {
    // Deselect all options in all facet selects
    selects.forEach(select => {
      Array.from(select.options).forEach(opt => { opt.selected = false; });
    });

    // Uncheck all checkboxes in multi-select panels
    view.querySelectorAll<HTMLInputElement>(
      ".js-multiselect input[type='checkbox']"
    ).forEach(cb => { cb.checked = false; });

    // Submit form to reload with no filters
    submitForm(view);
  });

  bottom.innerHTML = "";
  bottom.appendChild(pillsContainer);
  bottom.appendChild(resetBtn);
}

function submitForm(view: Element): void {
  const form = view.querySelector<HTMLFormElement>("form");
  if (!form) return;

  // Try BEF submit button first, then generic submit
  const submit = form.querySelector<HTMLElement>(
    '[data-drupal-selector="edit-submit-search"], [type="submit"]'
  );
  if (submit) {
    (submit as HTMLButtonElement).click();
  } else {
    form.submit();
  }
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
