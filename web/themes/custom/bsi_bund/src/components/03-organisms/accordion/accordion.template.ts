import { html, nsp } from "@globals/index";
import { buttonTemplate } from "@molecules/button/button.template";
import { headingTemplate } from "@atoms/heading/heading.template";
import { defaultAccordionItems } from "./accordion.constants";

export interface AccordionItem {
  title: string;
  content: string;
}

export interface AccordionTemplateArgs {
  items?: AccordionItem[];
  extraClasses?: string;
}

export function accordionTemplate({
  items = defaultAccordionItems,
  extraClasses = ""
}: AccordionTemplateArgs = {}): string {
  return html`
    <div class="${nsp("accordion", "grid", extraClasses)}" data-bsi-accordion>
      ${items
        .map((item, index) => {
          const isOpen = index === 0;
          const panelId = `bsi-accordion-panel-${index}`;

          return html`
            <div class="${nsp("accordion__item")}">
              <div class="${nsp("accordion__title")}">
                ${headingTemplate({
                  headingText: item.title,
                  layout: 3,
                  style: 5,
                  headingExtraClasses: "accordion__heading"
                })}
                ${buttonTemplate({
                  buttonVariant: "tertiary",
                  buttonIconAfter: "chevron-down",
                  buttonExtraClasses: "accordion__trigger",
                  buttonAriaLabel: item.title,
                  additionalButtonAttributes: {
                    "aria-expanded": isOpen,
                    "aria-controls": panelId,
                    "data-bsi-accordion-trigger": true
                  }
                })}
              </div>
              <div
                id="${panelId}"
                class="${nsp("accordion__panel")}"
                role="region"
                ${isOpen ? "" : "hidden"}
              >
                ${item.content}
              </div>
            </div>
          `;
        })
        .join("")}
    </div>
  `;
}
