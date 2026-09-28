/**
 * Renders a button with optional icons and various button types.
 *
 * @param buttonText - The visible button text.
 * @param buttonAriaLabel - Optional: aria-label for accessibility. Required when buttonText is not provided.
 * @param buttonType - Optional: The button type attribute (button, submit, reset).
 * @param buttonVariant - Optional: Visual variant or inline action variant.
 * @param buttonSize - Optional: Icon-only control size (lg, md, sm).
 * @param buttonMode - Optional: Color mode (default, on-color).
 * @param buttonState - Optional: Forced visual state for Storybook previews.
 * @param buttonDisabled - Optional: Whether the button is disabled.
 * @param buttonExtraClasses - Optional: Extra classes for the button element.
 * @param additionalButtonAttributes - Optional: Additional HTML attributes as object.
 * @param buttonIconBefore - Optional: icon name to render before text.
 * @param buttonIconAfter - Optional: icon name to render after text.
 *
 *
 * @example
 * import { buttonTemplate } from "@molecules/button/button.template";
 *
 * buttonTemplate({
 *   buttonText: "Click me",
 *   buttonType: "button",
 *   buttonVariant: "primary",
 *   buttonIconAfter: "arrow-right",
 *   additionalButtonAttributes: {
 *     "data-js": "submit"
 *   }
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";
import { inlineActionVariants } from "./button.constants";
import type {
  buttonModes,
  buttonSizes,
  buttonStates,
  buttonVariants,
  buttonVisualVariants
} from "./button.constants";

// Type alias for button attributes
export type AdditionalButtonAttributes = Record<
  string,
  string | number | boolean
>;

// Custom types
export type ButtonType = "button" | "submit" | "reset";
export type ButtonVisualVariant = (typeof buttonVisualVariants)[number];
export type InlineActionButtonVariant = (typeof inlineActionVariants)[number];
export type ButtonVariant = (typeof buttonVariants)[number];
export type ButtonSize = (typeof buttonSizes)[number];
export type ButtonMode = (typeof buttonModes)[number];
export type ButtonState = (typeof buttonStates)[number];

// Arguments accepted by the template
export interface ButtonTemplateArgs {
  buttonText?: string;
  buttonAriaLabel?: string;
  buttonType?: ButtonType;
  buttonVariant?: ButtonVariant;
  buttonSize?: ButtonSize;
  buttonMode?: ButtonMode;
  buttonState?: ButtonState;
  buttonDisabled?: boolean;
  buttonExtraClasses?: string;
  additionalButtonAttributes?: AdditionalButtonAttributes;
  buttonIconBefore?: string | null;
  buttonIconAfter?: string | null;
}

const inlineActionIconByVariant: Record<InlineActionButtonVariant, string> = {
  back: "arrow-left",
  close: "cross",
  expand: "plus"
};

const inlineActionIconPositionByVariant: Record<
  InlineActionButtonVariant,
  "before" | "after"
> = {
  back: "before",
  close: "after",
  expand: "before"
};

// Return the HTML string for the button with default values
export function buttonTemplate({
  buttonText,
  buttonAriaLabel,
  buttonType = "button",
  buttonVariant = "primary",
  buttonSize,
  buttonMode = "default",
  buttonState = "default",
  buttonDisabled = false,
  buttonExtraClasses = "",
  additionalButtonAttributes = {},
  buttonIconBefore = null,
  buttonIconAfter = null
}: ButtonTemplateArgs = {}): string {
  // -------------------------
  // State
  // -------------------------
  const isInlineAction = inlineActionVariants.includes(
    buttonVariant as InlineActionButtonVariant
  );

  const inlineVariant = isInlineAction
    ? (buttonVariant as InlineActionButtonVariant)
    : null;

  const isDisabled = buttonDisabled || buttonState === "disabled";

  // -------------------------
  // Icons
  // -------------------------
  function resolveIcon(position: "before" | "after") {
    if (!inlineVariant) {
      return position === "before" ? buttonIconBefore : buttonIconAfter;
    }

    const expectedPosition = inlineActionIconPositionByVariant[inlineVariant];

    if (expectedPosition === position) {
      return inlineActionIconByVariant[inlineVariant];
    }

    return null;
  }

  const resolvedIconBefore = resolveIcon("before");
  const resolvedIconAfter = resolveIcon("after");

  const hasOnlyIcon =
    !buttonText && !isInlineAction && (resolvedIconBefore ?? resolvedIconAfter);

  // -------------------------
  // Classes
  // -------------------------
  const modifierClasses = [
    `button--${buttonVariant}`,
    isInlineAction && "button--inline-action",
    buttonSize && buttonSize !== "default" && `button--size-${buttonSize}`,
    buttonMode !== "default" && `button--mode-${buttonMode}`,
    buttonState !== "default" && `button--state-${buttonState}`,
    hasOnlyIcon && "button--icon-only",
    hasOnlyIcon && buttonSize && "button--control"
  ]
    .filter(Boolean)
    .join(" ");

  const { class: externalClasses = "", ...restAttributes } =
    additionalButtonAttributes;

  const classAttr = [
    nsp("button", modifierClasses, buttonExtraClasses),
    externalClasses
  ]
    .filter(Boolean)
    .join(" ");

  // -------------------------
  // Attributes
  // -------------------------
  const attributes = [
    `type="${buttonType}"`,
    `class="${classAttr}"`,
    isDisabled && "disabled",
    !buttonText && buttonAriaLabel && `aria-label="${buttonAriaLabel}"`,
    ...Object.entries(restAttributes).map(
      ([key, value]) => `${key}="${String(value)}"`
    )
  ]
    .filter(Boolean)
    .join(" ");

  // -------------------------
  // Content
  // -------------------------
  function renderIcon(icon: string | null) {
    if (!icon) return "";

    return iconTemplate({
      iconName: icon,
      iconDecorative: true,
      iconExtraClasses: `button__icon button__icon--${icon}`
    });
  }

  const content = [
    renderIcon(resolvedIconBefore),
    buttonText && `<span class="${nsp("button__text")}">${buttonText}</span>`,
    renderIcon(resolvedIconAfter)
  ]
    .filter(Boolean)
    .join("\n");

  // -------------------------
  // Render
  // -------------------------
  return html`<button ${attributes}>${content}</button>`;
}
