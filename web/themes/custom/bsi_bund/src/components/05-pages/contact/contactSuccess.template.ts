/**
 * Renders the contact page
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { blockTemplate } from "@organisms/block/block.template";
import { cardGroupTemplate } from "@organisms/card-group/card-group.template";
import { type CardTemplateArgs } from "@molecules/card/card.template";
import { toolbarTemplate } from "@molecules/toolbar/toolbar.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";

const cardGroupItems: CardTemplateArgs[] = [
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    hasDivider: true,
    cardHeading: "Standorte",
    cardText:
      "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.",
    cardCtaText: "Zu den Standorten"
  },
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    hasDivider: true,
    cardHeading: "Services",
    cardText:
      "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.",
    cardCtaText: "Zu den Services"
  },
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    hasDivider: true,
    cardHeading: "Melden",
    cardText:
      "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.",
    cardCtaText: "Vorfall melden"
  }
];

// Return the HTML string for the contact page
export function contactSuccessTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: false,
      breadcrumb: breadcrumbTemplate({}),
      main: html`
        <!-- Toolbar -->
        ${toolbarTemplate({})}

        <!-- Success message -->
        ${blockTemplate({
          blockTitle: "Vielen Dank für Ihre Nachricht!",
          blockContent:
            "Wir haben Ihre Anfrage erhalten und werden uns in Kürze bei Ihnen melden. <br> Bitte prüfen Sie auch Ihren Spam-Ordner, falls Sie keine Antwort erhalten.",
          contentIndented: true,
          headingLayout: 1
        })}

        <!-- Block with Card Group -->
        ${blockTemplate({
          blockTitle: "Was Sie noch interessieren könnte",
          blockContent: cardGroupTemplate({
            items: cardGroupItems
          })
        })}
      `
    })}
  `;
}
