import Splide from "@splidejs/splide";
import { breakpoint } from "@globals/foundation/variables";
const parseToNumbers = (value: string): number =>
  Number.parseInt(value, 10) - 1;

/* ---------------------------------
  CARD SLIDER CLASS
--------------------------------- */

export default class CardSlider {
  private el: HTMLElement;
  private splide: Splide | null = null;

  constructor(el: HTMLElement) {
    this.el = el;

    if (!this.el) return;

    // keine doppelte Initialisierung (Storybook / Re-render)
    if (this.el.dataset.initialized === "true") return;

    this.el.dataset.initialized = "true";

    this.init();
  }

  init() {
    this.splide = new Splide(this.el, {
      type: "loop",
      perPage: 3,
      perMove: 1,
      gap: 32,
      arrows: false,
      pagination: true,
      drag: true,
      keyboard: "focused",
      breakpoints: {
        [parseToNumbers(breakpoint.lg)]: {
          perPage: 2,
          gap: 24
        },
        [parseToNumbers(breakpoint.md)]: {
          perPage: 1,
          gap: 24
        }
      }
    });

    this.splide.on("mounted", () => {
      this.el.querySelectorAll<HTMLElement>(".splide__slide").forEach(slide => {
        slide.removeAttribute("role");
      });
    });

    this.splide.mount();
    this.bindSliderControls();
  }

  bindSliderControls() {
    const prevBtn = this.el.querySelector(".bsi-card-slider__prev");
    const nextBtn = this.el.querySelector(".bsi-card-slider__next");

    prevBtn?.addEventListener("click", () => {
      this.splide?.go("<");
    });

    nextBtn?.addEventListener("click", () => {
      this.splide?.go(">");
    });
  }
}

// DRUPAL BEHAVIOR (mit Guard + once)
if (window.Drupal?.behaviors && typeof once === "function") {
  window.Drupal.behaviors.bsiCardSlider = {
    attach(context: ParentNode) {
      once(
        "data-js-card-slider",
        context.querySelectorAll("[data-js-card-slider]")
      ).forEach((el: Element) => {
        new CardSlider(el as HTMLElement);
      });
    }
  };
}

//  FALLBACK (Storybook / Static)
if (!window.Drupal) {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-js-card-slider]").forEach(el => {
      new CardSlider(el as HTMLElement);
    });
  });
}
