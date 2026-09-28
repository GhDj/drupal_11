// Globals
import { type AttributeType, attr, nsp, html } from "@globals/index";
import { searchbarTemplate } from "@molecules/searchbar/searchbar.template";
import {
  type LinkTemplateArgs,
  linkTemplate
} from "@molecules/link/link.template";

export interface SearchmenuTemplateArgs {
  quicklinksTitle?: string;
  quicklinks?: LinkTemplateArgs[];
  additionalAttributes?: AttributeType;
}

// Return the HTML string for the searchmenu
export function searchmenuTemplate({
  quicklinksTitle = "Häufig gesucht",
  quicklinks = [
    { linkText: "Regulierung nach NIS-2" },
    { linkText: "Gefährdungen durch Ransomware" },
    { linkText: "Zertifizierung Produkte & Personen (FAQ)" },
    { linkText: "Zertifizierung Common Criteria" },
    { linkText: "Elektronische Identitäten" }
  ],
  additionalAttributes
}: SearchmenuTemplateArgs): string {
  const quicklinksHTML = html`
    <nav aria-label="Quicklinks">
      <p class="${nsp("heading heading--6 searchmenu__title")}">
        ${quicklinksTitle}
      </p>
      <ul class="${nsp("searchmenu__list")}">
        ${quicklinks
          .map(link => {
            return `<li>
            ${linkTemplate({
              linkUrl: "#",
              linkText: link.linkText ?? "",
              linkExtraClasses: "link--button link--secondary-button",
              linkIconAfter: "chevron-right"
            })}
          </li>`;
          })
          .join("")}
      </ul>
    </nav>
  `;
  // Return the final searchmenu HTML
  return html`
    <div class="${nsp("searchmenu")}" ${attr(additionalAttributes)}>
      <div class="${nsp("grid")}">
        <form action="/formaction" method="post">${searchbarTemplate()}</form>
        ${quicklinksHTML}
      </div>
    </div>
  `;
}
