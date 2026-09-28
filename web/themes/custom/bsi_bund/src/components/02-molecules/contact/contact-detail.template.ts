import { html, nsp } from "@globals/index";

import { headingTemplate } from "@atoms/heading/heading.template";
import { iconTemplate } from "@atoms/icon/icon.template";
import { linkTemplate } from "@molecules/link/link.template";
import { listTemplate } from "@molecules/list/list.template";

export interface ContactDetailLinkItem {
  label: string;
  text: string;
  url?: string;
}

export interface ContactDetailTemplateArgs {
  iconName?: string;
  title?: string;
  name?: string;
  personname?: string;
  shorttext?: string;
  linkItems?: ContactDetailLinkItem[];
  link?: string;
  linkUrl?: string;
}

export function contactDetailTemplate(
  args: ContactDetailTemplateArgs = {}
): string {
  const {
    iconName = "badge",
    title = "Pressekontakt",
    name = "Bundesamt für Sicherheit in der Informationstechnik",
    personname = "Max Mustermann",
    shorttext = "Pressestelle",
    linkItems = [],
    link = "Das BSI in Social Media",
    linkUrl = "intern"
  } = args;

  return html`
    <div class="${nsp("contact", "contact--detail")}">
      ${iconTemplate({
        iconName,
        iconExtraClasses: "contact__icon"
      })}
      <div class="${nsp("contact__content")}">
        ${
          title
            ? headingTemplate({
                headingText: title,
                layout: 2,
                style: 5,
                headingExtraClasses: "contact__title"
              })
            : ""
        }
        <div class="${nsp("contact__info")}">
          <span class="${nsp("contact__info-row")}">${name}</span>
          <span class="${nsp("contact__info-row", "contact__info-row--person")}"
            >${personname}</span
          >
          <span class="${nsp("contact__info-row")}">${shorttext}</span>
        </div>

        ${listTemplate({
          items: linkItems.map(
            item => html`
              <span class="${nsp("contact__label")}"> ${item.label}: </span>
              <span class="${nsp("contact__value")}">
                ${
                  item.url
                    ? linkTemplate({
                        linkUrl: item.url,
                        linkText: item.text,
                        linkTitle: item.text,
                        linkExtraClasses: "contact__link"
                      })
                    : item.text
                }
              </span>
            `
          ),
          variant: "unstyled",
          extraClasses: "contact__detail-list"
        })}
        ${
          link
            ? linkTemplate({
                linkUrl: linkUrl,
                linkText: link,
                linkTitle: link,
                linkIconBefore: "chevron-right",
                linkExtraClasses:
                  "contact__ctalink link--button link--tertiary-button"
              })
            : ""
        }
      </div>
    </div>
  `;
}
