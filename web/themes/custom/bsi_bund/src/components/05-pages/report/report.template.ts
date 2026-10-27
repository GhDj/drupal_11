/**
 * Renders the reportpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { factsBoxTemplate } from "@molecules/facts-box/facts-box.template";
import { shortURLTemplate } from "@organisms/short-url/short-url.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";
import { openerTemplate } from "@organisms/opener/opener.template";
import { cardTemplate } from "@molecules/card/card.template";
import { cardGroupTemplate } from "@organisms/card-group/card-group.template";

// Return the HTML string for the report page
export function reportTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      showSidemenu: true,
      stage: openerTemplate({
        showBadges: false,
        subtitle: "Jahresbericht 2025"
      }),
      showBreadcrumb: true,
      breadcrumb: breadcrumbTemplate({}),
      main: html`
        <!-- Keyfacts -->
        ${factsBoxTemplate()}

        <!-- Short-URL -->
        ${shortURLTemplate()}

        <!-- Menu Cards Report -->
        ${cardTemplate({
          hasDivider: false,
          cardImagePosition: "image-left",
          cardHeading: "Advanced‑Persistent‑Threat-Gruppen",
          cardText: "APT-Gruppen (advanced persistent threat) sind sehr gut ausgebildete, oft staatlich gesteuerte Angreifer, die zum Zweck der Sabotage und Spionage gezielt angreifen.",
          cardCtaText: "mehr erfahren",
          cardImage: {
            imageSrc: "demo-image-2_1.jpg"
          },
          cardExtraClasses: "card--large-padding",
          additionalCardAttributes: {
            "data-theme": "blue-100"
          }
        })}
        ${cardGroupTemplate({
          extraClasses: "card-group--two-columns card-group--large-padding",
          items: [
            {
              hasDivider: false,
              cardHeading: "Angreiferaktivtitäten",
              cardText: "Mit Portscans suchen Cyberangreifer nach Schwachstellen in Systemen, die aus dem Internet erreichbar sind. Mit der Ausnutzung von Schwachstellen haben Angreifer im aktuellen ...",
              cardCtaText: "Weiterführende Infos",
              cardChart: {
                id: "1234",
                type: "bar",
                data: "",
              },
              additionalCardAttributes: {
                "data-theme": "blue-100"
              }
            },
            {
              hasDivider: false,
              cardHeading: "Neue maliziöse Websiten",
              cardText: "Im aktuellen Berichtszeitraum wurden mit Badbox und Vo1d zwei neue, große IoT-Botnetze bekannt. Die Malware wurde teilweise bereits während des Produktionsprozesses der IoT-Geräte eingeschleust und kann vom Nutzenden nicht erkannt werden.",
              cardCtaText: "mehr erfahren",
              additionalCardAttributes: {
                "data-theme": "blue-900"
              }
            },
            {
              hasDivider: false,
              cardHeading: "Vom BSI gehaltene CC-Zertifikate nach Themenbereich ",
              cardText: "APT-Gruppen (advanced persistent threat) sind sehr gut ausgebildete, oft staatlich gesteuerte Angreifer, die zum Zweck der Sabotage und Spionage gezielt angreifen.",
              cardCtaText: "mehr erfahren",
              additionalCardAttributes: {
                "data-theme": "blue-100"
              }
            },
            {
              hasDivider: false,
              cardHeading: "Das ist die Überschrift",
              cardTopline: "75%",
              cardToplineSize: "large",
              cardText: "der Advanced‑Persistent‑Threat-Gruppen, die sich gegen mindestens eines der Top-10-Zielländer richteten, zielten auf die USA.",
              cardCtaText: "mehr erfahren",
              additionalCardAttributes: {
                "data-theme": "blue-800"
              }
            }
          ]
        })}
      `
    })}
  `;
}
