/**
 * Renders the contentpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { blockTemplate } from "@organisms/block/block.template";
import { textTemplate } from "@atoms/text/text.template";
import { bodytextExampleContent } from "@atoms/text/text.constants";
import { openerTemplate } from "@organisms/opener/opener.template";

// Return the HTML string for the Content page
export function contentArticleTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      stage: openerTemplate({
        variant: "left-inside",
        showBadges: false,
        text: "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr."
      }),
      main: html`
        ${blockTemplate({
          blockTitle: "Beispiel Artikel",
          richtextIndented: true,
          blockContent: textTemplate({
            bodytext: bodytextExampleContent
          }),
          contentIndented: true
        })}
      `
    })}
  `;
}
