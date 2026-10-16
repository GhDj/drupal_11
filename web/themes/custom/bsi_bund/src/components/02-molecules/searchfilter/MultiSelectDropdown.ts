/**
 * Converts native <select multiple> elements inside .js-multiselect
 * containers into custom dropdown buttons with checkbox panels.
 *
 * Structure created:
 *   .bsi-multiselect
 *     <select> (hidden)
 *     button.bsi-multiselect__trigger (label + chevron)
 *     .bsi-multiselect__panel
 *       label > input[checkbox] + text (+ count)
 */

const NSP = "bsi";

export default function MultiSelectDropdown(): void {
  document.querySelectorAll<HTMLElement>(".js-multiselect").forEach(container => {
    if (container.dataset.initialized) return;
    container.dataset.initialized = "true";
    init(container);
  });
}

function init(container: HTMLElement): void {
  const select = container.querySelector<HTMLSelectElement>("select");
  if (!select) return;

  // Get the label text from the Drupal-rendered label or first option
  const drupalLabel = container.querySelector<HTMLLabelElement>("label");
  const labelText = drupalLabel?.textContent?.trim()
    || select.options[0]?.textContent?.trim()
    || "Filter";

  // Hide native select and label
  select.style.display = "none";
  if (drupalLabel) drupalLabel.style.display = "none";

  // Create trigger button
  const trigger = document.createElement("button");
  trigger.type = "button";
  trigger.className = `${NSP}-multiselect__trigger`;
  trigger.setAttribute("aria-expanded", "false");
  trigger.setAttribute("aria-haspopup", "listbox");
  trigger.innerHTML = `
    <span class="${NSP}-multiselect__label">${labelText}</span>
    <span class="${NSP}-multiselect__chevron" aria-hidden="true"></span>
  `;

  // Create dropdown panel
  const panel = document.createElement("div");
  panel.className = `${NSP}-multiselect__panel`;
  panel.setAttribute("role", "listbox");
  panel.setAttribute("aria-multiselectable", "true");
  panel.hidden = true;

  // Build checkbox options from select
  Array.from(select.options).forEach(option => {
    if (!option.value || option.value === "All") return;

    const item = document.createElement("label");
    item.className = `${NSP}-multiselect__option`;

    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.value = option.value;
    checkbox.checked = option.selected;
    checkbox.className = `${NSP}-multiselect__checkbox`;

    checkbox.addEventListener("change", () => {
      option.selected = checkbox.checked;
      select.dispatchEvent(new Event("change", { bubbles: true }));
    });

    const text = document.createElement("span");
    text.className = `${NSP}-multiselect__option-text`;
    text.textContent = option.textContent?.trim() || option.value;

    item.appendChild(checkbox);
    item.appendChild(text);
    panel.appendChild(item);
  });

  // Toggle dropdown
  trigger.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    const isOpen = trigger.getAttribute("aria-expanded") === "true";

    // Close all other dropdowns first
    document.querySelectorAll(".js-multiselect .bsi-multiselect__trigger[aria-expanded='true']").forEach(other => {
      if (other !== trigger) {
        other.setAttribute("aria-expanded", "false");
        (other.nextElementSibling as HTMLElement).hidden = true;
      }
    });

    trigger.setAttribute("aria-expanded", String(!isOpen));
    panel.hidden = isOpen;
  });

  // Close on outside click
  document.addEventListener("click", (e) => {
    if (!container.contains(e.target as Node)) {
      trigger.setAttribute("aria-expanded", "false");
      panel.hidden = true;
    }
  });

  // Close on Escape
  container.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      trigger.setAttribute("aria-expanded", "false");
      panel.hidden = true;
      trigger.focus();
    }
  });

  container.appendChild(trigger);
  container.appendChild(panel);
}
