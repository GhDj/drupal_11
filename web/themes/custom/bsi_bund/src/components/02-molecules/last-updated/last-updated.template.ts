import { nsp, html } from "@globals/index";

export interface LastUpdatedTemplateArgs {
  date?: string;
  dateTime?: string;
}

export function lastUpdatedTemplate({
                                      date = "15.03.2026",
                                      dateTime = "2026-03-15"
                                    }: LastUpdatedTemplateArgs = {}): string {
  return html`
    <div class="${nsp("last-updated")}">
      <span class="${nsp("last-updated__label")}">Zuletzt aktualisiert:</span>
      <time class="${nsp("last-updated__item")}" datetime="${dateTime}">
        ${date}
      </time>
    </div>
  `;
}
