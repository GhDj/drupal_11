// Globals
import { nsp, html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";
import { linkTemplate } from "@molecules/link/link.template";

// Data
import { defaultSideNav, defaultSideNavTel } from "./sidemenu.constants";

// Arguments accepted by the template
export interface SideNavLinkType {
  text: string;
  tel?: string;
  url?: string;
  subnav?: SideNavLinkType[];
}

// Navigation link
const navigationLink = (item: SideNavLinkType): string =>
  linkTemplate({
    linkUrl: item.url ?? "#",
    linkText: item.text,
    linkExtraClasses: "sidemenu__link"
  });

// Section heading
const sectionHeading = (text: string): string => `
  <h2 class="${nsp("sidemenu__heading")}">
    ${text}
  </h2>
`;

// Nested navigation
const nestedNavigation = (items: SideNavLinkType[], parentId: string): string =>
  items
    .map((item, index) => {
      const itemId = `${parentId}-${index}`;
      const panelId = `sideMenuPanel-${itemId}`;
      const hasChildren = Boolean(item.subnav?.length);

      // Link without children
      if (!hasChildren) {
        return `
          <li class="${nsp("sidemenu__item")}">
            ${navigationLink(item)}
          </li>
        `;
      }

      // Item with children
      return `
        <li class="${nsp("sidemenu__item sidemenu__item--parent")}">

          ${navigationLink(item)}

          ${buttonTemplate({
            buttonVariant: "tertiary",
            buttonIconAfter: "chevron-down",
            buttonAriaLabel: `${item.text} Untermenü öffnen`,
            buttonExtraClasses: "sidemenu__toggle",
            additionalButtonAttributes: {
              type: "button",
              "aria-controls": panelId,
              "aria-expanded": "false",
              "data-js-sidemenu-toggle": ""
            }
          })}

          <div
            id="${panelId}"
            class="${nsp("sidemenu__submenu")}"
            data-js-sidemenu-panel="submenu"
            hidden
          >
            <ul class="${nsp("sidemenu__list")}">
              ${nestedNavigation(item.subnav!, itemId)}
            </ul>
          </div>

        </li>
      `;
    })
    .join("");

// Navigation group
const navigationGroup = (group: SideNavLinkType, index: number): string => `
  <section class="${nsp("sidemenu__group")}">

    ${sectionHeading(group.text)}

    ${
      group.subnav?.length
        ? `
          <ul class="${nsp("sidemenu__list")}">
            ${nestedNavigation(group.subnav, `group-${index}`)}
          </ul>
        `
        : navigationLink(group)
    }

  </section>
`;

// Navigation panel
const navigationPanel = (sidenav: SideNavLinkType[]): string => `
  <aside
    id="sidemenu"
    class="${nsp("sidemenu__panel")}"
    aria-label="Dokumentnavigation"
    hidden
    data-js-sidemenu-panel="root"
  >

    <header class="${nsp("sidemenu__header")}">

      <div class="${nsp("sidemenu__header-content")}">
          ${linkTemplate({
            linkText: "Schließen",
            linkExtraClasses: "sidemenu__title",
            additionalLinkAttributes: {
              "data-js-sidemenu-close": ""
            }
          })}
          ${
            defaultSideNavTel
              ? `
            ${linkTemplate({
              linkText: defaultSideNavTel,
              linkUrl: `tel:${defaultSideNavTel.replace(/\s/g, "")}`,
              linkExtraClasses: "sidemenu__tel"
            })}
            `
              : ""
          }
        </div>

      ${buttonTemplate({
        buttonVariant: "tertiary",
        buttonIconBefore: "cross",
        buttonAriaLabel: "Navigation schließen",
        buttonExtraClasses: "sidemenu__close",
        additionalButtonAttributes: {
          type: "button",
          "data-js-sidemenu-close": ""
        }
      })}

    </header>

    <nav
      class="${nsp("sidemenu__navigation")}"
      aria-label="Dokumentnavigation"
    >
      ${sidenav.map((group, index) => navigationGroup(group, index)).join("")}
    </nav>

  </aside>
`;

// Navigation trigger
const navigationTrigger = (): string =>
  buttonTemplate({
    buttonVariant: "tertiary",
    buttonIconBefore: "sidebar",
    buttonAriaLabel: "Navigation öffnen",
    buttonExtraClasses: "sidemenu__trigger",
    additionalButtonAttributes: {
      type: "button",
      "aria-controls": "sidemenu",
      "aria-expanded": "false",
      "data-js-sidemenu-trigger": ""
    }
  });

// Sidemenu
export function sidemenuTemplate(
  sidenav: SideNavLinkType[] = defaultSideNav
): string {
  return html`
    <div class="${nsp("sidemenu")}" data-js-sidemenu>
      ${navigationTrigger()} ${navigationPanel(sidenav)}
    </div>
  `;
}
