import Splide from "@splidejs/splide";

/* ---------------------------------
  HERO SLIDER CLASS
--------------------------------- */

export default class HeroSlider {
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
      perPage: 1,
      arrows: false,
      pagination: true,

      paginationClass: "bsi-hero-slider__pagination",
      pageClass: "bsi-hero-slider__pagination-page"
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
    const prevBtn = this.el.querySelector(".bsi-hero-slider__prev");
    const nextBtn = this.el.querySelector(".bsi-hero-slider__next");

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
  window.Drupal.behaviors.bsiHeroSlider = {
    attach(context: ParentNode) {
      once(
        "bsi-hero-slider",
        context.querySelectorAll("[data-bsi-hero-slider]")
      ).forEach((el: Element) => {
        new HeroSlider(el as HTMLElement);
      });
    }
  };
}

//  FALLBACK (Storybook / Static)
if (!window.Drupal) {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-bsi-hero-slider]").forEach(el => {
      new HeroSlider(el as HTMLElement);
    });
  });
}
