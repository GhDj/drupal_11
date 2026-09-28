/**
 * Renders a list of links with optional title and link types.
 *
 * @param title - Optional heading displayed above the link list.
 * @param links - Optional array of links to render. Falls omitted, the default link list is used.
 *
 * @example
 * import { linkListTemplate } from "@molecules/linklist/linklist.template";
 *
 * linkListTemplate({
 *   title: "Downloads",
 *   links: [
 *     {
 *       linkUrl: "#",
 *       linkText: "PDF, 1 MB Download",
 *       linkAddOnText: "Lorem ipsum",
 *       linkType: "download",
 *     },
 *     {
 *       linkUrl: "#",
 *       linkText: "Contact",
 *       linkType: "internal",
 *     },
 *     {
 *       linkUrl: "https://example.com",
 *       linkText: "External website",
 *       linkType: "external",
 *     },
 *   ],
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { linkTemplate } from "@molecules/link/link.template";
import { headingTemplate } from "@atoms/heading/heading.template";

// Data
import { defaultLinkListItems } from "./linklist.constants";

// Arguments accepted by the template
export type LinkType = "download" | "internal" | "external";

export interface LinkItem {
  linkUrl: string;
  linkText: string;
  linkAddOnText?: string;
  linkType: LinkType;
}

export interface LinkListTemplateArgs {
  title?: string;
  links?: LinkItem[];
}

// Return the HTML string
export function linkListTemplate({
  title = "",
  links = defaultLinkListItems
}: LinkListTemplateArgs): string {
  // Return the final HTML string
  return html`
    <div class="${nsp("linklist")}">
      <!-- Heading -->
      ${headingTemplate({
        headingText: title,
        layout: 2,
        style: 5,
        headingExtraClasses: "linklist__heading"
      })}

      <!-- Linklist -->
      <ul class="${nsp("linklist__list")}">
        ${links
          .map(
            ({ linkUrl, linkText, linkAddOnText, linkType }) => html`
              <li class="${nsp("linklist__list-item")}">
                ${linkTemplate({
                  linkUrl,
                  linkText,
                  ...(linkAddOnText !== undefined && { linkAddOnText }),
                  linkIconBefore:
                    linkType === "download"
                      ? "download"
                      : linkType === "external"
                        ? "external-link"
                        : "chevron-right",
                  linkExtraClasses: `${linkType ? `linklist__link--${linkType}` : ""}`,
                  additionalLinkAttributes: {
                    target: "_blank",
                    rel: "noopener noreferrer"
                  }
                })}
              </li>
            `
          )
          .join("")}
      </ul>
    </div>
  `;
}
