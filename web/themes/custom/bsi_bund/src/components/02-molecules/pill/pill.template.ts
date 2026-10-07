/**
 * Renders a pill component with an optional variant and remove icon.
 *
 * @param pillTitle - Optional: Text displayed inside the pill.
 * @param pillVariant - Optional: Visual/state variant of the pill.
 * @param pillExtraClasses - Optional: Additional CSS classes.
 *
 * @example
 * pillTemplate({
 *   pillTitle: "title",
 *   pillVariant: "selected",
 * });
 */

// Globals
import { html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";

export type PillVariant =
  "default" | "selected" | "read-only" | "disabled" | "brand";

// Arguments accepted by the template
export interface PillTemplateArgs {
  pillTitle?: string;
  pillVariant?: PillVariant;
  pillExtraClasses?: string;
}

// Return the HTML string
export function pillTemplate({
  pillTitle,
  pillVariant,
  pillExtraClasses = ""
}: PillTemplateArgs = {}): string {
  const themeClass = pillVariant ? `pill--${pillVariant}` : "";
  const classes = [pillExtraClasses, themeClass].filter(Boolean).join(" ");

  const buttonState =
    pillVariant === "disabled" || pillVariant === "read-only"
      ? "disabled"
      : "default";

  const buttonIconAfter =
    pillVariant === "default" || pillVariant === "selected" ? "cross" : null;

  return html`
    ${buttonTemplate({
      buttonIconAfter,
      buttonVariant: "tertiary",
      buttonState,
      buttonText: pillTitle ?? "",
      buttonAriaLabel: `${pillTitle ?? ""} entfernen`,
      buttonExtraClasses: `pill ${classes}`
    })}
  `;
}
