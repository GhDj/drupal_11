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
    const slideCount = this.el.querySelectorAll(
      ".splide__slide"
    ).length;

    const perPageDesktop = 3;
    const perPageTablet = 2;

    // Disable slider when there are not enough items to scroll.
    const needsSlider = slideCount > perPageDesktop;

    this.splide = new Splide(this.el, {
      type: needsSlider ? "loop" : "slide",
      perPage: perPageDesktop,
      perMove: 1,
      gap: 32,
      arrows: false,
      pagination: needsSlider,
      drag: needsSlider,
      keyboard: needsSlider ? "focused" : false,
      breakpoints: {
        [parseToNumbers(breakpoint.lg)]: {
          perPage: perPageTablet,
          gap: 24,
          pagination: slideCount > perPageTablet,
          drag: slideCount > perPageTablet
        },
        [parseToNumbers(breakpoint.md)]: {
          perPage: 1,
          gap: 24,
          pagination: slideCount > 1,
          drag: slideCount > 1
        }
      }
    });

    this.splide.on("mounted", () => {
      this.el.querySelectorAll<HTMLElement>(".splide__slide").forEach(slide => {
        slide.removeAttribute("role");
      });
    });

    this.splide.mount();

    if (needsSlider) {
      this.bindSliderControls();
    } else {
      this.hideControls();
    }
  }

  hideControls() {
    const controls = this.el.querySelector<HTMLElement>(
      ".bsi-card-slider__controls"
    );
    if (controls) {
      controls.style.display = "none";
    }
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
