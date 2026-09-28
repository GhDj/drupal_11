/**
 * Renders a heading.
 *
 * @param headingText - The visible heading text.
 * @param layout - Heading level (1–6). Defaults to 2.
 * @param style - Optional style class suffix; falls back to `layout` if omitted.
 * @param headingExtraClasses - Optional extra classes for the <hx> tag.
 *
 *
 * @example
 * import { headingTemplate } from "@atoms/heading/heading.template";
 *
//  * headingTemplate({
//  *   headingText: "This is a heading",
//  *   layout: 2,
//  *   style: 3,
//  *   headingExtraClasses: "heading--highlight",
//  * });
 */

// Globals
import { nsp, html } from "@globals/index";

export type HeadingRenderAs = "heading" | "div";
export type HeadingLevel = 1 | 2 | 3 | 4 | 5 | 6;

export interface HeadingTemplateArgs {
  headingText?: string;
  layout?: HeadingLevel;
  style?: HeadingLevel;
  headingExtraClasses?: string;
  headingHref?: string;
}

export function headingTemplate({
  headingText = "",
  layout = 2,
  style,
  headingExtraClasses = "",
  headingHref = ""
}: HeadingTemplateArgs = {}): string {
  const resolvedStyle = style ?? layout;

  const tag = `h${layout}`;

  const isLinked = Boolean(headingHref);

  const headingClasses = [
    "heading",
    `heading--${resolvedStyle}`,
    isLinked ? "heading--linked" : "",
    headingExtraClasses
  ].filter(Boolean);

  const content = isLinked
    ? html`
        <a href="${headingHref}" class="${nsp("heading__link")}">
          ${headingText}
        </a>
      `
    : headingText;

  return html`
      <${tag} class="${nsp(...headingClasses)}">
        ${content}
      </${tag}>
  `;
}
