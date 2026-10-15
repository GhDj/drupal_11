/**
 * Global search filter behavior:
 * - Renders active filter pills from checked facet checkboxes
 * - Provides "Alle Filter zurücksetzen" reset link
 * - Mobile toggle to show/hide filter sections
 */

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

  // Collect checked checkboxes from facet filters
  const checkboxes = view.querySelectorAll<HTMLInputElement>(
    ".bsi-searchfilter__filter input[type='checkbox']:checked"
  );

  if (checkboxes.length === 0) {
    bottom.innerHTML = "";
    return;
  }

  const pillsContainer = document.createElement("div");
  pillsContainer.className = "bsi-searchfilter__pills";

  const heading = document.createElement("span");
  heading.className = "bsi-searchfilter__pills-label";
  heading.textContent = "Aktive Filter";
  pillsContainer.appendChild(heading);

  checkboxes.forEach(checkbox => {
    const label = checkbox.parentElement?.querySelector("label");
    const labelText = label?.textContent?.replace(/\(\d+\)$/, "").trim() || checkbox.value;

    const pill = document.createElement("button");
    pill.type = "button";
    pill.className = "bsi-button bsi-button--tertiary bsi-pill bsi-pill--default";
    pill.setAttribute("aria-label", `${labelText} entfernen`);
    pill.innerHTML = `<span class="bsi-button__text">${labelText}</span>
      <span class="bsi-icon bsi-icon--cross bsi-button__icon--after" aria-hidden="true"></span>`;

    pill.addEventListener("click", () => {
      checkbox.checked = false;
      checkbox.dispatchEvent(new Event("change", { bubbles: true }));
      const form = checkbox.closest("form");
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
