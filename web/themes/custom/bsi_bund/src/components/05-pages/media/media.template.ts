/**
 * Renders the contentpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { audioTemplate } from "@organisms/audio/audio.template";
import { imageTemplate } from "@atoms/image/image.template";
import { blockTemplate } from "@organisms/block/block.template";

// Return the HTML string for the Content page
export function mediaTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      main: html`
        <!-- Block -->
        ${blockTemplate({
          blockContent: html`
            ${imageTemplate()}
            ${audioTemplate({
              heading: "",
              text: "",
              imageSrc: ""
            })}
            <p>
              Lorem ipsum dolor sit amet consectetur. Quis fermentum risus sed
              malesuada mauris tortor. Hendrerit sit eu aenean risus. Arcu
              praesent diam dolor magnis posuere. Euismod at tortor odio sed
              ultricies diam magna mi urna. Mattis etiam cursus arcu amet neque
              tempus sed laoreet.
            </p>
          `,
          contentIndented: true
        })}
      `
    })}
  `;
}
