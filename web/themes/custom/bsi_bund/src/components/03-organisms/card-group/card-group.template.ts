import { nsp, html } from "@globals/index";
import {
  cardTemplate,
  type CardTemplateArgs
} from "@molecules/card/card.template";

import { defaultCardGroupItems } from "./card-group.constants";
export interface CardGroupTemplateArgs {
  items?: CardTemplateArgs[];
  extraClasses?: string;
}

export function cardGroupTemplate({
  items = defaultCardGroupItems,
  extraClasses = ""
}: CardGroupTemplateArgs = {}): string {
  return html`
    <div class="${nsp("card-group", "grid", extraClasses)}">
      <ul class="${nsp("card-group__items")}">
        ${items
          .map(
            item => html`
              <li class="${nsp("card-group__item")}">${cardTemplate(item)}</li>
            `
          )
          .join("")}
      </ul>
    </div>
  `;
}
