// ##############################################################################################################################################
// ⚠️ DEPRECATED: USE THE BUTTON-STYLES VARIANTS OF THE LINK COMPONENT INSTEAD: http://localhost:6006/?path=/story/molecules-link--button-styles
// ##############################################################################################################################################

// ##############################################################################################################################################
// ⚠️ DEPRECATED: USE THE BUTTON-STYLES VARIANTS OF THE LINK COMPONENT INSTEAD: http://localhost:6006/?path=/story/molecules-link--button-styles
// ##############################################################################################################################################

// ##############################################################################################################################################
// ⚠️ DEPRECATED: USE THE BUTTON-STYLES VARIANTS OF THE LINK COMPONENT INSTEAD: http://localhost:6006/?path=/story/molecules-link--button-styles
// ##############################################################################################################################################

// Globals
import { nsp, html } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";
import type {
  linkButtonSizes,
  linkButtonStates,
  linkButtonVariants
} from "./link-button.constants";

export type AdditionalLinkButtonAttributes = Record<
  string,
  string | number | boolean
>;
export type LinkButtonVariant = (typeof linkButtonVariants)[number];
export type LinkButtonSize = (typeof linkButtonSizes)[number];
export type LinkButtonState = (typeof linkButtonStates)[number];

export interface LinkButtonTemplateArgs {
  linkButtonUrl?: string;
  linkButtonText?: string;
  linkButtonAriaLabel?: string;
  linkButtonVariant?: LinkButtonVariant;
  linkButtonSize?: LinkButtonSize;
  linkButtonState?: LinkButtonState;
  linkButtonExtraClasses?: string;
  additionalLinkButtonAttributes?: AdditionalLinkButtonAttributes;
  linkButtonIconBefore?: string | null;
  linkButtonIconAfter?: string | null;
}

export function linkButtonTemplate({
  linkButtonUrl = "#",
  linkButtonText = "Button Link Text",
  linkButtonAriaLabel,
  linkButtonVariant = "primary",
  linkButtonSize = "default",
  linkButtonState = "default",
  linkButtonExtraClasses = "",
  additionalLinkButtonAttributes = {},
  linkButtonIconBefore = null,
  linkButtonIconAfter = null
}: LinkButtonTemplateArgs = {}): string {
  const isDisabled = linkButtonState === "disabled";
  const hasOnlyIcon = Boolean(
    !linkButtonText && [linkButtonIconBefore, linkButtonIconAfter].some(Boolean)
  );
  const modifierClasses = [
    linkButtonVariant ? `link-button--${linkButtonVariant}` : "",
    linkButtonSize !== "default" ? `link-button--size-${linkButtonSize}` : "",
    linkButtonState !== "default"
      ? `link-button--state-${linkButtonState}`
      : "",
    hasOnlyIcon ? "link-button--icon-only" : ""
  ]
    .filter(Boolean)
    .join(" ");

  const attrs = [
    isDisabled ? "" : `href="${linkButtonUrl}"`,
    `class="${nsp("link-button", modifierClasses, linkButtonExtraClasses)}"`,
    isDisabled ? 'role="button"' : "",
    isDisabled ? 'aria-disabled="true"' : "",
    isDisabled ? 'tabindex="-1"' : "",
    !linkButtonText && linkButtonAriaLabel
      ? `aria-label="${linkButtonAriaLabel}"`
      : "",
    ...Object.entries(additionalLinkButtonAttributes).map(
      ([key, value]) => `${key}="${String(value)}"`
    )
  ]
    .filter(Boolean)
    .join(" ");

  const children = [
    linkButtonIconBefore
      ? iconTemplate({
          iconName: linkButtonIconBefore,
          iconDecorative: true,
          iconExtraClasses: `link-button__icon link-button__icon--${linkButtonIconBefore}`
        })
      : "",
    linkButtonText
      ? `<span class="${nsp("link-button__text")}">${linkButtonText}</span>`
      : "",
    linkButtonIconAfter
      ? iconTemplate({
          iconName: linkButtonIconAfter,
          iconDecorative: true,
          iconExtraClasses: `link-button__icon link-button__icon--${linkButtonIconAfter}`
        })
      : ""
  ].filter(Boolean);

  const linkButtonContent = children.join("\n");

  return linkButtonContent
    ? html`<a ${attrs}>${linkButtonContent}</a>`
    : html`<a ${attrs}></a>`;
}
