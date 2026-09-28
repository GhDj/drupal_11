/**
 * Renders a generic custom dropdown with a button trigger and a popover list.
 * Toggle behaviour (open/close, Escape, click-outside) is handled by Dropdown.ts.
 *
 * @param dropdownLabel - Text shown in the trigger button (e.g. the currently selected item).
 * @param dropdownItems - List items to render inside the popover.
 * @param dropdownListId - id attribute on the <ul> (used for aria-controls).
 * @param extraClasses - Optional: Extra classes for the root element.
 *
 * @example
 * import { dropdownTemplate } from "@molecules/dropdown/dropdown.template";
 *
 * dropdownTemplate({
 *   dropdownLabel: "In 60 Sekunden",
 *   dropdownListId: "my-dropdown-list",
 *   dropdownItems: [
 *     { text: "In 60 Sekunden", href: "#in-60-sekunden" },
 *     { text: "Downloads", href: "#downloads" }
 *   ]
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";
import { linkTemplate } from "@molecules/link/link.template";

// Item shape for a single dropdown entry
export interface DropdownItem {
  text: string;
  href?: string;
  additionalAttributes?: Record<string, string | boolean>;
}

// Arguments accepted by the template
export interface DropdownTemplateArgs {
  dropdownLabel?: string;
  dropdownItems?: DropdownItem[];
  dropdownListId?: string;
  extraClasses?: string;
}

// Serialize an object of attributes into a string for HTML
function serializeAttrs(attrs: Record<string, string | boolean>): string {
  return Object.entries(attrs)
    .map(([key, value]) => {
      if (typeof value === "boolean") return value ? key : "";
      return `${key}="${value}"`;
    })
    .filter(Boolean)
    .join(" ");
}

// Return the HTML string for the dropdown molecule
export function dropdownTemplate({
  dropdownLabel = "Bitte auswählen",
  dropdownItems = [
    { text: "Option A", href: "#option-a" },
    { text: "Option B", href: "#option-b" },
    { text: "Option C", href: "#option-c" }
  ],
  dropdownListId = "bsi-dropdown-list",
  extraClasses = ""
}: DropdownTemplateArgs = {}): string {
  return html`
    <div class="${nsp("dropdown", extraClasses)}" data-js-dropdown>
      <!-- Trigger button -->
      ${buttonTemplate({
        buttonText: dropdownLabel,
        buttonIconAfter: "chevron-down",
        buttonExtraClasses: "dropdown__trigger",
        additionalButtonAttributes: {
          "data-js-dropdown-trigger": true,
          "aria-expanded": "false",
          "aria-controls": dropdownListId
        }
      })}

      <!-- Popover list -->
      <ul
        id="${dropdownListId}"
        class="${nsp("dropdown__list")}"
        data-js-dropdown-list
        hidden
      >
        <!-- Dropdown items -->
        ${dropdownItems
          .map(item => {
            // Additional attributes for the item
            const attrs = item.additionalAttributes
              ? serializeAttrs(item.additionalAttributes)
              : "";

            // If the item has an href, render it as a link; otherwise, render it as a span
            const inner = item.href
              ? linkTemplate({
                  linkExtraClasses: "dropdown__link",
                  linkText: item.text,
                  linkUrl: item.href,
                  additionalLinkAttributes: item.additionalAttributes ?? {}
                })
              : html`<span class="${nsp("dropdown__link")}" ${attrs}
                  >${item.text}</span
                >`;

            // Return the list item
            return html`<li class="${nsp("dropdown__item")}">${inner}</li>`;
          })
          .join("")}
      </ul>
    </div>
  `;
}
