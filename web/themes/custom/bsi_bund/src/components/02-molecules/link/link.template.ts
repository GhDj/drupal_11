/**
 * Renders a link with optional icons.
 *
 * @param linkUrl - The href value.
 * @param linkText - The visible link text.
 * @param linkAddOnText - Optional metadata text displayed after the link text.
 * @param linkTitle - Optional: title attribute.
 * @param linkExtraClasses - Optional: Extra classes for the <a> tag.
 * @param additionalLinkAttributes - Optional: Additional HTML attributes as object.
 * @param linkIconBefore - Optional: icon name to render before text.
 * @param linkIconAfter - Optional: icon name to render after text.
 *
 *
 * @example
 * import { linkTemplate } from "@molecules/link/link.template";
 *
 * linkTemplate({
 *   linkUrl: "#",
 *   linkText: "Home",
 *   linkTitle: "Go to the home page",
 *   linkExtraClasses: "some classes",
 *   linkIconBefore: "arrow-left",
 *   linkIconAfter: "arrow-right",
 *   additionalLinkAttributes: {
 *     target: "_blank",
 *     rel: "noopener"
 *   }
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";

// Typealias for link attributes
export type AdditionalLinkAttributes = Record<
  string,
  string | number | boolean
>;

// Arguments accepted by the template
export interface LinkTemplateArgs {
  linkUrl?: string;
  linkText?: string;
  linkAddOnText?: string;
  linkTitle?: string;
  linkAriaLabel?: string;
  linkBlockClass?: string;
  linkExtraClasses?: string;
  additionalLinkAttributes?: AdditionalLinkAttributes;
  linkIconBefore?: string | null;
  linkIconAfter?: string | null;
}

// Return the HTML string for a link
export function linkTemplate({
  linkUrl = "#",
  linkText = "",
  linkAddOnText = "",
  linkTitle,
  linkAriaLabel,
  linkExtraClasses = "",
  additionalLinkAttributes = {},
  linkIconBefore = null,
  linkIconAfter = null
}: LinkTemplateArgs = {}): string {
  const hasContent =
    Boolean(linkText) || Boolean(linkAddOnText) || Boolean(linkIconAfter);

  const attrs = Object.entries(additionalLinkAttributes)
    .map(([key, value]) => {
      if (typeof value === "boolean") {
        return value ? key : "";
      }
      return `${key}="${String(value)}"`;
    })
    .filter(Boolean)
    .join(" ");

  // Return the final HTML string
  return html`
    <a
      href="${linkUrl}"
      ${linkTitle ? `title="${linkTitle}"` : ""}
      ${linkAriaLabel ? `aria-label="${linkAriaLabel}"` : ""}
      class="${nsp("link", linkExtraClasses)}"
      ${attrs}
    >
      <!-- Icon before -->
      ${
        linkIconBefore
          ? iconTemplate({
              iconName: linkIconBefore,
              iconDecorative: true,
              iconExtraClasses: "link__icon"
            })
          : ""
      }

      <!-- Content -->
      ${
        hasContent
          ? html`
              <span class="${nsp("link__content")}">
                ${
                  linkText
                    ? html`
                        <span class="${nsp("link__text")}"> ${linkText} </span>
                      `
                    : ""
                }
                ${
                  linkAddOnText
                    ? html`
                        <span class="${nsp("link__add-on")}">
                          ${linkAddOnText}
                        </span>
                      `
                    : ""
                }
                ${
                  linkIconAfter
                    ? iconTemplate({
                        iconName: linkIconAfter,
                        iconDecorative: true,
                        iconExtraClasses: "link__icon"
                      })
                    : ""
                }
              </span>
            `
          : ""
      }
    </a>
  `;
}
