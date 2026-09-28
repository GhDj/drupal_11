/**
 * Renders a content teaser component with optional image, heading, text, and CTA.
 *
 * @param heading - Optional: The heading text.
 * @param image - Optional: Image configuration passed to the card template.
 * @param imagePosition - Optional: Position of the image within the card ("image-left", "image-right").
 * @param text - Optional: Body text content of the card.
 * @param ctaText - Optional: CTA link text.
 * @param ctaIconBefore - Optional: Icon name before CTA text.
 *
 * @example
 * import { contentTeaserTemplate } from "@organisms/content-teaser/content-teaser.template";
 *
 * contentTeaserTemplate({
 *   heading: "Normen im Alltag",
 *   image: { imageSrc: "demo-image-16_9.jpg", imageAlt: "Architecture detail" },
 *   imagePosition: "image-right",
 *   text: "Die DIN 18065 ist die zentrale Norm im Treppenbau.",
 *   ctaText: "Mehr erfahren"
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { headingTemplate } from "@atoms/heading/heading.template";
import { linkTemplate } from "@molecules/link/link.template";
import { type ImageTemplateArgs } from "@atoms/image/image.template";
import { cardTemplate } from "@molecules/card/card.template";

// Arguments accepted by the template
export interface ContentTeaserTemplateArgs {
  heading?: string;
  image?: ImageTemplateArgs;
  imagePosition?: "image-left" | "image-right";
  text?: string;
  ctaText?: string;
  ctaIconBefore?: string;
}

// Return the HTML string for the content teaser
export function contentTeaserTemplate({
  heading = "Noch offene Fragen? Das Service Center hilft Ihnen!",
  image = {
    imageSrc: "demo-image-16_9.jpg",
    imageExtraClasses: "content-teaser__image",
    imageCopyright: "Bundesamt für Sicherheit in der Informationstechnik"
  },
  imagePosition = "image-left",
  text = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. Lorem ipsum dolor sit amet.",
  ctaText = "Kontakt zum BSI aufnehmen",
  ctaIconBefore = "chevron-right"
}: ContentTeaserTemplateArgs = {}): string {
  const cardSection = cardTemplate({
    cardExtraClasses: "content-teaser__card",
    cardImage: image,
    cardImagePosition: imagePosition,
    cardText: text
  });

  // Return the final HTML string
  return html`
    <div class="${nsp("content-teaser")}">
      <!-- Teaser Heading -->
      ${
        heading
          ? headingTemplate({
              headingText: heading,
              layout: 2,
              style: 2,
              headingExtraClasses: "content-teaser__heading"
            })
          : ""
      }

      <div class="${nsp("content-teaser__content")}">
        <!-- Teaser Card -->
        ${cardSection}

        <!-- Teaser CTA -->
        <div class="more-link">
          ${
            ctaText
              ? linkTemplate({
                  linkText: ctaText,
                  linkIconBefore: ctaIconBefore,
                  linkExtraClasses:
                    "link--button link--secondary-button link--cta"
                })
              : ""
          }
        </div>
      </div>
    </div>
  `;
}
