/**
 * Renders a card component with image/icon, heading, text, and optional button or link.
 *
 * @param cardExtraClasses - Optional: Extra classes for the card container.
 * @param cardIsFullWidth - Optional: Card variant (full-width).
 * @param cardImagePosition - Optional: Card variant (image-left, image-right).
 * @param cardTag - Optional: HTML tag for the card (default: "div").
 * @param additionalCardAttributes - Optional: Additional HTML attributes as object.
 * @param cardImage - Optional: URL or path to the card image.
 * @param cardImageAlt - Optional: Alternative text for the image.
 * @param cardTopline - Optional: Topline text above the heading.
 * @param cardHeading - Optional: The card heading text.
 * @param cardHeadingLevel - Optional: The heading level.
 * @param cardHeadingStyle - Optional: The heading style.
 * @param cardText - Optional: The card body text.
 * @param cardCtaText - Optional: CTA text.
 * @param cardCtaIconBefore - Optional: Icon name before CTA text.
 * @param hasDivider - Optional: Renders a horizontal rule between card text and CTA.
 *
 * @example
 * import { cardTemplate } from "@molecules/card/card.template";
 *
 * // Card with image
 * cardTemplate({
 *   cardImagePosition: "image-left",
 *   cardImage: "demo-image-16_9.jpg",
 *   cardImageAlt: "Description of image",
 *   cardHeading: "Das ist ein H3",
 *   cardText: "Die Verwaltung von Normen muss keine Herausforderung sein.",
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import {
  imageTemplate,
  type ImageTemplateArgs
} from "@atoms/image/image.template";
import { headingTemplate } from "@atoms/heading/heading.template";
import { linkTemplate } from "@molecules/link/link.template";
import {
  dateBoxTemplate,
  type DateBoxTemplateArgs
} from "@molecules/date-box/date-box.template";
import { iconTemplate } from "@atoms/icon/icon.template";

// Typealias for card attributes
export type AdditionalCardAttributes = Record<string, string>;

// Custom types
export type CardImagePosition = "" | "image-left" | "image-right";
export type CardHeadingLevel = 2 | 3;
export type CardHeadingStyle = 2 | 3 | 4;
export type CardTag = "div" | "li";

// Arguments accepted by the template
export interface CardTemplateArgs {
  cardTag?: CardTag;
  cardExtraClasses?: string;
  cardImagePosition?: CardImagePosition;
  additionalCardAttributes?: AdditionalCardAttributes;
  cardImage?: ImageTemplateArgs;
  cardDate?: DateBoxTemplateArgs;
  cardIcon?: string;
  cardTopline?: string | boolean;
  cardHeading?: string | boolean;
  cardHeadingHref?: string;
  cardHeadingLevel?: CardHeadingLevel;
  cardHeadingStyle?: CardHeadingStyle;
  cardText?: string | boolean;
  cardCtaText?: string | boolean;
  cardCtaIconBefore?: string;
  hasDivider?: boolean;
}

// Return the HTML string for the card
export function cardTemplate({
  cardTag = "div",
  cardExtraClasses = "",
  cardImagePosition = "",
  additionalCardAttributes = {},
  cardImage,
  cardDate,
  cardIcon,
  cardTopline = "",
  cardHeading = "",
  cardHeadingHref,
  cardHeadingLevel = 3,
  cardHeadingStyle = 4,
  cardText = "",
  cardCtaText = "",
  cardCtaIconBefore = "chevron-right",
  hasDivider = false
}: CardTemplateArgs = {}): string {
  // Serialize the additionalCardAttributes object into a space-separated HTML attribute string
  const attrs = Object.entries(additionalCardAttributes)
    .map(([key, value]) => `${key}="${String(value)}"`)
    .join(" ");

  // Add image positions to cardExtraClasses
  if (cardImagePosition) {
    cardExtraClasses = `card--${cardImagePosition} ${cardExtraClasses}`.trim();
  }

  // Icon section
  const iconSection = cardIcon
    ? `<div class="${nsp("card__icon-box")}">
        ${iconTemplate({
          iconName: cardIcon,
          iconDecorative: true,
          iconExtraClasses: "card__icon"
        })}
      </div>`
    : "";

  // Image section
  const imageSection = cardImage
    ? `<div class="${nsp("card__image")}">
          ${imageTemplate(cardImage)}
        </div>`
    : "";

  // Date section
  const dateSection = cardDate
    ? `<div class="${nsp("card__image")}">
          ${dateBoxTemplate(cardDate)}
        </div>`
    : "";

  // Heading
  const cardHeadingText = cardHeading;

  // Topline/Heading section
  const headingSection =
    cardTopline || cardHeading
      ? `<div class="${nsp("card__heading-wrapper")}">
        ${cardTopline ? `<p class="${nsp("card__topline")}">${cardTopline}</p>` : ""}
        ${
          cardHeadingText
            ? headingTemplate({
                headingText: String(cardHeadingText),
                headingHref: String(cardHeadingHref),
                layout: cardHeadingLevel,
                style: cardHeadingStyle,
                headingExtraClasses: "card__heading"
              })
            : ""
        }
      </div>`
      : "";

  // Return the final card HTML
  return html`
    <${cardTag} class="${nsp("card", cardExtraClasses)}" ${attrs}>
      ${imageSection}

      ${iconSection}

      ${dateSection}
      ${
        cardTopline || cardHeading || cardText || cardCtaText
          ? `<div class="${nsp("card__content")}">
              ${
                cardTopline || cardHeading || cardText
                  ? `<div class="${nsp("card__text")}">
                      ${headingSection}
                      ${
                        cardText
                          ? `<div class="${nsp("card__body")}">
                              ${cardText}
                            </div>`
                          : ""
                      }
                    </div>`
                  : ""
              }

              ${hasDivider ? `<hr>` : ""}

              ${
                cardCtaText
                  ? `
                    ${linkTemplate({
                      linkText: String(cardCtaText),
                      linkUrl: "#",
                      linkIconBefore: cardCtaIconBefore || null,
                      linkExtraClasses: "card__link"
                    })}
                  `
                  : ""
              }
            </div>`
          : ""
      }
    </${cardTag}>
  `;
}
