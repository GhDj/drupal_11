// Globals
import { html, nsp } from "@globals/utils";

// Components
import { headingTemplate } from "@atoms/heading/heading.template";
import { imageTemplate } from "@atoms/image/image.template";
import { buttonTemplate } from "@molecules/button/button.template";
import { linkButtonTemplate } from "@molecules/link-button/link-button.template";

// Data
import { openerSliderDefaultItems } from "@organisms/opener/opener.constants";

export interface OpenerSliderItem {
  imageUrl: string;
  imageAlt?: string;
  title?: string;
  text: string;
  ctaText?: string;
  ctaUrl?: string;
}

export interface OpenerSliderTemplateArgs {
  items?: OpenerSliderItem[];
  pageTitle: string;
}

export function openerSliderTemplate({
  items = openerSliderDefaultItems,
  pageTitle = "Technologie & Forschung"
}: OpenerSliderTemplateArgs): string {
  const controls = html`
    <div class="${nsp("opener__controls")}">
      ${buttonTemplate({
        buttonIconBefore: "chevron-left",
        buttonAriaLabel: "Vorherige Slide",
        buttonVariant: "primary",
        buttonExtraClasses: "opener__prev"
      })}
      ${buttonTemplate({
        buttonIconBefore: "chevron-right",
        buttonAriaLabel: "Nächster Slide",
        buttonVariant: "primary",
        buttonExtraClasses: "opener__next"
      })}
    </div>
  `;

  return html`
    ${headingTemplate({
      headingText: pageTitle,
      layout: 1,
      style: 1,
      headingExtraClasses: "opener__page-title"
    })}
    <div class="splide" data-bsi-opener-slider>
      <div class="splide__track">
        <ul class="splide__list">
          ${items
            .map(
              item => html`
                <li class="splide__slide ${nsp("opener__slide")}">
                  <div class="${nsp("opener__content-wrapper")}">
                    ${imageTemplate({
                      imageSrc: item.imageUrl,
                      imageAlt: item.imageAlt ?? "",
                      imageLoading: "eager",
                      imageExtraClasses: "opener__image"
                    })}

                    <div class="${nsp("opener__content-grid")}">
                      <div class="${nsp("opener__content")}">
                        ${
                          item.title
                            ? headingTemplate({
                                headingText: item.title,
                                layout: 4,
                                style: 4,
                                headingExtraClasses: "opener__title"
                              })
                            : ""
                        }
                        ${
                          item.text
                            ? html`
                                <div class="${nsp("opener__text")}">
                                  ${item.text}
                                </div>
                              `
                            : ""
                        }

                        <div class="${nsp("opener__button-box")}">
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
                    </div>
                  </div>
                </li>
              `
            )
            .join("")}
        </ul>
      </div>
      <!-- Arrows -->
      ${controls}
    </div>
  `;
}
