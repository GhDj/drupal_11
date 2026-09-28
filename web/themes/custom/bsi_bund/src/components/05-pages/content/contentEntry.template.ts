/**
 * Renders the contentpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { openerTemplate } from "@organisms/opener/opener.template";
import { linkBoxTemplate } from "@organisms/linkbox/linkbox.template";

// Return the HTML string for the Content page
export function contentEntryTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      stage: openerTemplate({
        variant: "slider",
        showBadges: false
      }),
      main: html`
        <!-- Link Box -->
        <section id="lorem-6">${linkBoxTemplate({})}</section>
      `
    })}
  `;
}
