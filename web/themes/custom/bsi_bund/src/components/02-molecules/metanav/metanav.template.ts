// Globals
import { nsp, html } from "@globals/index";

// Components
import { linkTemplate } from "@molecules/link/link.template";

// Data
import { defaultLinks } from "./metanav.constants";

export interface MetaNavLink {
  url: string;
  text: string;
  extraClasses: string;
  iconAfter: string;
}

export interface MetaNavTemplateArgs {
  links?: MetaNavLink[];
  wrapper?: boolean;
}

export function metaNavTemplate({
  links = defaultLinks,
  wrapper = true
}: MetaNavTemplateArgs = {}): string {
  const list = html`
    <ul class="${nsp("metanav__list")}">
      ${links
        .map(
          link =>
            `<li class="${nsp("metanav__item")}">${linkTemplate({
              linkUrl: "/",
              linkText: link.text,
              linkExtraClasses: `metanav__link ${link.extraClasses}`,
              linkIconAfter: link.iconAfter
            })}</li>`
        )
        .join("")}
    </ul>
  `;

  // the metanav is used without wrapper in the mobile mainmenu
  if (!wrapper) return list;

  return html`
    <nav class="${nsp("metanav")}" aria-label="Meta Navigation">${list}</nav>
  `;
}
