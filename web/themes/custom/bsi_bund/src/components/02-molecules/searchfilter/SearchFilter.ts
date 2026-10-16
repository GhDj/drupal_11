/**
 * Global search filter behavior:
 * - Initializes custom multi-select dropdown filters
 * - Renders active filter pills from selected values
 * - Provides "Alle Filter zurücksetzen" reset link
 * - Mobile toggle to show/hide filter sections
 */

import MultiSelectDropdown from "./MultiSelectDropdown";

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
    pill.innerHTML = `<span class="bsi-button__text">${label}</span>
      <span class="bsi-icon bsi-icon--cross bsi-button__icon--after" aria-hidden="true"></span>`;

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
      const form = select.closest("form");
      if (form) {
        const submit = form.querySelector<HTMLButtonElement>(
          '[data-drupal-selector="edit-submit-search"]'
        );
        if (submit) submit.click();
      }
    });

    pillsContainer.appendChild(pill);
  });

  const resetBtn = document.createElement("button");
  resetBtn.type = "button";
  resetBtn.className = "bsi-button bsi-button--tertiary bsi-searchfilter__reset";
  resetBtn.innerHTML = `<span class="bsi-icon bsi-icon--cross bsi-button__icon--before" aria-hidden="true"></span>
    <span class="bsi-button__text">Alle Filter zurücksetzen</span>`;
  resetBtn.addEventListener("click", () => {
    const form = view.querySelector<HTMLFormElement>("form");
    if (form) {
      const resetButton = form.querySelector<HTMLButtonElement>(
        '[data-drupal-selector="edit-reset"]'
      );
      if (resetButton) {
        resetButton.click();
      }
    }
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
