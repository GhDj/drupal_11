import { CountUp } from "countup.js";

const selector = "[data-bsi-facts-value]";
const initializedAttribute = "data-bsi-facts-initialized";
const defaultDuration = 3;

function initFigureValue(element: HTMLElement): void {
  if (element.hasAttribute(initializedAttribute)) {
    return;
  }

  const text = element.textContent?.trim() ?? "";
  const numberMatch = /[\d.]+/.exec(text);
  const numberText = numberMatch?.[0] ?? "";
  const prefix = text.substring(0, text.indexOf(numberText));
  const suffix = text.substring(text.indexOf(numberText) + numberText.length);
  const supportsAutoAnimate = "IntersectionObserver" in window;

  element.setAttribute(initializedAttribute, "true");

  if (!numberText) {
    return;
  }

  element.textContent = numberText;

  const countUp = new CountUp(element, null, {
    startVal: 0,
    duration: defaultDuration,
    decimal: ",",
    separator: ".",
    autoAnimate: supportsAutoAnimate,
    autoAnimateDelay: 0,
    autoAnimateOnce: true,
    prefix: prefix,
    suffix: suffix,
    onCompleteCallback: () => {
      element.textContent = text;
    }
  });

  if (countUp.error) {
    element.textContent = text;
    return;
  }

  if (!supportsAutoAnimate) {
    countUp.start();
  }
}

export function initFactsAndFigures(context: ParentNode = document): void {
  const values = Array.from(context.querySelectorAll<HTMLElement>(selector));

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  values.forEach(initFigureValue);
}

document.addEventListener("DOMContentLoaded", () => initFactsAndFigures());

declare global {
  interface Window {
    Drupal?: {
      behaviors?: Record<string, { attach: (context: ParentNode) => void }>;
    };
  }
}

if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiFactsAndFigures = {
    attach(context: ParentNode) {
      initFactsAndFigures(context);
    }
  };
}
