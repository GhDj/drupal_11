/**
 * Renders a header with logo, navigation, search bar, and meta navigation.
 *
 * @param logoLink - Optional: URL for the logo link (typically homepage).
 * @param navLinks - Optional: Array of navigation link objects with text and optional URL. Links without URL render as collapsible menu toggles.
 *
 * @example
 * import { headerTemplate } from "@organisms/header/header.template";
 *
 * headerTemplate({
 *   navLinks: [
 *     { text: "Standards" },
 *     { text: "Topics" },
 *     { text: "About", url: "/about" }
 *   ],
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";
import { mainMenuTemplate } from "@molecules/mainmenu/mainmenu.template";
import { metaNavTemplate } from "@molecules/metanav/metanav.template";
import { searchmenuTemplate } from "@molecules/searchmenu/searchmenu.template";

// Return the HTML string for the header
export function headerTemplate({ logoLink = "#" }): string {
  const logo = `<a class="${nsp("link")}" href="${logoLink}" title="Zur Startseite"><img src="logo_bsi.svg" alt="DIN Logo" /></a>`;
  const logoMobile = `<a class="${nsp("link")}" href="${logoLink}" title="Zur Startseite"><img src="logo_bsi_mobile.svg" alt="DIN Logo" /></a>`;

  const search = searchmenuTemplate({
    additionalAttributes: {
      id: "header-search",
      popover: ""
    }
  });

  // Return the final header HTML
  return html`
    <header class="${nsp("header")}">
      <div class="${nsp("header__grid")}">
        <div class="${nsp("header__logo")}">
          <div class="${nsp("header__logo--desktop")}">${logo}</div>
          <div class="${nsp("header__logo--mobile")}">${logoMobile}</div>
        </div>

        <div class="${nsp("header__right")}">
          <div class="${nsp("header__meta-navigation")}">
            ${metaNavTemplate()}
          </div>

          <div class="${nsp("header__navigation")}">
            ${mainMenuTemplate()}
            ${buttonTemplate({
              buttonText: "",
              buttonAriaLabel: "Suche",
              buttonType: "button",
              buttonVariant: "tertiary",
              buttonIconBefore: "search",
              buttonExtraClasses: "header__search-button",
              additionalButtonAttributes: {
                popovertarget: "header-search"
              }
            })}
            ${search}
          </div>
        </div>
      </div>
    </header>
  `;
}
