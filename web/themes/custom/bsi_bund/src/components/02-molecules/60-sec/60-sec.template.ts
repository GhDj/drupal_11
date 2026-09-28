import { nsp, html } from "@globals/index";
import { headingTemplate } from "@atoms/heading/heading.template";

import { defaultSixtySecItems } from "./60-sec.constants";

export interface SixtySecItem {
  title: string;
  text: string;
}

export interface SixtySecTemplateArgs {
  idPrefix?: string;
  blockTitle?: string;
  items?: SixtySecItem[];
  extraClasses?: string;
  blockExtraClasses?: string;
}

let sixtySecInstanceId = 0;

function getIdPrefix(idPrefix?: string): string {
  const customIdPrefix = idPrefix?.trim();

  if (customIdPrefix) {
    return customIdPrefix;
  }

  sixtySecInstanceId += 1;

  return `sixty-sec-${sixtySecInstanceId}`;
}

function renderQuestion(
  item: SixtySecItem,
  index: number,
  idPrefix: string
): string {
  const isActive = index === 0;
  const tabId = `${idPrefix}-tab-${index + 1}`;
  const targetId = `${idPrefix}-panel-${index + 1}`;

  return html`
    <li class="${nsp("60-sec__question-item")}" role="presentation">
      <button
        class="${nsp("60-sec__question")}"
        id="${tabId}"
        type="button"
        role="tab"
        data-bsi-60-sec-question
        data-bsi-60-sec-target="${targetId}"
        aria-controls="${targetId}"
        aria-selected="${isActive ? "true" : "false"}"
        tabindex="${isActive ? "0" : "-1"}"
      >
        ${item.title}
      </button>
    </li>
  `;
}

function renderPanel(
  item: SixtySecItem,
  index: number,
  idPrefix: string
): string {
  const isActive = index === 0;
  const tabId = `${idPrefix}-tab-${index + 1}`;
  const panelId = `${idPrefix}-panel-${index + 1}`;

  return html`
    <section
      class="${nsp("60-sec__panel")}"
      id="${panelId}"
      role="tabpanel"
      data-bsi-60-sec-panel
      aria-labelledby="${tabId}"
      tabindex="-1"
      ${isActive ? "" : "hidden"}
    >
      <p class="${nsp("60-sec__text")}">${item.text}</p>
    </section>
  `;
}

export function sixtySecTemplate({
  idPrefix,
  blockTitle = "In 60 Sekunden",
  items = defaultSixtySecItems,
  extraClasses = "",
  blockExtraClasses = ""
}: SixtySecTemplateArgs = {}): string {
  const hasBlockTitle = Boolean(blockTitle);
  const normalizedItems = (items ?? []).filter(
    item => Boolean(item.title) && Boolean(item.text)
  );
  const resolvedIdPrefix = getIdPrefix(idPrefix);
  const renderedQuestions = normalizedItems
    .map((item, index) => renderQuestion(item, index, resolvedIdPrefix))
    .join("");
  const renderedPanels = normalizedItems
    .map((item, index) => renderPanel(item, index, resolvedIdPrefix))
    .join("");
  const hasContent = Boolean(normalizedItems.length);

  if (!hasBlockTitle && !hasContent) {
    return "";
  }

  return html`
    <div class="${nsp("block", "grid", blockExtraClasses)}">
      ${
        hasBlockTitle
          ? headingTemplate({
              headingText: blockTitle,
              layout: 2,
              style: 2,
              headingExtraClasses: "block__title"
            })
          : ""
      }

      <div class="${nsp("block__content")}">
        ${
          hasContent
            ? html`
                <section class="${nsp("60-sec", extraClasses)}" data-bsi-60-sec>
                  <div class="${nsp("60-sec__summary")}">
                    <ul
                      class="${nsp("60-sec__questions")}"
                      role="tablist"
                      aria-orientation="vertical"
                    >
                      ${renderedQuestions}
                    </ul>
                  </div>
                  ${
                    renderedPanels
                      ? html`
                          <div
                            class="${nsp("60-sec__content", "bodytext")}"
                            data-bsi-60-sec-content
                          >
                            ${renderedPanels}
                          </div>
                        `
                      : ""
                  }
                </section>
              `
            : ""
        }
      </div>
    </div>
  `;
}
