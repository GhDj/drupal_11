// Globals
import { nsp, html } from "@globals/index";

import {
  imageTemplate,
  type ImageTemplateArgs
} from "@atoms/image/image.template";
// Components

interface TimelineItem {
  title?: string;
  description?: string;
  image?: ImageTemplateArgs;
}

interface TimelineMonth {
  title: string;
  items: TimelineItem[];
}

interface TimelineYear {
  title: string;
  months: TimelineMonth[];
}

export interface TimelineTemplateArgs {
  years?: TimelineYear[];
}

// Return the HTML string for the form
export function timelineTemplate({ years }: TimelineTemplateArgs): string {
  let monthIndex = 0;

  const timelineContent = years
    ?.map(
      year => html`
        <div class="${nsp("timeline__year")}">
          <div class="${nsp("timeline__year-title")}">${year.title}</div>

          ${year.months
            .map(month => {
              const monthClass =
                monthIndex % 2 === 0
                  ? " timeline__month--align-left"
                  : " timeline__month--align-right";
              monthIndex += 1;
              return html`
                <div class="${nsp("timeline__month", monthClass)}">
                  <div class="${nsp("timeline__month-title")}">
                    ${month.title}
                  </div>

                  ${month.items
                    .map(
                      item => html`
                        <div class="${nsp("timeline__item")}">
                          <div class="${nsp("timeline__marker")}"></div>

                          <div class="${nsp("timeline__card")}">
                            <h3 class="${nsp("timeline__card-title")}">
                              ${item.title ?? ""}
                            </h3>
                            <p>${item.description ?? ""}</p>
                            ${item.image ? imageTemplate(item.image) : ""}
                          </div>
                        </div>
                      `
                    )
                    .join("")}
                </div>
              `;
            })
            .join("")}
        </div>
      `
    )
    .join("");

  return html`
    <div class="${nsp("timeline")}">
      <div class="${nsp("timeline__content")}">
        <div class="${nsp("timeline__progress")}"></div>
        ${timelineContent}
      </div>
    </div>
  `;
}
