// Globals
import { nsp, html } from "@globals/index";
import {
  cardTemplate,
  type CardTemplateArgs
} from "@molecules/card/card.template";
import { paginationTemplate } from "@molecules/pagination/pagination.template";

export interface SearchResultTemplateArgs {
  results?: CardTemplateArgs[];
}

// Return the HTML string
export function searchResultTemplate({
  results
}: SearchResultTemplateArgs = {}): string {
  const sort = html`
    <div class="${nsp("search-dropdown search-dropdown--sort")}">
      <label
        for="edit-sort-bef-combine"
        class="${nsp("search-dropdown__label")}"
        >Sortieren</label
      >
      <select class="${nsp("search-dropdown__select")}">
        <option value="created_ASC">Created Aufsteigend</option>
        <option value="created_DESC" selected="">Created Absteigend</option>
      </select>
    </div>
  `;

  return html`
    <div class="${nsp("search-result")}">
      <div class="${nsp("search-result__inner")}">
        <div class="${nsp("search-result__head")}">${sort}</div>
        <ul class="${nsp("search-result__list")}">
          ${
            results
              ? results
                  .map(item => {
                    return `<li class="${nsp("search-result__list-item")}">
                    ${cardTemplate({ cardExtraClasses: "card--search", ...item })}
            </li>`;
                  })
                  .join("")
              : ""
          }
        </ul>
        ${paginationTemplate({})}
      </div>
    </div>
  `;
}
