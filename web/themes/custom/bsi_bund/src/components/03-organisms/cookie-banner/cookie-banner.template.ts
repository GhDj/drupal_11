import { html } from "@globals/index";

export function cookieBannerTemplate() {
  return html`
    <div
      id="klaro"
      style="--button-text-color: #fff; --dark1: #fafafa; --dark2: #777; --dark3: #555; --light1: #444; --light2: #666; --light3: #111; --green3: #f00; --notice-top: 20px; --notice-bottom: auto; --notice-left: 20px; --notice-right: auto; --notice-max-width: calc(100vw - 60px); --notice-position: fixed;"
    >
      <div lang="de" class="klaro  learn-more-as-button klaro-theme-bsi_bund">
        <div
          role="dialog"
          aria-describedby="id-cookie-notice"
          aria-labelledby="id-cookie-title"
          id="klaro-cookie-notice"
          tabindex="0"
          autofocus=""
          class="cookie-notice   "
        >
          <div class="cn-body">
            <h2 id="id-cookie-title">
              Verwendung von personenbezogenen Daten und Cookies
            </h2>
            <p id="id-cookie-notice">
              <span
                >Wir verwenden Cookies und verarbeiten personenbezogene Daten
                für die folgenden Zwecke:
                <strong>Funktional & Eingebettete externe Inhalte</strong
                >.</span
              >
            </p>
            <div class="cn-ok">
              <a
                href="#"
                class="cm-link cn-learn-more"
                title="Öffnet das Auswahlformular"
                role="button"
                aria-haspopup="dialog"
                >Datenschutzeinstellungen anpassen</a
              >
              <div class="cn-buttons">
                <button type="button" class="cm-btn cm-btn-danger cn-decline">
                  Alle optionalen Cookies ablehnen
                </button>
                <button type="button" class="cm-btn cm-btn-success">
                  Alle Cookies akzeptieren
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}
