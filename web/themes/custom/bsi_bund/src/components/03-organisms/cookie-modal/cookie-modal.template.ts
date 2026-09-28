import { html } from "@globals/index";

export function cookieModalTemplate() {
  return html`
    <div
      id="klaro"
      style="--button-text-color: #fff; --dark1: #fafafa; --dark2: #777; --dark3: #555; --light1: #444; --light2: #666; --light3: #111; --green3: #f00; --notice-top: 20px; --notice-bottom: auto; --notice-left: 20px; --notice-right: auto; --notice-max-width: calc(100vw - 60px); --notice-position: fixed;"
    >
      <div lang="de" class="klaro  learn-more-as-button klaro-theme-bsi_bund">
        <div id="cookieScreen" class="cookie-modal">
          <div class="cm-bg"></div>
          <div
            class="cm-modal cm-klaro"
            role="dialog"
            aria-modal="true"
            aria-labelledby="cm-modal-title"
          >
            <div class="cm-header">
              <button
                title="Schließen"
                aria-label="Schließen"
                type="button"
                tabindex="0"
                class="hide"
              >
                <svg
                  role="img"
                  aria-label="Schließen"
                  width="12"
                  height="12"
                  version="1.1"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <title>Schließen</title>
                  <line x1="1" y1="11" x2="11" y2="1" stroke-width="1"></line>
                  <line x1="1" y1="1" x2="11" y2="11" stroke-width="1"></line>
                </svg>
              </button>
              <h1 class="title" id="cm-modal-title">
                <span>Verwendung von personenbezogenen Daten und Cookies</span>
              </h1>
              <p>
                <span
                  >Bitte die Dienste und Anwendungen von Drittanbietern
                  auswählen, die Sie nutzen möchten. Für weitere Informationen
                  bitte unsere
                  <a href="/#TBD" target="_blank" rel="noopener"
                    >Datenschutzhinweise</a
                  >
                  lesen.
                </span>
              </p>
            </div>
            <div class="cm-body">
              <ul class="cm-services">
                <li class="cm-service">
                  <div>
                    <input
                      id="service-item-cms"
                      aria-labelledby="service-item-cms-title"
                      aria-describedby="service-item-cms-description"
                      disabled=""
                      tabindex="0"
                      type="checkbox"
                      class="cm-list-input required"
                    />
                    <label
                      for="service-item-cms"
                      class="cm-list-label"
                      onkeydown="Drupal.behaviors.klaro.KlaroToggleService(event)"
                      aria-labelledby="service-item-cms-title"
                      aria-describedby="service-item-cms-description"
                    >
                      <span id="service-item-cms-title" class="cm-list-title"
                        >Funktional</span
                      >
                      <span
                        title="Dieser Dienst ist immer erforderlich."
                        class="cm-required"
                        >(immer erforderlich)</span
                      >
                      <span class="cm-switch">
                        <div class="slider round active"></div>
                      </span>
                    </label>

                    <div id="service-item-cms-description">
                      <p class="cm-list-description">
                        <span
                          >Speichern von Daten (z.B. Cookie für die
                          Benutzersitzung) in Ihrem Browser (erforderlich für
                          die Nutzung dieser Website).</span
                        >
                      </p>
                      <p class="purposes">Zweck: Funktional</p>
                    </div>
                  </div>
                </li>
                <li class="cm-service">
                  <div>
                    <input
                      id="service-item-klaro"
                      aria-labelledby="service-item-klaro-title"
                      aria-describedby="service-item-klaro-description"
                      disabled=""
                      tabindex="0"
                      type="checkbox"
                      class="cm-list-input required"
                    />
                    <label
                      for="service-item-klaro"
                      class="cm-list-label"
                      onkeydown="Drupal.behaviors.klaro.KlaroToggleService(event)"
                      aria-labelledby="service-item-klaro-title"
                      aria-describedby="service-item-klaro-description"
                    >
                      <span id="service-item-klaro-title" class="cm-list-title"
                        >Consent Manager</span
                      >
                      <span
                        title="Dieser Dienst ist immer erforderlich."
                        class="cm-required"
                        >(immer erforderlich)</span
                      >
                      <span class="cm-switch">
                        <div class="slider round active"></div>
                      </span>
                    </label>
                    <div id="service-item-klaro-description">
                      <p class="cm-list-description">
                        <span
                          >Klaro! Cookie &amp; Consent speichert Ihren
                          Einwilligungsstatus im Browser.</span
                        >
                      </p>
                      <p class="purposes">Zweck: Funktional</p>
                    </div>
                  </div>
                </li>
                <li class="cm-service">
                  <div>
                    <input
                      id="service-item-mastodon_module"
                      aria-labelledby="service-item-mastodon_module-title"
                      aria-describedby="service-item-mastodon_module-description"
                      tabindex="0"
                      type="checkbox"
                      class="cm-list-input"
                    />
                    <label
                      for="service-item-mastodon_module"
                      class="cm-list-label"
                      onkeydown="Drupal.behaviors.klaro.KlaroToggleService(event)"
                      aria-labelledby="service-item-mastodon_module-title"
                      aria-describedby="service-item-mastodon_module-description"
                    >
                      <span
                        id="service-item-mastodon_module-title"
                        class="cm-list-title"
                        >Mastodon</span
                      >
                      <span class="cm-switch">
                        <div class="slider round active"></div>
                      </span>
                    </label>
                    <div id="service-item-mastodon_module-description">
                      <p class="cm-list-description">
                        <span
                          >Das Mastodon-Modul für Drupal stellt eine Verbindung
                          zu einem bestimmten Mastodon-Server her und speichert
                          Beiträge im Browser des Benutzers. Dabei werden
                          personenbezogene Daten wie IP-Adressen
                          verarbeitet.</span
                        >
                      </p>
                      <p class="purposes">
                        Zweck: Eingebettete externe Inhalte
                      </p>
                    </div>
                  </div>
                </li>
                <li class="cm-service">
                  <div>
                    <input
                      id="service-item-multimedia"
                      aria-labelledby="service-item-multimedia-title"
                      aria-describedby="service-item-multimedia-description"
                      tabindex="0"
                      type="checkbox"
                      class="cm-list-input"
                    />
                    <label
                      for="service-item-multimedia"
                      class="cm-list-label"
                      onkeydown="Drupal.behaviors.klaro.KlaroToggleService(event)"
                      aria-labelledby="service-item-multimedia-title"
                      aria-describedby="service-item-multimedia-description"
                    >
                      <span
                        id="service-item-multimedia-title"
                        class="cm-list-title"
                        >Multimedia</span
                      >
                      <span class="cm-switch">
                        <div class="slider round active"></div>
                      </span>
                    </label>
                    <div id="service-item-multimedia-description">
                      <p class="cm-list-description">
                        <span
                          >Multimedia-Inhalte, welche vom BSI gehostet
                          werden.</span
                        >
                      </p>
                      <p class="purposes">
                        Zweck: Eingebettete externe Inhalte
                      </p>
                    </div>
                  </div>
                </li>
                <li class="cm-service cm-toggle-all">
                  <div>
                    <input
                      id="service-item-disableAll"
                      aria-labelledby="service-item-disableAll-title"
                      aria-describedby="service-item-disableAll-description"
                      tabindex="0"
                      type="checkbox"
                      class="cm-list-input half-checked only-required"
                    />
                    <label
                      for="service-item-disableAll"
                      class="cm-list-label"
                      onkeydown="Drupal.behaviors.klaro.KlaroToggleService(event)"
                      aria-labelledby="service-item-disableAll-title"
                      aria-describedby="service-item-disableAll-description"
                    >
                      <span
                        id="service-item-disableAll-title"
                        class="cm-list-title"
                        >Alle Dienste umschalten</span
                      >
                      <span class="cm-switch">
                        <div class="slider round active"></div>
                      </span>
                    </label>
                    <div id="service-item-disableAll-description">
                      <p class="cm-list-description">
                        <span
                          >Diesen Schalter nutzen, um alle Dienste zu
                          aktivieren/deaktivieren.</span
                        >
                      </p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="cm-footer">
              <div class="cm-footer-buttons">
                <button
                  type="button"
                  class="cm-btn cm-btn-decline cm-btn-danger cn-decline"
                >
                  Alle optionalen Cookies ablehnen
                </button>
                <button
                  type="button"
                  class="cm-btn cm-btn-success cm-btn-info cm-btn-accept"
                >
                  Speichern
                </button>
                <button
                  type="button"
                  class="cm-btn cm-btn-success cm-btn-accept-all"
                >
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
