import Splide from "@splidejs/splide";

/* ---------------------------------
  IMAGE SLIDER CLASS
--------------------------------- */

export default class ImageSlider {
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
      type: "slide",
      perPage: 1,
      arrows: false,
      pagination: true,
      paginationClass: "bsi-image-slider__pagination",
      pageClass: "bsi-image-slider__pagination-page"
    });

    this.splide.on("mounted", () => {
      this.el.querySelectorAll<HTMLElement>(".splide__slide").forEach(slide => {
        slide.removeAttribute("role");
      });

      this.updateButtons();
    });

    this.splide.on("mounted moved", () => {
      this.updateButtons();
    });

    this.bindSliderControls();
    this.splide.mount();
  }

  private updateButtons = () => {
    if (!this.splide) return;

    const prevBtn = this.el.querySelector<HTMLButtonElement>(
      ".bsi-image-slider__prev"
    );

    const nextBtn = this.el.querySelector<HTMLButtonElement>(
      ".bsi-image-slider__next"
    );

    if (prevBtn) {
      prevBtn.disabled = this.splide.Components.Controller.getPrev() === -1;
    }

    if (nextBtn) {
      nextBtn.disabled = this.splide.Components.Controller.getNext() === -1;
    }
  };

  private bindSliderControls = () => {
    const prevBtn = this.el.querySelector<HTMLButtonElement>(
      ".bsi-image-slider__prev"
    );

    const nextBtn = this.el.querySelector<HTMLButtonElement>(
      ".bsi-image-slider__next"
    );

    prevBtn?.addEventListener("click", () => {
      this.splide?.go("<");
    });

    nextBtn?.addEventListener("click", () => {
      this.splide?.go(">");
    });
  };
}

// DRUPAL BEHAVIOR (mit Guard + once)
if (window.Drupal?.behaviors && typeof once === "function") {
  window.Drupal.behaviors.bsiImageslider = {
    attach(context: ParentNode) {
      once(
        "bsi-image-slider",
        context.querySelectorAll("[data-bsi-image-slider]")
      ).forEach((el: Element) => {
        new ImageSlider(el as HTMLElement);
      });
    }
  };
}

// FALLBACK (Storybook / Static)
if (!window.Drupal) {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-bsi-image-slider]").forEach(el => {
      new ImageSlider(el as HTMLElement);
    });
  });
}
