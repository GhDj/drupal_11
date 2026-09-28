// Globals
import { nsp, html } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";
import { headingTemplate } from "@atoms/heading/heading.template";
import { linkTemplate } from "@molecules/link/link.template";

// Return the HTML string
export function shortURLTemplate(): string {
  // Return the final HTML string
  return html`
    <div class="${nsp("short-url")}">
      <!-- icon -->
      <div class="${nsp("short-url__icon")}">
        ${iconTemplate({
          iconName: "link-chain"
        })}
      </div>

      <div class="${nsp("short-url__content")}">
        <!-- heading -->
        ${headingTemplate({
          headingText: "Kurz-URL",
          layout: 2,
          style: 5,
          headingExtraClasses: "short-url__title"
        })}

        <hr />

        <!-- link -->
        ${linkTemplate({
          linkUrl: "https://www.bsi.bund.de/dok/bsig",
          linkText: "https://www.bsi.bund.de/dok/bsig",
          linkExtraClasses: "short-url__link"
        })}
      </div>
    </div>
  `;
}
