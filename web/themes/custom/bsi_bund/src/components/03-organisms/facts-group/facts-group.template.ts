import { nsp, html } from "@globals/index";
import {
  factsAndFiguresTemplate,
  type FactsAndFiguresTemplateArgs
} from "@molecules/facts/facts.template";

import { defaultFactsAndFiguresGroupItems } from "./facts-group.constants";

export interface FactsAndFiguresGroupTemplateArgs {
  factsAndFiguresGroupItems?: FactsAndFiguresTemplateArgs[];
  factsAndFiguresGroupExtraClasses?: string;
}

export function factsAndFiguresGroupTemplate({
  factsAndFiguresGroupItems = defaultFactsAndFiguresGroupItems,
  factsAndFiguresGroupExtraClasses = ""
}: FactsAndFiguresGroupTemplateArgs = {}): string {
  return html`
    <div class="${nsp("facts-group", factsAndFiguresGroupExtraClasses)}">
      <ul class="${nsp("facts-group__items")}">
        ${factsAndFiguresGroupItems
          .map(
            item => html`
              <li class="${nsp("facts-group__item")}">
                ${factsAndFiguresTemplate(item)}
              </li>
            `
          )
          .join("")}
      </ul>
    </div>
  `;
}
