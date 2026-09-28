/**
 * Renders a copyright notice.
 */

import { nsp, html } from "@globals/index";

export interface CopyrightTemplateArgs {
  copyrightText?: string;
  copyrightPrefix?: string;
  copyrightExtraClasses?: string;
}

export function copyrightTemplate({
  copyrightText = "",
  copyrightPrefix = "Quelle:",
  copyrightExtraClasses = ""
}: CopyrightTemplateArgs = {}): string {
  if (!copyrightText) return "";

  return html`
    <div class="${nsp("copyright", copyrightExtraClasses)}">
      <span class="${nsp("copyright__text")}">
        ${copyrightPrefix} ${copyrightText}
      </span>
    </div>
  `;
}
