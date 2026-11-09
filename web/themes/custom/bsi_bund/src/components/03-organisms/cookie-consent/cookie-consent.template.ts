import { html } from "@globals/index";

export function cookieConsentTemplate() {
  return html`
    <div data-type="placeholder" data-name="multimedia">
      <div
        lang="de"
        class="klaro learn-more-as-button klaro-theme-bsi_bund cm-as-context-notice"
      >
        <div class="context-notice">
          <p>Von Multimedia bereitgestellten externen Inhalt laden?</p>

          <p class="cm-buttons">
            <button type="button" class="cm-btn cm-btn-success">
              Ja (einmalig)
            </button>

            <button type="button" class="cm-btn cm-btn-success-var">
              Immer
            </button>
          </p>

          <p class="cm-dialog-link">
            <a
              href="#"
              title="Einwilligungsdialog öffnen"
              rel="open-consent-manager"
            >
              Datenschutzeinstellungen verwalten
            </a>
          </p>
        </div>
      </div>
    </div>
  `;
}
