const rootSelector = "[data-js-sidemenu]";
const triggerSelector = "[data-js-sidemenu-trigger]";
const panelSelector = '[data-js-sidemenu-panel="root"]';
const closeSelector = "[data-js-sidemenu-close]";
const toggleSelector = "[data-js-sidemenu-toggle]";
const initializedAttribute = "data-js-sidemenu-initialized";

// Retrieves the sidemenu panel element within the specified root element
function getPanel(root: HTMLElement): HTMLElement | null {
  return root.querySelector<HTMLElement>(panelSelector);
}

// Retrieves the sidemenu trigger button within the specified root element
function getTrigger(root: HTMLElement): HTMLButtonElement | null {
  return root.querySelector<HTMLButtonElement>(triggerSelector);
}

// Retrieves the sidemenu close button within the specified root element
function getCloseButtons(root: HTMLElement): NodeListOf<HTMLElement> {
  return root.querySelectorAll<HTMLButtonElement>(closeSelector);
}

// Closes the sidemenu by updating the aria-expanded state and hiding the panel
function closeSidemenu(root: HTMLElement): void {
  const trigger = getTrigger(root);
  const panel = getPanel(root);

  if (!trigger || !panel) return;

  trigger.setAttribute("aria-expanded", "false");
  panel.hidden = true;
}

// Opens the sidemenu by updating the aria-expanded state and showing the panel
function openSidemenu(root: HTMLElement): void {
  const trigger = getTrigger(root);
  const panel = getPanel(root);

  if (!trigger || !panel) return;

  trigger.setAttribute("aria-expanded", "true");
  panel.hidden = false;
}

// Toggles the sidemenu open/closed state
function toggleSidemenu(root: HTMLElement): void {
  const trigger = getTrigger(root);
  const panel = getPanel(root);

  if (!trigger || !panel) return;

  const isOpen = trigger.getAttribute("aria-expanded") === "true";

  if (isOpen) {
    closeSidemenu(root);
  } else {
    openSidemenu(root);
  }
}

// Toggles an individual submenu
function toggleSubmenu(toggle: HTMLButtonElement): void {
  const panelId = toggle.getAttribute("aria-controls");

  if (!panelId) return;

  const panel = document.getElementById(panelId);

  if (!panel) return;

  const isOpen = toggle.getAttribute("aria-expanded") === "true";

  toggle.setAttribute("aria-expanded", String(!isOpen));
  panel.hidden = isOpen;
}

// Initializes a sidemenu instance by setting up event listeners
function initSidemenuInstance(root: HTMLElement): void {
  if (root.hasAttribute(initializedAttribute)) return;

  root.setAttribute(initializedAttribute, "true");

  // Set initial state of the sidemenu
  const trigger = getTrigger(root);

  if (trigger) {
    trigger.addEventListener("click", event => {
      event.stopPropagation();
      toggleSidemenu(root);
    });
  }

  // Handle close button
  const closeButtons = getCloseButtons(root);

  closeButtons.forEach(button => {
    button.addEventListener("click", () => {
      closeSidemenu(root);
    });
  });

  // Handle nested navigation toggles
  const toggles = root.querySelectorAll<HTMLButtonElement>(toggleSelector);

  toggles.forEach(toggle => {
    toggle.addEventListener("click", event => {
      event.stopPropagation();
      toggleSubmenu(toggle);
    });
  });

  // Close the sidemenu when clicking outside of it
  document.addEventListener("click", event => {
    if (!root.contains(event.target as Node)) {
      closeSidemenu(root);
    }
  });

  // Close the sidemenu when pressing the Escape key
  document.addEventListener("keydown", event => {
    if (event.key === "Escape") {
      closeSidemenu(root);
    }
  });
}

// Initializes all sidemenu instances within the specified context
export function initSidemenu(context: ParentNode = document): void {
  context
    .querySelectorAll<HTMLElement>(rootSelector)
    .forEach(root => initSidemenuInstance(root));
}

// Initialize sidemenus when the DOM content is fully loaded
document.addEventListener("DOMContentLoaded", () => {
  initSidemenu();
});

// Extend the global Window interface to include the Drupal behaviors property
declare global {
  interface Window {
    Drupal?: {
      behaviors?: Record<string, { attach: (context: ParentNode) => void }>;
    };
  }
}

// Attach the initSidemenu function to the Drupal behaviors if available
if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiSidemenu = {
    attach(context: ParentNode) {
      initSidemenu(context);
    }
  };
}
