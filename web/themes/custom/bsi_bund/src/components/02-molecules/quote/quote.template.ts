import { imageTemplate } from "@atoms/image/image.template";
import { nsp, html } from "@globals/index";
import { linkTemplate } from "@molecules/link/link.template.ts";

export interface QuoteTemplateArgs {
  imageSrc?: string;
  imageAlt?: string;
  quoteText?: string;
  authorName?: string;
  authorRole?: string;
  linkText?: string;
  linkUrl?: string;
  extraClasses?: string;
}

export function quoteTemplate({
  imageSrc,
  imageAlt = "",
  quoteText,
  authorName,
  authorRole,
  linkText,
  linkUrl = "#",
  extraClasses = ""
}: QuoteTemplateArgs = {}): string {
  const hasImage = Boolean(imageSrc);
  const hasQuote = Boolean(quoteText);
  const hasAuthor = Boolean(authorName) || Boolean(authorRole);
  const hasLink = Boolean(linkText);

  const classes = [!hasImage ? "quote--no-image" : "", extraClasses]
    .filter(Boolean)
    .join(" ");

  return html`
    <article class="${nsp("quote", classes)}">
      ${
        hasImage
          ? html`
              <div class="${nsp("quote__media")}">
                <div class="${nsp("quote__image")}">
                  ${imageTemplate({
                    imageSrc: imageSrc!,
                    imageAlt
                  })}
                </div>
              </div>
            `
          : ""
      }

      <div class="${nsp("quote__content")}">
        ${
          hasQuote
            ? html`
                <blockquote class="${nsp("quote__blockquote")}">
                  <p>${quoteText ?? ""}</p>
                </blockquote>
              `
            : ""
        }
        ${
          hasAuthor
            ? html`
                <p class="${nsp("quote__author")}">
                  ${
                    authorName
                      ? html`<span class="${nsp("quote__author-name")}">
                          ${authorName}
                        </span>`
                      : ""
                  }
                  ${
                    authorRole
                      ? html`<span class="${nsp("quote__author-role")}">
                          ${authorRole}
                        </span>`
                      : ""
                  }
                </p>
              `
            : ""
        }
        ${
          hasLink
            ? html`
                <div class="${nsp("quote__link-box")}">
                  ${linkTemplate({
                    linkExtraClasses: "quote__link",
                    linkUrl: linkUrl,
                    linkText: linkText!,
                    linkIconBefore: "external-link"
                  })}
                </div>
              `
            : ""
        }
      </div>
    </article>
  `;
}
