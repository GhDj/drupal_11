/**
 * Renders the opener organism — a hero section with a background image, an
 * overlaid white content card (title + short text), and an badges row of
 * label + badge pairs below the hero.
 *
 * @param pageTitle - Optional: H1 heading displayed above the slider (slider variant only).
 * @param title - Optional: Headline shown in the content card.
 * @param subtitle - Optional: Subtitle shown in the content card.
 * @param text - Optional: Body line shown under the headline.
 * @param typ - Optional: Value for the "Typ" badge in the badges.
 * @param datum - Optional: Value for the "Datum" badge in the badges.
 * @param imageSrc - Optional: Background image source.
 * @param imageAlt - Optional: Alternative text for the background image.
 * @param variant - Optional: Layout variant; adds `opener--<variant>` to the container.
 * @param items - Optional: Collection of slides rendered by the slider variant.
 * @param showBadges - Optional: Whether to render the badges row. Defaults to `true`.
 * @param extraClasses - Optional: Extra classes appended to the root element.
 *
 * @example
 * import { openerTemplate } from "@organisms/opener/opener.template";
 *
 * openerTemplate({ title: "BSI startet NIS-2-FAQ …", typ: "Meldung" });
 */

// Globals
import { html, nsp } from "@globals/index";

// Components
import { headingTemplate } from "@atoms/heading/heading.template";
import { imageTemplate } from "@atoms/image/image.template";
import { badgeTemplate } from "@molecules/badge/badge.template";
import {
  type OpenerSliderItem,
  openerSliderTemplate
} from "./opener-slider.template";
import { jobFactsTemplate } from "@molecules/job-facts/job-facts.template.ts";

// Constants
import {
  type OpenerVariant,
  openerSliderDefaultItems
} from "./opener.constants";

export interface OpenerTemplateArgs {
  pageTitle?: string;
  title?: string;
  subtitle?: string;
  text?: string;
  typ?: string;
  datum?: string;
  imageSrc?: string;
  imageAlt?: string;
  variant?: OpenerVariant;
  items?: OpenerSliderItem[];
  showBadges?: boolean;
  showFacts?: boolean;
  extraClasses?: string;
}

export function openerTemplate({
  pageTitle = "Technologie & Forschung",
  title = "Cybersicherheit",
  subtitle = "",
  text = "Schutz, Prävention und sichere digitale Nutzung <br> – alles rund um Cybersicherheit auf einen Blick.",
  typ = "Meldung",
  datum = "13.03.2026",
  imageSrc = "demo-image-3_1.jpg",
  imageAlt = "",
  variant = "centered",
  items = openerSliderDefaultItems,
  showBadges = true,
  showFacts = false,
  extraClasses = ""
}: OpenerTemplateArgs = {}): string {
  const typBadge = typ
    ? badgeTemplate({
        badgeTitle: typ,
        badgeTheme: "date",
        badgeExtraClasses: "opener__badge"
      })
    : "";

  const datumBadge = datum
    ? badgeTemplate({
        badgeTitle: datum,
        badgeTheme: "date"
      })
    : "";

  const badges = showBadges && Boolean(typBadge || datumBadge);

  return html`
    <div class="${nsp("opener", `opener--${variant}`, extraClasses)}">
      ${
        variant === "slider"
          ? openerSliderTemplate({ items, pageTitle })
          : html`
              <div class="${nsp("opener__content-wrapper")}">
                ${imageTemplate({
                  imageSrc,
                  imageAlt,
                  imageExtraClasses: "opener__image"
                })}

                <div class="${nsp("opener__content-grid")}">
                  <div class="${nsp("opener__content")}">
                    ${
                      title
                        ? headingTemplate({
                            headingText: title,
                            layout: 2,
                            style: 2,
                            headingExtraClasses: "opener__title"
                          })
                        : ""
                    }
                    ${
                      subtitle
                        ? html`
                            <p class="${nsp("opener__subtitle")}">
                              ${subtitle}
                            </p>
                          `
                        : ""
                    }
                    ${
                      text
                        ? html` <p class="${nsp("opener__text")}">${text}</p> `
                        : ""
                    }
                  </div>
                </div>
              </div>
            `
      }
      ${
        badges
          ? html`
              <ul class="${nsp("opener__badges")}">
                <li class="${nsp("opener__badge-item")}">
                  <span class="${nsp("opener__badge-prefix")}">Typ</span>
                  ${typBadge}
                </li>

                <li class="${nsp("opener__badge-item")}">
                  <span class="${nsp("opener__badge-prefix")}">Datum</span>
                  ${datumBadge}
                </li>
              </ul>
            `
          : ""
      }
      ${showFacts ? html` ${jobFactsTemplate()} ` : ""}
    </div>
  `;
}
