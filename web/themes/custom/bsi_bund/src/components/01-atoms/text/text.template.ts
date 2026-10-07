/**
 * Renders a text or bodytext element.
 *
 * - If `bodytext` is provided, its rich HTML is wrapped in a bodytext <div>
 * - Otherwise, `textContent` is rendered inside a text <p>
 */

// Globals
import { nsp, html } from "@globals/index";
export interface TextTemplateArgs {
  bodytext?: string | null;
  textContent?: string | null;
  textExtraClasses?: string;
  bodytextExtraClasses?: string;
}

export function textTemplate({
  bodytext = null,
  textContent = null,
  textExtraClasses = "",
  bodytextExtraClasses = ""
}: TextTemplateArgs = {}): string {
  if (!bodytext && !textContent) {
    return "";
  }

  // rich text block
  if (bodytext) {
    return `
      <div class="${nsp("text", bodytextExtraClasses, textExtraClasses)}">
        ${bodytext}
      </div>
    `;
  }

  // simple text block
  return html`
    <p class="${nsp("text", textExtraClasses)}">${textContent ?? ""}</p>
  `;
}
