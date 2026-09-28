import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

const meta: Meta = {
  title: "Molecules/Cert Teaserlist",

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;

type Story = StoryObj;

export const Default: Story = {
  render: () => html`
    <div class="bsi-cert-teaserlist bsi-cert-teaserlist--citizen">
      <ul class="bsi-cert-teaserlist__grid">
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-10"
            data-ed11y-result="10"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="10"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=7fb38d39-74df-4343-88b0-7e10d2ab025e"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Microsoft Patchday Juli 2026 (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Microsoft Patchday Juli 2026</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-11"
            data-ed11y-result="11"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="9"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=70dc6d57-9faf-455d-9b5f-9264ee07b390"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Adobe Creative Cloud Applikationen: Mehrere Schwachstellen (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Adobe Creative Cloud Applikationen: Mehrere Schwachstellen</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-12"
            data-ed11y-result="12"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="8"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=f62c4744-3000-45b4-bee7-96f2e1485fa5"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Bitdefender Internet und Total Security: Schwachstelle ermöglicht Privilegieneskalation (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Bitdefender Internet und Total Security: Schwachstelle ermöglicht
              Privilegieneskalation</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-13"
            data-ed11y-result="13"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="7"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=b9721a6f-41ad-4351-a19a-6cca4f4ac42f"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Lenovo BIOS: Mehrere Schwachstellen (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Lenovo BIOS: Mehrere Schwachstellen</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-14"
            data-ed11y-result="14"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="6"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=f3a34e77-e56e-4d9e-931f-6d5d352f1d01"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Google Chrome: Mehrere Schwachstellen (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Google Chrome: Mehrere Schwachstellen</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-15"
            data-ed11y-result="15"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="5"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=368a10b7-3bf6-4277-9e3a-31864397282f"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Mozilla Firefox: Mehrere Schwachstellen ermöglichen nicht spezifizierten Angriff (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-15"
              >15.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Mozilla Firefox: Mehrere Schwachstellen ermöglichen nicht
              spezifizierten Angriff</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-16"
            data-ed11y-result="16"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="4"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=f2dc75ab-91bd-48df-9432-fad0384d84d6"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Mozilla Firefox: Schwachstelle ermöglicht Manipulation von Dateien (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-14"
              >14.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Mozilla Firefox: Schwachstelle ermöglicht Manipulation von
              Dateien</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-17"
            data-ed11y-result="17"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="3"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=bc176625-0e96-405e-9846-25b814591a81"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Microsoft Edge: Mehrere Schwachstellen (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-13"
              >13.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Microsoft Edge: Mehrere Schwachstellen</span
            >
          </a>
        </li>
        <li class="bsi-cert-teaser">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-18"
            data-ed11y-result="18"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="2"
          ></ed11y-element-result
          ><a
            class="bsi-link bsi-cert-teaser__link"
            href="https://wid.cert-bund.de/portal/wid/buergercert/details?uuid=e4083049-9c40-4986-88d7-27a028b09909"
            target="_blank"
            rel="noopener noreferrer"
            title="Sicherheitsmeldung in neuem Browser-Fenster öffnen: Samsung Android: Mehrere Schwachstellen (Quelle: Bürger-CERT)"
          >
            <time class="bsi-cert-teaser__date" datetime="2026-07-10"
              >10.07.2026</time
            >
            <span class="bsi-cert-teaser__title"
              >Samsung Android: Mehrere Schwachstellen</span
            >
          </a>
        </li>
      </ul>

      <div class="bsi-cert-teaserlist__cta">
        <div class="more-link">
          <ed11y-element-result
            class="ed11y-element"
            id="ed11y-result-19"
            data-ed11y-result="19"
            data-ed11y-open="false"
            style="outline: transparent solid 0px; top: initial; left: initial; transform: translate(-34px, 10px);"
            data-ed11y-jump-position="1"
          ></ed11y-element-result
          ><a
            class="bsi-link--button bsi-link--secondary-button bsi-link--cta bsi-link"
            href="https://www.buerger-cert.de"
            target="_blank"
            rel="noopener noreferrer"
            title="Opens in new browser window"
            >Bürger-CERT RSS-Feed</a
          >
        </div>
      </div>
    </div>
  `
};
