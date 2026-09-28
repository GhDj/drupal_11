import { nsp, html } from "@globals/index";

import { defaultListItems, type listVariants } from "./list.constants";

export type ListVariant = (typeof listVariants)[number];

export interface ListTemplateArgs {
  items?: string[];
  variant?: ListVariant;
  extraClasses?: string;
}

function renderListItem(item: string): string {
  if (!item) {
    return "";
  }

  return html`<li class="${nsp("list__item")}">${item}</li>`;
}

export function listTemplate({
  items = defaultListItems,
  variant = "bullet",
  extraClasses = ""
}: ListTemplateArgs = {}): string {
  const renderedItems = (items ?? [])
    .map(renderListItem)
    .filter(Boolean)
    .join("");
  const hasItems = Boolean(renderedItems);

  if (!hasItems) {
    return "";
  }

  return html`
    <ul class="${nsp("list", `list--${variant}`, extraClasses)}">
      ${renderedItems}
    </ul>
  `;
}
