// Globals
import { html, nsp } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";
import { buttonTemplate } from "@molecules/button/button.template";

// Content
import { addressContent, phoneContent } from "./toolbar.constants";

// Arguments accepted by the template
export interface ToolbarTemplateArgs {
  phone?: string;
  address?: string;
}

// Return the HTML string for the toolbar
export function toolbarTemplate({
  phone = phoneContent,
  address = addressContent
}: ToolbarTemplateArgs): string {
  // Return the final HTML string
  return html` <div class="${nsp("toolbar")}">
    <!-- phone button -->
    ${buttonTemplate({
      buttonAriaLabel: "open availability information",
      buttonVariant: "secondary",
      buttonIconBefore: "phone",
      buttonExtraClasses: "toolbar__button toolbar__button--phone",
      additionalButtonAttributes: { popovertarget: "toolbar-phone" }
    })}

    <!-- phone popover -->
    <div
      popover="auto"
      id="toolbar-phone"
      class="${nsp("toolbar__popover toolbar__popover--phone")}"
    >
      ${iconTemplate({
        iconName: "phone"
      })}
      <div class="${nsp("toolbar__popover-content")}">${phone}</div>
    </div>

    <!-- mail button -->
    ${buttonTemplate({
      buttonAriaLabel: "open address information",
      buttonVariant: "secondary",
      buttonIconBefore: "mail",
      buttonExtraClasses: "toolbar__button toolbar__button--mail",
      additionalButtonAttributes: { popovertarget: "toolbar-mail" }
    })}

    <!-- mail popover -->
    <div
      popover
      id="toolbar-mail"
      class="${nsp("toolbar__popover toolbar__popover--mail")}"
    >
      ${iconTemplate({
        iconName: "mail"
      })}
      <div class="${nsp("toolbar__popover-content")}">${address}</div>
    </div>
  </div>`;
}
