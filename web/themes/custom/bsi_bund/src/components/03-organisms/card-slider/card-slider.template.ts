import { nsp, html } from "@globals/index";
import {
  cardTemplate,
  type CardTemplateArgs
} from "@molecules/card/card.template";

import { buttonTemplate } from "@molecules/button/button.template";

import { items as sliderItems } from "./card-slider.constants";

export interface CardSliderTemplateArgs {
  items?: CardTemplateArgs[];
  extraClasses?: string;
}

export function cardSliderTemplate({
  items = sliderItems,
  extraClasses = ""
}: CardSliderTemplateArgs = {}): string {
  return html`
    <section class="${nsp("card-slider", "grid", extraClasses)}">
      <div class="splide" data-js-card-slider>
        <div class="splide__track ${nsp("card-slider__track")}">
          <ul class="splide__list ${nsp("card-slider__items")}">
            ${items
              .map(
                item => html`
                  <li class="splide__slide ${nsp("card-slider__item")}">
                    ${cardTemplate(item)}
                  </li>
                `
              )
              .join("")}
          </ul>
        </div>

        <ul
          class="splide__pagination ${nsp("image-slider__pagination")} ${nsp("grid")}"
        ></ul>

        <div class="${nsp("card-slider__controls")}">
          ${buttonTemplate({
            buttonIconBefore: "chevron-left",
            buttonAriaLabel: "Vorherige Slide",
            buttonVariant: "primary",
            buttonExtraClasses: "card-slider__prev"
          })}
          ${buttonTemplate({
            buttonIconBefore: "chevron-right",
            buttonAriaLabel: "Nächster Slide",
            buttonVariant: "primary",
            buttonExtraClasses: "card-slider__next"
          })}
        </div>
      </div>
    </section>
  `;
}
