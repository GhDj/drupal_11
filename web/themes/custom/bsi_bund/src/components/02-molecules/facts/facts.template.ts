import { nsp, html } from "@globals/index";

export interface FactsAndFiguresTemplateArgs {
  figurePrefix?: string;
  figureValue?: string;
  figureSuffix?: string;
  text?: string;
  extraClasses?: string;
}

export function factsAndFiguresTemplate({
  figurePrefix = "",
  figureValue = "",
  figureSuffix = "",
  text = "",
  extraClasses = ""
}: FactsAndFiguresTemplateArgs = {}): string {
  const hasFigure = Boolean(figurePrefix || figureValue || figureSuffix);
  const hasText = Boolean(text);

  return html`
    <article class="${nsp("facts", extraClasses)}">
      ${
        hasFigure
          ? html`
              <p class="${nsp("facts__figure")}" data-bsi-facts-value>
                ${figureValue ? figureValue : ""}
              </p>
            `
          : ""
      }
      ${hasText ? html`<p class="${nsp("facts__text")}">${text}</p>` : ""}
    </article>
  `;
}
