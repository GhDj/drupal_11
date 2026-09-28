import { nsp, html } from "@globals/index.ts";

// Components
import { buttonTemplate } from "@molecules/button/button.template";

export interface SearchbarTemplateArgs {
  title?: string;
  placeholder?: string;
  extraClasses?: string;
}

export function searchbarTemplate({
  title = "",
  placeholder = "Suchbegriff eingeben",
  extraClasses = ""
}: SearchbarTemplateArgs = {}): string {
  return html`
    ${title ? `<span class="${nsp("searchbar__title")}">${title}</span>` : ""}
    <div class="${nsp("searchbar", extraClasses)}">
      <input
        id="searchbar-ID"
        aria-label="Suche"
        class="${nsp("searchbar__input")}"
        type="text"
        placeholder="${placeholder}"
      />

      ${buttonTemplate({
        buttonIconBefore: "search",
        buttonVariant: "tertiary",
        buttonAriaLabel: "Suche",
        buttonExtraClasses: "searchbar__button",
        buttonType: "submit"
      })}
    </div>
  `;
}
