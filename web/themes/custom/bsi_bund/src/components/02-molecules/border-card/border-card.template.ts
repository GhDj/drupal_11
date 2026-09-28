import { headingTemplate } from "@atoms/heading/heading.template";
import { linkTemplate } from "@molecules/link/link.template";

import {
  badgeTemplate,
  type BadgeTheme
} from "@molecules/badge/badge.template";

import { nsp, html } from "@globals/index";

export type BorderCardVariant =
  | "neutral"
  | "high"
  | "critical"
  | "info"
  | "success"
  | "transparent"
  | "small";
export interface BorderCardArgs {
  title?: string;
  date?: string;
  linkText?: string;
  linkUrl?: string;
  linkIconBefore?: string;
  badgeInfo?: BadgeTheme[];
  variant?: BorderCardVariant[];
  colorVariant?: Exclude<BorderCardVariant, "small">;
  small?: boolean;
  extraClasses?: string;
}
export function borderCardTemplate({
  title = "",
  date = "",
  linkText = "Mehr erfahren",
  linkUrl = "intern",
  linkIconBefore = "chevron-right",
  badgeInfo = ["medium", "date", "low"],
  colorVariant = "neutral",
  small = false,
  extraClasses = ""
}: BorderCardArgs): string {
  const hasLink = Boolean(linkText);
  const hasTitle = Boolean(title);

  const link = hasLink
    ? linkTemplate({
        linkUrl,
        linkText,
        linkIconBefore,
        linkExtraClasses: nsp("link--button")
      })
    : "";

  const variants: BorderCardVariant[] = [
    colorVariant,
    ...(small ? ["small" as const] : [])
  ];

  const variantClasses = variants.map(v => `border-card--${v}`).join(" ");

  const dateBadge = date
    ? badgeTemplate({
        badgeTitle: date,
        badgeTheme: "date"
      })
    : "";

  const infoBadges = (badgeInfo ?? [])
    .map(badge =>
      badgeTemplate({
        badgeTitle: badge,
        badgeTheme: badge
      })
    )
    .join("");

  const titleHTML = hasTitle
    ? headingTemplate({
        headingText: title,
        headingExtraClasses: "border-card__title",
        layout: 3,
        style: 4
      })
    : "";

  const badges = infoBadges
    ? html` <div class="${nsp("border-card__badges")}">${infoBadges}</div> `
    : "";

  return html`
    <div
      class="${nsp("border-card", `${variantClasses} ${extraClasses}`.trim())}"
    >
      <div class="${nsp("border-card__meta")}">${dateBadge} ${badges}</div>

      ${titleHTML} ${link}
    </div>
  `;
}
