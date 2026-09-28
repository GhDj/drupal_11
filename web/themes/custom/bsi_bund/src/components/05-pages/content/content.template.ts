/**
 * Renders the contentpage.
 */

// Globals
import { html } from "@globals/index";

// Components
import { textTemplate } from "@atoms/text/text.template";
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { introTemplate } from "@molecules/intro/intro.template";
import { contentTeaserTemplate } from "@organisms/content-teaser/content-teaser.template";
import { shortURLTemplate } from "@organisms/short-url/short-url.template";
import { audioTemplate } from "@organisms/audio/audio.template";
import { videoTemplate } from "@organisms/video/video.template";
import { blockTemplate } from "@organisms/block/block.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";
import { anchorsTemplate } from "@organisms/anchors/anchors.template";
import { openerTemplate } from "@organisms/opener/opener.template";
import { imageSliderTemplate } from "@organisms/image-slider/image-slider.template";
import { cardGroupTemplate } from "@organisms/card-group/card-group.template";
import { fourColumnsCardGroupItems } from "@organisms/card-group/card-group.constants";

// Return the HTML string for the Content page
export function contentTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: true,
      showAnchors: true,
      stage: openerTemplate({
        variant: "left-outside",
        showBadges: false,
        text: "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet, consetetur sadipscing elitr."
      }),
      breadcrumb: breadcrumbTemplate({}),
      anchors: anchorsTemplate({
        items: [
          { id: "lorem-1", text: "Lorem ipsum 1" },
          { id: "lorem-2", text: "Lorem ipsum 2" },
          { id: "lorem-3", text: "Lorem ipsum 3" },
          { id: "lorem-4", text: "Lorem ipsum 4" },
          { id: "lorem-5", text: "Lorem ipsum 5" },
          { id: "lorem-6", text: "Lorem ipsum 6" }
        ]
      }),
      main: html`
        <!-- Intro -->
        ${introTemplate()}

        <!-- Content Teaser -->
        <section id="anchor-lorem-1">${contentTeaserTemplate()}</section>

        <!-- Block with audio -->
        <section id="anchor-lorem-2">${audioTemplate()}</section>

        <!-- Block with video -->
        <section id="anchor-lorem-3">${videoTemplate()}</section>

        <!-- Short-URL -->
        ${shortURLTemplate()}

        <!-- Image Slider -->
        <section id="anchor-lorem-4">${imageSliderTemplate()}</section>

        <!-- Block -->
        <section id="anchor-lorem-5">
          ${blockTemplate({
            blockContent: textTemplate({
              bodytext: `
              <div class="bsi-bodytext">
                <p>
                  Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
                  eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
                  voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet
                  clita kasd gubergren, no&nbsp;
                </p>
                <figure class="bsi-image">
                    <picture>
                      <img loading="lazy" width="1196" height="673" src="../src/demo-content/images/demo-image-2_1.jpg" alt="Terrasse des WSOs">
                    </picture>
                    <figcaption>Bundesamt für Sicherheit in der Informationstechnik</figcaption>
                </figure>
                <p>
                  Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy
                  eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam
                  voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet
                  clita kasd gubergren, no&nbsp;
                </p>
              </div>
              `
            })
          })}
        </section>

        <!-- Menu Cards -->
        <section id="anchor-lorem-6">
          ${cardGroupTemplate({
            extraClasses: "card-group--four-columns",
            items: fourColumnsCardGroupItems
          })}
        </section>
      `
    })}
  `;
}
