const selector = "[data-bsi-accordion]";
const triggerSelector = "[data-bsi-accordion-trigger]";
const initializedAttribute = "data-bsi-accordion-initialized";
const itemClass = ".bsi-accordion__item";

function getPanel(trigger: HTMLButtonElement): HTMLElement | null {
  return (
    trigger
      .closest(itemClass)
      ?.querySelector<HTMLElement>(".bsi-accordion__panel") ?? null
  );
}

//  Toggle
function toggle(trigger: HTMLButtonElement, open: boolean) {
  const item = trigger.closest(itemClass);
  const panel = getPanel(trigger);

  if (!item || !panel) return;

  trigger.setAttribute("aria-expanded", String(open));
  panel.hidden = !open;

  item.classList.toggle("is-open", open);
}

//  Onclick
function onClick(this: HTMLButtonElement) {
  const isOpen = this.getAttribute("aria-expanded") === "true";

  toggle(this, !isOpen);
}

//  Init single
function initAccordionInstance(wrapper: HTMLElement) {
  if (wrapper.hasAttribute(initializedAttribute)) return;

  wrapper.setAttribute(initializedAttribute, "true");

  const triggers = wrapper.querySelectorAll<HTMLButtonElement>(triggerSelector);

  triggers.forEach(trigger => {
    trigger.addEventListener("click", onClick);

    // Adopt initial state
    const isOpen = trigger.getAttribute("aria-expanded") === "true";
    toggle(trigger, isOpen);
  });
}

//  Init all
export function initAccordion(context: ParentNode = document) {
  context
    .querySelectorAll<HTMLElement>(selector)
    .forEach(wrapper => initAccordionInstance(wrapper));
}

//  Auto init
document.addEventListener("DOMContentLoaded", () => {
  initAccordion();
});

//  Drupal support
declare global {
  interface Window {
    Drupal?: {
      behaviors?: Record<string, { attach: (context: ParentNode) => void }>;
    };
  }
}

if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiAccordion = {
    attach(context: ParentNode) {
      initAccordion(context);
    }
  };
}
