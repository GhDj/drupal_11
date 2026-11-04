import Splide from "@splidejs/splide";

/* ---------------------------------
  OPENER SLIDER CLASS
--------------------------------- */

export default class Opener {
  private el: HTMLElement;
  private splide: Splide | null = null;
  private resizeObserver: ResizeObserver | null = null;

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

      paginationClass: "bsi-opener__pagination",
      pageClass: "bsi-opener__pagination-page"
    });

    this.splide.on("mounted", () => {
      this.el.querySelectorAll<HTMLElement>(".splide__slide").forEach(slide => {
        slide.removeAttribute("role");
      });
      this.updateContentGridHeight();
    });

    this.splide.mount();
    this.bindSliderControls();

    this.resizeObserver = new ResizeObserver(() => {
      this.updateContentGridHeight();
    });
    this.resizeObserver.observe(this.el);
  }

  private updateContentGridHeight(): void {
    const contentGrids = Array.from(
      this.el.querySelectorAll<HTMLElement>(".bsi-opener__content-grid")
    );

    contentGrids.forEach(grid => {
      grid.style.height = "auto";
    });

    const maxHeight = Math.max(...contentGrids.map(grid => grid.offsetHeight));

    contentGrids.forEach(grid => {
      grid.style.height = `${maxHeight}px`;
    });
  }

  bindSliderControls() {
    const prevBtn = this.el.querySelector(".bsi-opener__prev");
    const nextBtn = this.el.querySelector(".bsi-opener__next");

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
  window.Drupal.behaviors.bsiOpenerSlider = {
    attach(context: ParentNode) {
      once(
        "bsi-opener--slider",
        context.querySelectorAll("[data-bsi-opener-slider]")
      ).forEach((el: Element) => {
        new Opener(el as HTMLElement);
      });
    }
  };
}

//  FALLBACK (Storybook / Static)
if (!window.Drupal) {
  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-bsi-opener-slider]").forEach(el => {
      new Opener(el as HTMLElement);
    });
  });
}
