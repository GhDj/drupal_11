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
    const track = this.el.querySelector<HTMLElement>(".splide__track");
    if (!track) return;

    const trackHeight = track.offsetHeight;

    this.el.querySelectorAll<HTMLElement>(".splide__slide").forEach(slide => {
      const image = slide.querySelector<HTMLElement>(".bsi-opener__image");
      const contentGrid = slide.querySelector<HTMLElement>(
        ".bsi-opener__content-grid"
      );
      if (!image || !contentGrid) return;

      // marginTop is negative (calc(var(--bsi-space-7xl) * -1)); subtracting it adds the overlap back
      const marginTop = parseFloat(getComputedStyle(contentGrid).marginTop);
      contentGrid.style.height = `${trackHeight - image.offsetHeight - marginTop}px`;
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
