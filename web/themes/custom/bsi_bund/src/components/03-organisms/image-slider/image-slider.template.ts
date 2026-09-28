import { imageTemplate } from "@atoms/image/image.template";
import { html, nsp } from "@globals/index";
import { buttonTemplate } from "@molecules/button/button.template";
import { type ImageTemplateArgs } from "@atoms/image/image.template";

// Arguments accepted by the template
export interface ImageSliderTemplateArgs {
  items?: ImageTemplateArgs[];
}

export function imageSliderTemplate({
  items = [
    { imageSrc: "demo-image-4_3.jpg" },
    { imageSrc: "demo-image-2_1.jpg" },
    { imageSrc: "demo-image-16_9.jpg" }
  ]
}: ImageSliderTemplateArgs = {}): string {
  return html`
    <div class="${nsp("image-slider grid")}">
      <div class="splide" data-bsi-image-slider>
        <div class="splide__track">
          <ul class="splide__list">
            ${items
              .map(
                item => html`
                  <li class="splide__slide ${nsp("image-slider__slide")}">
                    ${imageTemplate({
                      imageSrc: item.imageSrc ?? "",
                      imageLoading: "eager",
                      imageExtraClasses: "image-slider__image",
                      imageCaption: "Das ist die Caption",
                      imageCopyright: "Das ist das Copyright"
                    })}
                  </li>
                `
              )
              .join("")}
          </ul>
        </div>

        <!-- Arrows bleiben -->
        <div class="${nsp("image-slider__controls")}">
          ${buttonTemplate({
            buttonIconBefore: "chevron-left",
            buttonAriaLabel: "Vorherige Slide",
            buttonVariant: "primary",
            buttonExtraClasses: "image-slider__prev"
          })}
          ${buttonTemplate({
            buttonIconBefore: "chevron-right",
            buttonAriaLabel: "Nächster Slide",
            buttonVariant: "primary",
            buttonExtraClasses: "image-slider__next"
          })}
        </div>

        <ul
          class="splide__pagination ${nsp("image-slider__pagination")} ${nsp("grid")}"
        ></ul>
      </div>
    </div>
  `;
}
