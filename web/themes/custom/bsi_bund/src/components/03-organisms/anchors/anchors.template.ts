/**
 * Renders a sticky anchor navigation with jump links to sections on the current page.
 * Above breakpoint-lg (≥992px) the anchors are displayed as a horizontal list.
 * Below that breakpoint they collapse into a custom dropdown (see Molecules/Dropdown).
 *
 * @param items - Optional: Array of anchor items ({ id, text }) rendered as jump links.
 * @param title - Optional: Heading shown before the anchor list.
 * @param extraClasses - Optional: Extra classes for the root <nav> element.
 *
 * @example
 * import { anchorsTemplate } from "@organisms/anchors/anchors.template";
 *
 * anchorsTemplate({
 *   title: "Auf dieser Seite:",
 *   items: [
 *     { id: "intro", text: "Einleitung" },
 *     { id: "downloads", text: "Downloads" }
 *   ]
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { linkTemplate } from "@molecules/link/link.template";
import { dropdownTemplate } from "@molecules/dropdown/dropdown.template";

// Anchor item shape
export interface AnchorItem {
  id: string;
  text: string;
}

// Arguments accepted by the template
export interface AnchorsTemplateArgs {
  items?: AnchorItem[];
  title?: string;
  extraClasses?: string;
}

// Return the HTML string for the anchors organism
export function anchorsTemplate({
  items = [
    { id: "in-60-sekunden", text: "In 60 Sekunden" },
    { id: "einsatz-und-training", text: "Einsatz und Training" },
    { id: "fehlinterpretationen", text: "Fehlinterpretationen" },
    { id: "sicher-entwickeln", text: "Sicher entwickeln" },
    { id: "downloads", text: "Downloads" }
  ],
  title = "Auf dieser Seite:",
  extraClasses = ""
}: AnchorsTemplateArgs = {}): string {
  const firstItem = items[0];

  return html`
    <nav
      class="${nsp("anchors", extraClasses)}"
      aria-label="${title}"
      data-bsi-anchors
    >
      <div class="${nsp("anchors__inner")}">
        <p class="${nsp("anchors__title")}">${title}</p>

        <!-- Dropdown (mobile / laptop, < breakpoint-lg) -->
        <div class="${nsp("anchors__dropdown")}">
          ${dropdownTemplate({
            dropdownLabel: firstItem?.text ?? "",
            dropdownListId: "bsi-anchors-list-mobile",
            dropdownItems: items.map(item => ({
              text: item.text,
              href: `#anchor-${item.id}`
            }))
          })}
        </div>

        <!-- Horizontal list (≥ breakpoint-lg) -->
        <ul class="${nsp("anchors__list")}">
          ${items
            .map(
              item => html`
                <li class="${nsp("anchors__item")}">
                  ${linkTemplate({
                    linkUrl: `#anchor-${item.id}`,
                    linkText: item.text,
                    linkExtraClasses: "anchors__link"
                  })}
                </li>
              `
            )
            .join("")}
        </ul>
      </div>
    </nav>
  `;
}
