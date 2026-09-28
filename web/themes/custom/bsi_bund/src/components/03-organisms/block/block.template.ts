import { html, nsp } from "@globals/index";
import {
  headingTemplate,
  type HeadingLevel
} from "@atoms/heading/heading.template";
import { placeholderTemplate } from "@base/placeholder/placeholder.template";
import { linkTemplate } from "@molecules/link/link.template";

/* ---------------------------
  Types
--------------------------- */

export interface BlockTemplateArgs {
  blockTitle?: string;
  blockContent?: string;
  blockExtraClasses?: string;
  contentIndented?: boolean;
  richtextIndented?: boolean;
  headingLayout?: HeadingLevel;
  headingStyle?: HeadingLevel;
  metaLink?: string;
}

/* ---------------------------
  Template
--------------------------- */

export function blockTemplate({
  blockTitle,
  blockContent = "",
  blockExtraClasses = "",
  headingLayout = 2,
  headingStyle = 2,
  contentIndented = false,
  richtextIndented = false,
  metaLink
}: BlockTemplateArgs = {}): string {
  const contentClasses = [
    "block__content",
    contentIndented ? "block__content--indented" : "",
    richtextIndented ? "block__content--indented-richtext" : ""
  ].filter(Boolean);

  return html`
    <div class="${nsp("block", "grid", blockExtraClasses)}">
      <div class="${nsp("block__wrapper ")}">
        <!-- heading -->
        ${
          blockTitle
            ? headingTemplate({
                headingText: blockTitle,
                layout: headingLayout,
                style: headingStyle,
                headingExtraClasses: "block__title"
              })
            : ""
        }

        <!-- link -->
        ${
          metaLink
            ? linkTemplate({
                linkText: metaLink,
                linkUrl: "#",
                linkIconBefore: "chevron-right",
                linkExtraClasses:
                  "link--button link--tertiary-button block__link"
              })
            : ""
        }
      </div>

      <div class="${nsp(...contentClasses)}">
        ${blockContent ? blockContent : placeholderTemplate()}
      </div>
    </div>
  `;
}
