import { nsp, html } from "@globals/index";

export type BadgeTheme =
  "low" | "medium" | "high" | "critical" | "none" | "status" | "date";

export interface BadgeTemplateArgs {
  badgeTitle?: string;
  badgeTheme?: BadgeTheme;
  badgeExtraClasses?: string;
}

export function badgeTemplate({
  badgeTitle = "Badge",
  badgeTheme,
  badgeExtraClasses = ""
}: BadgeTemplateArgs = {}): string {
  const themeClass = badgeTheme ? `badge--${badgeTheme}` : "";

  const classes = [badgeExtraClasses, themeClass].filter(Boolean).join(" ");

  return html` <span class="${nsp("badge", classes)}"> ${badgeTitle} </span> `;
}
