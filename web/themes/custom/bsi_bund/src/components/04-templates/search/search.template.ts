import { html, nsp } from "@globals/index";
import { headingTemplate } from "@atoms/heading/heading.template";
import { openerTemplate } from "@organisms/opener/opener.template";
import { searchbarTemplate } from "@molecules/searchbar/searchbar.template";

/* ---------------------------
  Types
--------------------------- */

export interface SearchTemplateArgs {
  title?: string;
  openerTitle?: string;
  openerText?: string;
  searchTitle?: string;
  imageSrc?: string;
  extraClasses?: string;
}

/* ---------------------------
  Template
--------------------------- */

export function SearchTemplate({
  title = "Jahreslagebericht 2025",
  openerTitle = "Die Systematik der Lagebewertung",
  openerText = "Lorem ipsum dolor sit amet.",
  searchTitle = "Lagebericht durchsuchen",
  extraClasses = ""
}: SearchTemplateArgs = {}): string {
  return html`
    <div class="${nsp("search-page grid", extraClasses)}">
      ${
        title
          ? headingTemplate({
              headingText: title,
              layout: 2,
              style: 2,
              headingExtraClasses: "search-page__title"
            })
          : ""
      }

      <div class="${nsp("search-page__opener")}">
        ${openerTemplate({
          imageSrc: "demo-content-page-suche.jpg",
          title: openerTitle,
          text: openerText
        })}
      </div>
      <div class="${nsp("search-page__search")}">
        ${searchbarTemplate({
          title: searchTitle,
          placeholder: "Suchbegriff eingeben"
        })}
      </div>
    </div>
  `;
}
