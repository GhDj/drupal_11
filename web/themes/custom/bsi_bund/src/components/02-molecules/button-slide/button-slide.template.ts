/**
 * Renders a draggable slide button.
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";

export type ButtonSlideState = "default" | "success";

export interface ButtonSlideTemplateArgs {
  buttonSlideText?: string;
  buttonSlideSuccessText?: string;
  buttonSlideState?: ButtonSlideState;
  buttonSlideThreshold?: number;
  buttonSlideExtraClasses?: string;
}

export function buttonSlideTemplate({
  buttonSlideText = "slide to action",
  buttonSlideSuccessText = "action successful",
  buttonSlideState = "default",
  buttonSlideThreshold = 0.55,
  buttonSlideExtraClasses = ""
}: ButtonSlideTemplateArgs = {}): string {
  const isSuccess = buttonSlideState === "success";
  const classes = nsp(
    "button-slide",
    isSuccess ? "button-slide--success" : "",
    buttonSlideExtraClasses
  );
  const ariaText = isSuccess ? buttonSlideSuccessText : buttonSlideText;
  const arrowIcon = iconTemplate({
    iconName: "arrow-right",
    iconDecorative: true,
    iconExtraClasses: "button-slide__icon"
  });

  const checkIcon = iconTemplate({
    iconName: "check",
    iconDecorative: true,
    iconExtraClasses: "button-slide__icon"
  });

  return html`
    <button
      type="button"
      class="${classes} js-button-slide"
      data-slide-state="${buttonSlideState}"
      data-slide-threshold="${String(buttonSlideThreshold)}"
      data-slide-text="${buttonSlideText}"
      data-slide-success-text="${buttonSlideSuccessText}"
      aria-label="${ariaText}"
      aria-pressed="${isSuccess ? "true" : "false"}"
    >
      <span class="${nsp("button-slide__label")}" data-slide-label>
        ${buttonSlideText}
      </span>
      <span class="${nsp("button-slide__fill")}" data-slide-fill>
        <span
          class="${nsp("button-slide__fill-label")}"
          data-slide-progress-label
        >
          ${buttonSlideSuccessText}
        </span>
      </span>
      <span class="${nsp("button-slide__handle")}" data-slide-handle>
        ${arrowIcon}
      </span>
      <span
        class="${nsp("button-slide__success-icon")}"
        data-slide-success-icon
      >
        ${checkIcon}
      </span>
    </button>
  `;
}
