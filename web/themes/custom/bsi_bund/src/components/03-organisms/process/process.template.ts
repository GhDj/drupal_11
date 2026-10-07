import { nsp, html } from "@globals/index";

interface StepArgs {
  label?: string;
  title?: string;
  content?: string;
  addition?: string;
}

export interface ProcessTemplateArgs {
  steps?: StepArgs[];
  showAdditionalColumn: boolean;
}

// Return the HTML string for the process
export function processTemplate({
  steps,
  showAdditionalColumn = true
}: ProcessTemplateArgs): string {
  return html`
    <div class="${nsp("process grid")}">
      <div class="${nsp("grid-indented")}">
        <ol>
          ${steps
            ?.map(step => {
              return `
                <li class="${nsp("process__step")}">
                  ${step.label ? `<span class="${nsp("process__label")}">${step.label}</span>` : ""}

                  <div class="${nsp("process__content")}">
                    ${step.title ? `<h3 class="${nsp("process__title")}">${step.title}</h3>` : ""}

                    <div class="${nsp("bodytext process__text")}">
                      ${step.content ?? ""}
                    </div>
                  </div>

                  ${step.addition && showAdditionalColumn ? `<div class="${nsp("bodytext process__addition")}">${step.addition}</div>` : ""}
                </li>`;
            })
            .join("")}
        </ol>
      </div>
    </div>
  `;
}
