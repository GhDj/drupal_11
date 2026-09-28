/**
 * Renders the homepage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { heroSliderTemplate } from "@organisms/hero-slider/hero-slider.template";
import { blockTemplate } from "@organisms/block/block.template";
import { cardTemplate } from "@molecules/card/card.template";
import { cardGroupTemplate } from "@organisms/card-group/card-group.template";
import { tabsTemplate } from "@organisms/tabs/tabs.template";
import { bannerTemplate } from "@organisms/banner/banner.template";
import { quoteTemplate } from "@molecules/quote/quote.template";

// Constants
import { cardGroupItems, featuredCardArgs, quoteArgs } from "./home.constants";
import { cardSliderTemplate } from "@organisms/card-slider/card-slider.template";

// Return the HTML string for the Home page
export function homeTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      showBreadcrumb: false,
      stage: heroSliderTemplate({ showOverlay: true }),
      main: html`
        <!-- Block with Featured Card and Card Group -->
        ${blockTemplate({
          blockTitle: "Aktuelles zur IT-Sicherheitslage",
          metaLink: "Alle News anzeigen",
          blockContent: html`
            ${cardTemplate(featuredCardArgs)}
            ${cardGroupTemplate({
              items: cardGroupItems
            })}
          `
        })}

        <!-- Block with Horizontal Tabs -->
        ${blockTemplate({
          blockContent: tabsTemplate()
        })}

        <!-- Banner -->
        ${bannerTemplate()}

        <!-- Quote -->
        ${quoteTemplate(quoteArgs)}

        <!-- Block with Card Slider -->
        ${blockTemplate({
          blockTitle: "Unsere Themen",
          metaLink: "Alle Themen anzeigen",
          blockContent: cardSliderTemplate()
        })}
      `
    })}
  `;
}
