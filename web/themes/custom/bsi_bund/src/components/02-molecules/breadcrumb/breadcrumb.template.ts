// Globals
import { nsp, html } from "@globals/index";
import { linkTemplate } from "@molecules/link/link.template";

export interface BreadcrumbItemType {
  text: string;
  url?: string;
  active?: boolean;
}

// Arguments accepted by the template
export interface BreadcrumbTemplateArgs {
  breadcrumbItems?: BreadcrumbItemType[];
}

// Return the HTML string for a link
export function breadcrumbTemplate({
  breadcrumbItems = [
    { text: "Unterkategorie Level 1", url: "/subpage" },
    { text: "Unterkategorie Level 2", url: "/subsubpage" },
    { text: "Aktuelle Seite", active: true }
  ]
}: BreadcrumbTemplateArgs): string {
  const getBreadcrumbItem = (item: BreadcrumbItemType) =>
    item.active
      ? `<li
          class="${nsp("breadcrumb__list-item")}"
          aria-current="page"
          >${item.text}</li>`
      : `<li
          class="${nsp("breadcrumb__list-item")}">
          ${linkTemplate({
            linkExtraClasses: "breadcrumb__link",
            linkUrl: item.url ?? "#",
            linkText: item.text ?? "",
            linkTitle: item.text ?? ""
          })}</li>`;

  // Return the final HTML string
  return html`
    <nav class="${nsp("breadcrumb")}" aria-label="Breadcrumb">
      <ol class="${nsp("breadcrumb__list")}">
        <li class="${nsp("breadcrumb__list-item")}">
          ${linkTemplate({
            linkExtraClasses: "breadcrumb__home",
            linkUrl: "/",
            linkText: "",
            linkAriaLabel: "Startseite",
            linkIconBefore: "home"
          })}
        </li>
        ${breadcrumbItems.map(item => getBreadcrumbItem(item)).join("")}
      </ol>
    </nav>
  `;
}
