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
      `
    })}
  `;
}
