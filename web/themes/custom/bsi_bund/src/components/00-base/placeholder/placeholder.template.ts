/**
 * Renders a placeholder.
 *
 *
 * @example
 * import { placeholderTemplate } from "@base/placeholder/placeholder.template";
 *
 * placeholderTemplate();
 */

// Globals
import { nsp, html } from "@globals/index";

export interface PlaceholderTemplateArgs {
  label?: string;
}

export function placeholderTemplate({
  label = "Placeholder"
}: PlaceholderTemplateArgs = {}): string {
  return html`
    <div class="${nsp("placeholder")}">
      <div class="${nsp("placeholder__content")}">${label}</div>
    </div>
  `;
}
