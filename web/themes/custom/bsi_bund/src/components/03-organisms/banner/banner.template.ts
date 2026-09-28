/**
 * Renders the banner organism — a full-width promotional block with a
 * background image and a dark, overlaid content card containing a heading,
 * short text and a CTA link.
 *
 * @param imageSrc - Optional: Background image source.
 * @param title - Optional: Headline shown in the overlaid content card.
 * @param text - Optional: Body text shown under the headline.
 * @param linkText - Optional: CTA link label.
 * @param linkIconBefore - Optional: Icon rendered before the CTA label.
 * @param extraClasses - Optional: Extra classes appended to the root element.
 *
 * @example
 * import { bannerTemplate } from "@organisms/banner/banner.template";
 *
 * bannerTemplate({
 *   imageSrc: "demo-image-16_9.jpg",
 *   title: "Digitale Services & Meldungen",
 *   text: "Nutzen Sie das BSI-Portal für sichere Meldungen, Anträge und geschützte Fachverfahren.",
 *   linkText: "Zum BSI-Portal",
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { headingTemplate } from "@atoms/heading/heading.template";
import { imageTemplate } from "@atoms/image/image.template";
import { linkTemplate } from "@molecules/link/link.template";

// Arguments accepted by the template
export interface BannerTemplateArgs {
  imageSrc?: string;
  title?: string;
  text?: string;
  linkText?: string;
  linkIconBefore?: string;
  extraClasses?: string;
}

// Return the HTML string for the banner
export function bannerTemplate({
  imageSrc = "demo-image-16_9.jpg",
  title = "Digitale Services & Meldungen",
  text = "Nutzen Sie das BSI-Portal für sichere Meldungen, Anträge und geschützte Fachverfahren.",
  linkText = "Zum BSI-Portal",
  linkIconBefore = "external-link",
  extraClasses = ""
}: BannerTemplateArgs = {}): string {
  return html`
    <div class="${nsp("banner", extraClasses)}" data-theme="blue-900">
      <!-- Background image -->
      ${imageSrc ? imageTemplate({ imageSrc, imageExtraClasses: "banner__image" }) : ""}

      <!-- Content -->
      <div class="${nsp("banner__content")}" data-theme="blue-900">
        <!-- Title -->
        ${
          title
            ? headingTemplate({
                headingText: title,
                layout: 2,
                style: 2,
                headingExtraClasses: "banner__title"
              })
            : ""
        }

        <!-- Text -->
        ${text ? html`<p class="${nsp("banner__text")}">${text}</p>` : ""}

        <!-- Link -->
        ${
          linkText
            ? linkTemplate({
                linkText,
                linkUrl: "#",
                linkIconBefore,
                linkExtraClasses:
                  "link--button link--secondary-button banner__cta"
              })
            : ""
        }
      </div>
    </div>
  `;
}
