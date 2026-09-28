import { headingTemplate } from "@atoms/heading/heading.template";
import { imageTemplate } from "@atoms/image/image.template";
import { html, nsp } from "@globals/index";
import {
  cardTemplate,
  type CardTemplateArgs
} from "@molecules/card/card.template";
import { linkButtonTemplate } from "@molecules/link-button/link-button.template";
import { buttonTemplate } from "@molecules/button/button.template";
import {
  heroSliderDefaultItems,
  heroSliderDefaultOverlay,
  type HeroSliderItem
} from "./hero-slider.constants";

export type { HeroSliderItem };

export interface HeroSliderTemplateArgs {
  ariaLabel?: string;
  items?: HeroSliderItem[];
  overlay?: CardTemplateArgs;
  showOverlay?: boolean;
}

export function heroSliderTemplate({
  ariaLabel = "Buehnen-Slider",
  items = heroSliderDefaultItems,
  overlay,
  showOverlay = false
}: HeroSliderTemplateArgs = {}): string {
  const activeOverlay =
    overlay ?? (showOverlay ? heroSliderDefaultOverlay : undefined);
  // Overlay card
  const overlayCard = activeOverlay
    ? cardTemplate({
        ...activeOverlay,
        additionalCardAttributes: { "data-theme": "neutral-50" },
        cardExtraClasses: "hero-slider__card",
        hasDivider: true
      })
    : "";

  return html`
    <section
      class="splide ${nsp("hero-slider")}"
      aria-label="${ariaLabel}"
      data-bsi-hero-slider
    >
      <div class="splide__track">
        <ul class="splide__list">
          ${items
            .map(
              item => html`
                <li class="splide__slide ${nsp("hero-slider__slide")}">
                  ${imageTemplate({
                    imageSrc: item.imageUrl,
                    imageAlt: item.imageAlt ?? "",
                    imageLoading: "eager",
                    imageExtraClasses: "hero-slider__image"
                  })}

                  <div class="${nsp("hero-slider__inner")}">
                    <div class="${nsp("hero-slider__content")}">
                      ${headingTemplate({
                        headingText: item.title,
                        layout: 2,
                        style: 2,
                        headingExtraClasses: "hero-slider__title"
                      })}

                      <p class="${nsp("hero-slider__text")}">${item.text}</p>

                      ${
                        item.ctaText
                          ? linkButtonTemplate({
                              linkButtonUrl: item.ctaUrl ?? "#",
                              linkButtonText: item.ctaText,
                              linkButtonVariant: "secondary",
                              linkButtonIconBefore: "chevron-right"
                            })
                          : ""
                      }
                    </div>
                  </div>
                </li>
              `
            )
            .join("")}
        </ul>
      </div>
      <ul
        class="splide__pagination ${nsp("hero-slider__pagination")} ${nsp("grid")}"
      ></ul>

      <!-- Arrows bleiben -->
      <div class="${nsp("hero-slider__controls")}">
        ${buttonTemplate({
          buttonIconBefore: "chevron-left",
          buttonAriaLabel: "Vorherige Slide",
          buttonVariant: "primary",
          buttonExtraClasses: "hero-slider__prev"
        })}
        ${buttonTemplate({
          buttonIconBefore: "chevron-right",
          buttonAriaLabel: "Nächster Slide",
          buttonVariant: "primary",
          buttonExtraClasses: "hero-slider__next"
        })}
      </div>

      <!--  Overlay card -->
      ${
        overlayCard
          ? `
              <div class="${nsp("hero-slider__overlay")}">${overlayCard}</div>`
          : ""
      }
    </section>
  `;
}
