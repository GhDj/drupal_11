// Globals
import { nsp, html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";
import { linkTemplate } from "@molecules/link/link.template";

// Data
import { defaultMainNav } from "./mainmenu.constants";
import { cardTemplate } from "@molecules/card/card.template";

export interface NavLinkType {
  text: string;
  cardTitle?: string;
  cardDate?: string;
  cardLink?: string;
  cardLinkText?: string;
  cardAddOnText?: string;
  subnav?: NavLinkType[];
}

// Return the HTML string for the mainmenu
export function mainMenuTemplate(
  mainnav: NavLinkType[] = defaultMainNav
): string {
  // submenu panel for level 2 and level 3
  // ==========================================================================
  const submenuPanel = (id: string, level: "l2" | "l3", content: string) => `
    <div
      class="${nsp("mainmenu__panel", `mainmenu__panel--${level}`)}"
      id="${id}"
      data-js-mainmenu-panel="${level}"
      hidden
    >
      ${content}
    </div>
  `;

  // first-level links (no submenu)
  // ==========================================================================
  const level1Link = (link: NavLinkType) =>
    linkTemplate({
      linkUrl: "#",
      linkText: link.text,
      linkExtraClasses: "mainmenu__link"
    });

  // first-level toggle-button with level 2 submenu
  // ==========================================================================
  const level2Submenu = (link: NavLinkType, index: number) => {
    const panelId = `mainMenuL2-${index}`;

    return `
      ${buttonTemplate({
        buttonText: link.text,
        buttonExtraClasses: "mainmenu__collapse-toggle",
        additionalButtonAttributes: {
          "aria-controls": panelId,
          "aria-expanded": "false",
          "data-js-mainmenu-toggle": "l1"
        }
      })}

      ${submenuPanel(
        panelId,
        "l2",
        `
          <!-- submenu back-button -->
          ${buttonTemplate({
            buttonText: link.text,
            buttonExtraClasses: "button--inline-action mainmenu__back",
            buttonIconBefore: "chevron-left",
            additionalButtonAttributes: {
              "data-js-mainmenu-back": "",
              "aria-label": "Zurück zu Hauptmenü"
            }
          })}

          <!-- overview link: mobile only -->
          ${linkTemplate({
            linkUrl: "#",
            linkText: `Im Überblick: ${link.text}`,
            linkExtraClasses:
              "mainmenu__overview-link mainmenu__overview-link--mobile"
          })}

          <!-- overview column: desktop only -->
          <div class="${nsp("mainmenu__overview-column")}">
            ${linkTemplate({
              linkUrl: "#",
              linkText: `Im Überblick: ${link.text}`,
              linkExtraClasses: "mainmenu__overview-link"
            })}
          </div>

          <ul class="${nsp("mainmenu__submenu")}">
            ${link.subnav?.map((item, l2Index) => level2Link(item, index, l2Index)).join("") ?? ""}
          </ul>
         ${
           link.cardTitle
             ? `
            <div class="${nsp("mainmenu__card")}">
               ${cardTemplate({
                 additionalCardAttributes: { "data-theme": "blue-100" },
                 cardTopline: "2019-2022",
                 cardHeading: "Erste Umwelterklärung nach EMAS",
                 cardText:
                   "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
                 cardCtaText: "Umwelterklärung herunterladen",
                 hasDivider: true
               })}
            </div>
          `
             : ""
         }
        `
      )}
    `;
  };

  // second-level toggle-button with level 3 submenu
  // ==========================================================================
  const level3Submenu = (
    items: NavLinkType[],
    parentLinkText: string,
    l1Index: number,
    l2Index: number
  ) =>
    submenuPanel(
      `mainMenuL3-${l1Index}-${l2Index}`,
      "l3",
      `
        <ul class="${nsp("mainmenu__submenu")}">
          <!-- overview link: first item in L3 list -->
          <li class="${nsp("mainmenu__submenu-item mainmenu__submenu-item--overview")}">
            ${linkTemplate({
              linkUrl: "#",
              linkText: parentLinkText,
              linkExtraClasses: "mainmenu__link mainmenu__link--overview"
            })}
          </li>
          ${items
            .map(
              item =>
                `
                  <li class="${nsp("mainmenu__submenu-item")}">
                    ${linkTemplate({
                      linkUrl: "#",
                      linkText: item.text,
                      linkExtraClasses: "mainmenu__link"
                    })}
                  </li>
                `
            )
            .join("")}
        </ul>
      `
    );

  // second-level menu item (link or parent toggle with level 3 panel)
  // ==========================================================================
  const level2Link = (item: NavLinkType, l1Index: number, l2Index: number) => {
    const hasNoChildren = !item.subnav?.length;
    const panelId = `mainMenuL3-${l1Index}-${l2Index}`;

    if (hasNoChildren) {
      return `
        <li class="${nsp("mainmenu__submenu-item")}">
          ${linkTemplate({
            linkUrl: "#",
            linkText: item.text,
            linkExtraClasses: "mainmenu__link"
          })}
        </li>
      `;
    }

    return `
      <li class="${nsp("mainmenu__submenu-item")}">
        ${buttonTemplate({
          buttonText: item.text,
          buttonExtraClasses: "mainmenu__collapse-toggle",
          buttonIconAfter: "chevron-down",
          additionalButtonAttributes: {
            "aria-controls": panelId,
            "aria-expanded": "false",
            "data-js-mainmenu-toggle": "l2"
          }
        })}

        ${level3Submenu(item.subnav!, item.text, l1Index, l2Index)}
      </li>
    `;
  };

  // menu (all level)
  // ==========================================================================
  const menu = html`
    <nav class="${nsp("mainmenu")}" id="mainmenu" aria-label="Hauptmenü">
      <ul class="${nsp("mainmenu__list")}">
        ${mainnav
          ?.map(
            (link, index) =>
              `
                <li class="${nsp("mainmenu__list-item")}">
                  ${link.subnav ? level2Submenu(link, index) : level1Link(link)}
                </li>
              `
          )
          .join("")}
      </ul>
    </nav>
  `;

  // Return the final mainmenu HTML
  return html`
    <!-- menu button -->
    ${buttonTemplate({
      buttonVariant: "tertiary",
      buttonIconBefore: "menu-burger",
      buttonIconAfter: "cross",
      buttonAriaLabel: "Toggle mainmenu",
      buttonExtraClasses: "mainmenu__menu-button",
      additionalButtonAttributes: { popovertarget: "mainmenu" }
    })}

    <!-- menu -->
    ${menu}
  `;
}
