/**
 * Renders the contentpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { openerTemplate } from "@organisms/opener/opener.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";
import { searchfilterTemplate } from "@molecules/searchfilter/searchfilter.template";
import { Searchfilter } from "@molecules/searchfilter/searchfilter.stories";
import { searchResultTemplate } from "@molecules/search-result/search-result.template";

const result = {
  cardTopline: "Publikation  ·  10.06.2026",
  cardHeading:
    "Cybersicherheitsmonitor 2026: Ergebnispräsentation zur Befragung",
  cardText:
    "Fokusthema „Online-Betrug & Künstliche Intelligenz“ zum Cybersicherheitsmonitor 2026 der Polizeilichen Kriminalprävention der Länder und des Bundes (ProPK) und des Bundesamts für Sicherheit in der Informationstechnik (BSI)",
  cardCtaText: "PDF, 909KB herunterladen",
  cardCtaIconBefore: "download"
};

// Return the HTML string for the search page
export function searchTemplate(): string {
  return html`
    ${pageGridTemplate({
      extraClass: "page-grid--search",
      showStage: true,
      breadcrumb: breadcrumbTemplate({}),
      stage: openerTemplate({
        variant: "left-inside",
        showBadges: false,
        title: "Suche",
        text: "Die Ergebnisse können Sie mit den angegebenen Filtern eingrenzen. Für die Suche nach einer exakten Wortgruppe setzen Sie die Suchworte in Anführungszeichen."
      }),
      main: html` ${searchfilterTemplate(Searchfilter.args)}
      ${searchResultTemplate({
        results: [result, result, result, result, result]
      })}`
    })}
  `;
}
