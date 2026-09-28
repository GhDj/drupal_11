/**
 * Renders a link box containing one or multiple link lists with an optional icon.
 *
 * @param linkLists - Array of link lists to render inside the link box.
 *
 * @example
 * import { linkBoxTemplate } from "@organisms/linkbox/linkbox.template";
 *
 * linkBoxTemplate({
 *   linkLists: [
 *     {
 *       title: "Downloads",
 *       links: [
 *         {
 *           linkUrl: "/downloads/file.pdf",
 *           linkText: "PDF, 1 MB Download",
 *           linkAddOnText: "Lorem ipsum",
 *           linkType: "download",
 *         },
 *       ],
 *     },
 *     {
 *       title: "Links",
 *       links: [
 *         {
 *           linkUrl: "#",
 *           linkText: "Contact",
 *           linkType: "internal",
 *         },
 *       ],
 *     },
 *   ],
 * });
 */

// Globals
import { nsp, html } from "@globals/index.ts";

// Components
import { iconTemplate } from "@atoms/icon/icon.template.ts";
import {
  linkListTemplate,
  type LinkListTemplateArgs
} from "@molecules/linklist/linklist.template.ts";

// Data
import { defaultLinkBox } from "./linkbox.constants.ts";

// Arguments accepted by the template
export interface LinkBoxTemplateArgs {
  linkLists?: LinkListTemplateArgs[];
}

// Return the HTML string
export function linkBoxTemplate({
  linkLists = defaultLinkBox
}: LinkBoxTemplateArgs): string {
  // Return the final HTML string
  return html`
    <div class="${nsp("linkbox")}">
      <div class="${nsp("linkbox__icon")}">
        ${iconTemplate({
          iconName: "documents",
          iconDecorative: true
        })}
      </div>
      <div class="${nsp("linkbox__content")}">
        ${linkLists.map(linkList => linkListTemplate(linkList)).join("")}
      </div>
    </div>
  `;
}
