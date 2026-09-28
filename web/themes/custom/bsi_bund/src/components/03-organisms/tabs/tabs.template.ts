import { html } from "@globals/index";

export function tabsTemplate() {
  return html`
    <div class="bsi-grid">
      <div data-horizontal-tabs class="horizontal-tabs clearfix">
        <!-- TAB NAV -->
        <ul data-horizontal-tabs-list class="horizontal-tabs-list">
          <li
            class="horizontal-tab-button horizontal-tab-button-0 first selected"
          >
            <a href="#">
              <strong>BSI-IT-Sicherheitsmeldungen</strong>
              <span class="summary"></span>
              <span class="visually-hidden">(active tab)</span>
            </a>
          </li>

          <li class="horizontal-tab-button horizontal-tab-button-1">
            <a href="#">
              <strong>Federal CERT</strong>
              <span class="summary"></span>
            </a>
          </li>

          <li class="horizontal-tab-button horizontal-tab-button-2 last">
            <a href="#">
              <strong>Citizen CERT</strong>
              <span class="summary"></span>
            </a>
          </li>
        </ul>

        <!-- PANES -->
        <div data-horizontal-tabs-panes class="horizontal-tabs-panes">
          <!-- ACTIVE TAB -->
          <details class="horizontal-tabs-pane" open>
            <summary class="claro-details__summary">
              BSI-IT-Sicherheitsmeldungen
            </summary>

            <div class="details-wrapper">
              <!-- VIEW CONTENT -->
              <div class="view-content">
                <div class="views-row">
                  <article class="bsi-border-card bsi-border-card--high">
                    <span class="bsi-badge bsi-badge--date">
                      <time datetime="2026-05-12">12.05.2026</time>
                    </span>

                    <span class="bsi-badge bsi-badge--high"> Sehr hoch </span>

                    <h3
                      class="bsi-heading bsi-heading--4 bsi-border-card__title"
                    >
                      Beispiel-Medium BITS Sehr hoch
                    </h3>

                    <div class="field">
                      <a
                        href="#"
                        class="file file--application-pdf"
                        target="_blank"
                      >
                        Zur Sicherheitsmitteilung
                      </a>
                    </div>
                  </article>
                </div>
              </div>

              <!-- MORE LINK -->
              <div class="more-link">
                <a
                  class="bsi-link--button bsi-link--secondary-button bsi-link--cta bsi-link"
                  href="#"
                  >Alle Sicherheitsmeldungen</a
                >
              </div>
            </div>
          </details>
        </div>
      </div>
    </div>
  `;
}
