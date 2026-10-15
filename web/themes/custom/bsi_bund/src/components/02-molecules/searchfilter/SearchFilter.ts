/**
 * Global search filter behavior:
 * - Renders active filter pills from selected facet values
 * - Provides "Alle Filter zurücksetzen" reset link
 * - Mobile toggle to show/hide filter dropdowns
 */

const PILL_CLASS = "bsi-pill";
const PILL_VARIANT = "bsi-pill--default";

export default function SearchFilter(): void {
  const view = document.querySelector(
    ".view-display-id-block_search_global"
  );
  if (!view) return;

  initActiveFilterPills(view);
  initMobileToggle(view);
}

function initActiveFilterPills(view: Element): void {
  const bottom = view.querySelector(".js-searchfilter-bottom");
  if (!bottom) return;

  const selects = view.querySelectorAll<HTMLSelectElement>(
    ".bsi-searchfilter__top .bsi-search-dropdown select"
  );

  const activeFilters: { label: string; select: HTMLSelectElement; value: string }[] = [];

  selects.forEach(select => {
    const selectedOptions = Array.from(select.selectedOptions);
    selectedOptions.forEach(option => {
      if (option.value && option.value !== "" && option.value !== "All") {
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

  const heading = document.createElement("span");
  heading.className = "bsi-searchfilter__pills-label";
  heading.textContent = "Aktive Filter";
  pillsContainer.appendChild(heading);

  activeFilters.forEach(({ label, select, value }) => {
    const pill = document.createElement("button");
    pill.type = "button";
    pill.className = `bsi-button bsi-button--tertiary ${PILL_CLASS} ${PILL_VARIANT}`;
    pill.setAttribute("aria-label", `${label} entfernen`);
    pill.innerHTML = `<span class="bsi-button__text">${label}</span>
      <span class="bsi-icon bsi-icon--cross bsi-button__icon--after" aria-hidden="true"></span>`;

    pill.addEventListener("click", () => {
      // Deselect the option
      const option = select.querySelector<HTMLOptionElement>(
        `option[value="${CSS.escape(value)}"]`
      );
      if (option) option.selected = false;

      // Trigger change and submit
      select.dispatchEvent(new Event("change", { bubbles: true }));
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
  if (!toggle) return;

  toggle.addEventListener("click", () => {
    const expanded = toggle.getAttribute("aria-expanded") === "true";
    toggle.setAttribute("aria-expanded", String(!expanded));
  });
}
