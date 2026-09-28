/**
 * Renders the jobspage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";
import { openerTemplate } from "@organisms/opener/opener.template";

// Return the HTML string for the jobs page
export function jobsTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      stage: openerTemplate({
        variant: "left-inside",
        showBadges: false,
        showFacts: true
      }),
      showBreadcrumb: true,
      breadcrumb: breadcrumbTemplate({})
    })}
  `;
}
