const rootSelector = "[data-js-dropdown]";
const triggerSelector = "[data-js-dropdown-trigger]";
const listSelector = "[data-js-dropdown-list]";
const itemSelector = ".bsi-dropdown__link";
const triggerTextSelector = ".bsi-button__text";
const initializedAttribute = "data-js-dropdown-initialized";

// Retrieves the dropdown list element within the specified root element
function getList(root: HTMLElement): HTMLElement | null {
  return root.querySelector<HTMLElement>(listSelector);
}

// Retrieves the dropdown trigger button element within the specified root element
function getTrigger(root: HTMLElement): HTMLButtonElement | null {
  return root.querySelector<HTMLButtonElement>(triggerSelector);
}

// Closes the dropdown by setting the aria-expanded attribute to false and hiding the list
function closeDropdown(root: HTMLElement): void {
  const trigger = getTrigger(root);
  const list = getList(root);

  if (!trigger || !list) return;

  trigger.setAttribute("aria-expanded", "false");
  list.hidden = true;
}

// Toggles the dropdown state by updating the aria-expanded attribute and the visibility of the list
function toggleDropdown(root: HTMLElement): void {
  const trigger = getTrigger(root);
  const list = getList(root);

  if (!trigger || !list) return;

  const isOpen = trigger.getAttribute("aria-expanded") === "true";

  trigger.setAttribute("aria-expanded", String(!isOpen));
  list.hidden = isOpen;
}

// Updates the trigger text with the selected dropdown item
function updateTriggerText(root: HTMLElement, selectedItem: HTMLElement): void {
  const triggerText = root.querySelector<HTMLElement>(triggerTextSelector);

  if (!triggerText) return;

  triggerText.textContent = selectedItem.textContent?.trim() ?? "";
}

// Initializes the dropdown instance by setting up event listeners for the trigger and document events
function initDropdownInstance(root: HTMLElement): void {
  if (root.hasAttribute(initializedAttribute)) return;

  root.setAttribute(initializedAttribute, "true");

  // Set initial state of the dropdown
  const trigger = getTrigger(root);

  if (trigger) {
    trigger.addEventListener("click", event => {
      event.stopPropagation();
      toggleDropdown(root);
    });
  }

  // Handle dropdown item selection
  const items = root.querySelectorAll<HTMLElement>(itemSelector);

  items.forEach(item => {
    item.addEventListener("click", () => {
      updateTriggerText(root, item);
      closeDropdown(root);
    });
  });

  // Close the dropdown when clicking outside of it
  document.addEventListener("click", event => {
    if (!root.contains(event.target as Node)) {
      closeDropdown(root);
    }
  });

  // Close the dropdown when pressing the Escape key
  document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
      closeDropdown(root);
    }
  });
}

// Initializes all dropdown instances within the specified context (defaulting to the entire document)
export function initDropdown(context: ParentNode = document): void {
  context
    .querySelectorAll<HTMLElement>(rootSelector)
    .forEach(root => initDropdownInstance(root));
}

// Initialize dropdowns when the DOM content is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  initDropdown();
});

// Extend the global Window interface to include the Drupal behaviors property
declare global {
  interface Window {
    Drupal?: {
      behaviors?: Record<string, { attach: (context: ParentNode) => void }>;
    };
  }
}

// Attach the initDropdown function to the Drupal behaviors if available
if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiDropdown = {
    attach(context: ParentNode) {
      initDropdown(context);
    }
  };
}
