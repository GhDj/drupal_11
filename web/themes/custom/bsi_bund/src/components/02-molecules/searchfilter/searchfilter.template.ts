/**
 * Renders a search filter component with selectable filter fields, active filter pills,
 * and an reset button.
 *
 * @param filters - Optional: Array of filter configurations with ID, label, options, and optional selected value.
 * @param activeFilters - Optional: Array of currently active filter labels rendered as removable pills.
 * @param extraClasses - Optional: Extra classes for the search filter container.
 *
 * @example
 * import { searchfilterTemplate } from "@molecules/searchfilter/searchfilter.template";
 *
 * // Search filter with selectable options and active filters
 * searchfilterTemplate({
 *   filters: [
 *     {
 *       id: "format",
 *       label: "Format",
 *       options: [
 *         { value: "digital", label: "Digital" },
 *       ],
 *     }
 *   ],
 *   activeFilters: ["Digital"],
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { buttonTemplate } from "@molecules/button/button.template";
import { pillTemplate } from "@molecules/pill/pill.template";
import { searchbarTemplate } from "@molecules/searchbar/searchbar.template";

// Arguments accepted by the template
export interface SearchFilterOption {
  value: string;
  label: string;
}

export interface SearchFilter {
  id: string;
  label: string;
  options: SearchFilterOption[];
  value?: string;
}

export interface SearchfilterTemplateArgs {
  filters?: SearchFilter[];
  activeFilters?: string[];
  extraClasses?: string;
}

// Return the HTML string
export function searchfilterTemplate({
  filters = [],
  activeFilters,
  extraClasses = ""
}: SearchfilterTemplateArgs = {}): string {
  const filterFields = filters
    .map(
      filter => `
    <div class="${nsp("search-dropdown")}">
      <label
        for="${filter.id}"
        class="${nsp("search-dropdown__label")}"
      >
        ${filter.label}
      </label>

      <select
        id="${filter.id}"
        name="${filter.id}"
        class="${nsp("search-dropdown__select")}"
      >
        <option value="">${filter.label}</option>

        ${filter.options
          .map(
            option => html`
              <option
                value="${option.value}"
                ${filter.value === option.value ? "selected" : ""}
              >
                ${option.label}
              </option>
            `
          )
          .join("")}
      </select>
    </div>
  `
    )
    .join("");

  const activeFilterContent = activeFilters
    ? `
      <div class="${nsp("searchfilter__pills")}">
        ${activeFilters
          .map(filter =>
            pillTemplate({
              pillTitle: filter,
              pillVariant: "default"
            })
          )
          .join("")}
      </div>
      ${buttonTemplate({
        buttonIconBefore: "cross",
        buttonVariant: "tertiary",
        buttonText: "Alle Filter zurücksetzen",
        buttonAriaLabel: "Alle Filter zurücksetzen"
      })}
  `
    : "";

  return html`
    <div class="${nsp("searchfilter", extraClasses)}">
      <div class="${nsp("searchfilter__inner")}">
        ${searchbarTemplate()}
        <p class="${nsp("searchfilter__text")}">
          <strong>1 bis 10</strong> von <strong>8.609</strong> Ergebnissen für
          Ihre Suche nach <strong>Suchbegriff</strong>
        </p>
        <hr />
        <div class="${nsp("searchfilter__top")}">${filterFields}</div>
        <div class="${nsp("searchfilter__bottom")}">${activeFilterContent}</div>
      </div>
    </div>
  `;
}
