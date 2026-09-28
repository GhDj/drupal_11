import { headingTemplate } from "@atoms/heading/heading.template";
import { iconTemplate } from "@atoms/icon/icon.template";
import { nsp, html } from "@globals/index";

import { defaultFactsBoxItems } from "./facts-box.constants";

export type AdditionalFactsBoxAttributes = Record<string, string>;

export interface FactsBoxItem {
  label?: string;
  value?: string;
}

export interface FactsBoxTemplateArgs {
  title?: string;
  intro?: string;
  showIntro?: boolean;
  iconName?: string;
  items?: FactsBoxItem[];
  additionalAttributes?: AdditionalFactsBoxAttributes;
  extraClasses?: string;
}

function renderFactItem({ label, value }: FactsBoxItem): string {
  const hasLabel = Boolean(label);
  const hasValue = Boolean(value);

  if (!hasLabel && !hasValue) {
    return "";
  }

  return html`
    <div class="${nsp("facts-box__item")}">
      ${
        hasLabel
          ? html`
              <dt class="${nsp("facts-box__item-title")}">
                ${headingTemplate({
                  headingText: label ?? "",
                  layout: 3,
                  style: 6,
                  headingExtraClasses: "facts-box__item-heading"
                })}
              </dt>
            `
          : ""
      }
      ${
        hasValue
          ? html`<dd class="${nsp("facts-box__item-value")}">${value}</dd>`
          : ""
      }
    </div>
  `;
}

export function factsBoxTemplate({
  title = "Informationen zum Ereignis",
  intro = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  showIntro = true,
  iconName = "info-circle",
  items = defaultFactsBoxItems,
  additionalAttributes = {},
  extraClasses = ""
}: FactsBoxTemplateArgs = {}): string {
  const hasTitle = Boolean(title);
  const hasIcon = Boolean(iconName);
  const renderedItems = (items ?? [])
    .map(renderFactItem)
    .filter(Boolean)
    .join("");
  const hasItems = Boolean(renderedItems);

  const classes = [!hasIcon ? "facts-box--no-icon" : "", extraClasses]
    .filter(Boolean)
    .join(" ");

  const attrs = Object.entries(additionalAttributes)
    .map(([key, value]) => `${key}="${String(value)}"`)
    .join(" ");

  return html`
    <section class="${nsp("facts-box", classes)}" ${attrs}>
      ${
        hasIcon
          ? html`
              <div class="${nsp("facts-box__icon-wrap")}">
                ${iconTemplate({
                  iconName,
                  iconDecorative: true,
                  iconExtraClasses: "facts-box__icon"
                })}
              </div>
            `
          : ""
      }

      <div class="${nsp("facts-box__content")}">
        ${
          hasTitle
            ? headingTemplate({
                headingText: title,
                layout: 2,
                style: 5,
                headingExtraClasses: "facts-box__title"
              })
            : ""
        }
        ${showIntro && intro ? html`<div class="${nsp("facts-box__intro-text")}">${intro}</div>` : ""}
        ${
          hasItems
            ? html`<dl class="${nsp("facts-box__list")}">${renderedItems}</dl>`
            : ""
        }
      </div>
    </section>
  `;
}
