import { nsp, html } from "@globals/index";

export interface IconTemplateArgs {
  iconName: string;
  iconExtraClasses?: string;
  iconDecorative?: boolean;
  iconTitle?: string | null;
  iconDesc?: string | null;
}

export function iconTemplate({
  iconName,
  iconExtraClasses = "",
  iconDecorative = true,
  iconTitle = null,
  iconDesc = null
}: IconTemplateArgs): string {
  const ariaHidden = iconDecorative ? 'aria-hidden="true"' : 'role="img"';
  const focusable = 'focusable="false"';

  return html`
    <svg class="${nsp("icon", iconExtraClasses)}" ${ariaHidden} ${focusable}>
      <use href="/dist/icons.svg#${iconName}"></use>

      ${!iconDecorative && iconTitle ? `<title>${iconTitle}</title>` : ""}
      ${!iconDecorative && iconDesc ? `<desc>${iconDesc}</desc>` : ""}
    </svg>
  `;
}
