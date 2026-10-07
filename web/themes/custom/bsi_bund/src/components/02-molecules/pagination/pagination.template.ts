/**
 * Renders a pagination component with previous/next navigation and,
 * depending on the selected variant, page links and ellipsis items.
 *
 * The component is arg-driven and generates its visible page model from
 * `paginationCurrentPage` and `paginationTotalPages`.
 *
 * Variant behavior:
 * - `large`: renders the extended pagination pattern with page links and ellipsis
 * - `small`: renders a reduced pagination pattern for compact layouts
 * - `no-numbers`: renders only previous/next controls without page links
 *
 * Defensive bounds handling:
 * - `paginationTotalPages` is clamped to at least 1
 * - `paginationCurrentPage` is clamped into the valid range 1..paginationTotalPages
 *
 * @param paginationCurrentPage - Required: The currently active page.
 * @param paginationTotalPages - Required: Total number of available pages.
 * @param variant - Required: Visual and logical pagination variant.
 * @param paginationBaseUrl - Optional: Base URL used to build page links.
 * @param buttonTitleNext - Optional: Title attribute for the next button.
 * @param buttonTitlePrevious - Optional: Title attribute for the previous button.
 *
 * @example
 * import { paginationTemplate } from "@molecules/pagination/pagination.template";
 *
 * paginationTemplate({
 *   paginationCurrentPage: 3,
 *   paginationTotalPages: 20,
 *   paginationBaseUrl: "#",
 * });
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { linkButtonTemplate } from "@molecules/link-button/link-button.template";
import { createPaginationItems } from "./pagination-items.template";
import { iconTemplate } from "@atoms/icon/icon.template";

// Arguments accepted by the template
export interface PaginationTemplateArgs {
  paginationTotalPages?: number;
  paginationCurrentPage?: number;
  paginationBaseUrl?: string;
  buttonTitleNext?: string;
  buttonTitlePrevious?: string;
}

// Return the HTML string for the component
export function paginationTemplate({
  paginationCurrentPage = 1,
  paginationTotalPages = 10,
  buttonTitleNext = "Zur nächsten Seite gehen",
  buttonTitlePrevious = "Zur vorherigen Seite gehen"
}: PaginationTemplateArgs = {}): string {
  // Normalize incoming values so the template always renders a valid state,
  // even if Storybook args or external data contain invalid numbers.
  const safepaginationTotalPages = Math.max(1, paginationTotalPages);
  const safepaginationCurrentPage = Math.min(
    Math.max(1, paginationCurrentPage),
    safepaginationTotalPages
  );

  // Navigation controls are disabled at the range boundaries.
  const isPreviousDisabled = safepaginationCurrentPage === 1;
  const isNextDisabled = safepaginationCurrentPage === safepaginationTotalPages;

  // Previous control is always rendered, regardless of variant.
  const previousLink = linkButtonTemplate({
    linkButtonUrl: "#",
    linkButtonText: "",
    linkButtonAriaLabel: buttonTitlePrevious,
    linkButtonVariant: "primary",
    linkButtonIconBefore: "chevron-left",
    linkButtonExtraClasses: "pagination__control",
    additionalLinkButtonAttributes: {
      title: buttonTitlePrevious,
      ...(isPreviousDisabled ? { "aria-disabled": true, tabindex: -1 } : {})
    }
  });

  // Next control
  const nextLink = linkButtonTemplate({
    linkButtonUrl: "#",
    linkButtonText: "",
    linkButtonAriaLabel: buttonTitleNext,
    linkButtonVariant: "primary",
    linkButtonIconBefore: "chevron-right",
    linkButtonExtraClasses: "pagination__control",
    additionalLinkButtonAttributes: {
      title: buttonTitleNext,
      ...(isNextDisabled ? { "aria-disabled": true, tabindex: -1 } : {})
    }
  });

  // Derive the visible pagination structure from the normalized state.
  const paginationItems = createPaginationItems(
    safepaginationCurrentPage,
    safepaginationTotalPages
  );

  // Build all visible page items.
  const itemsHtml = paginationItems
    .map(item => {
      if (item.type === "ellipsis") {
        return html`
          <span class="${nsp("pagination__ellipsis")}" aria-hidden="true">
            ${iconTemplate({ iconName: "ellipses", iconDecorative: true })}
          </span>
        `;
      }

      // Mark the active page so styling and accessibility hooks can target it.
      const isCurrent = item.page === safepaginationCurrentPage;

      return linkButtonTemplate({
        linkButtonUrl: "#",
        linkButtonText: String(item.page),
        linkButtonVariant: "tertiary",
        linkButtonExtraClasses: "pagination__item",
        additionalLinkButtonAttributes: {
          ...(isCurrent ? { "aria-current": "page" } : {})
        }
      });
    })
    .join("");

  return html`
    <nav class="${nsp("pagination")}" aria-label="Pagination">
      ${previousLink}
      ${
        itemsHtml
          ? `<div class="${nsp("pagination__list")}">${itemsHtml}</div>`
          : ""
      }
      ${nextLink}
    </nav>
  `;
}
