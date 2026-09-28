// Globals
import { html } from "@globals/index";

// Arguments accepted by the template
export interface FormTemplateArgs {
  formContent: string;
}

// Return the HTML string for the form
export function formTemplate({ formContent = "" }: FormTemplateArgs): string {
  // Return the final HTML string
  return html`
    <div class="block block-webform block-webform-block">
      <form action="/action" method="post" id="ID_12345">${formContent}</form>
    </div>
  `;
}
