import { headingTemplate } from "@atoms/heading/heading.template";
import { textTemplate } from "@atoms/text/text.template";
import { linkTemplate } from "@molecules/link/link.template";
import { imageTemplate } from "@atoms/image/image.template";

import { nsp, html } from "@globals/index";

export interface Link {
  label: string;
  url: string;
}

export interface FooterTemplateArgs {
  navigationLinks?: Link[];
  imageSrc?: string;
  imageAlt: string;
  imageDescription?: string;
  socialMediaHeading?: string;
  socialMediaLinks?: Link[];
  serviceLinks?: Link[];
  linkButtonText?: string;
  copyright?: string;
}

export function footerTemplate({
  imageSrc = "/image",
  imageAlt,
  imageDescription = "Description",
  linkButtonText = "Label",
  copyright = "2026",
  socialMediaHeading = "Heading",
  navigationLinks = [
    {
      label: "Label",
      url: "#"
    }
  ],
  socialMediaLinks = [
    {
      label: "Label",
      url: "#"
    }
  ],
  serviceLinks = [
    {
      label: "Label",
      url: "#"
    }
  ]
}: FooterTemplateArgs): string {
  const navigationItems = (navigationLinks ?? [])
    .map(
      link => html`
        <li>
          ${linkTemplate({
            linkText: link.label,
            linkUrl: link.url,
            linkExtraClasses: "footer__link"
          })}
        </li>
      `
    )
    .join("");

  const navigationHTML = html`<nav
    class="${nsp("footer__nav")}"
    aria-label="Footer Navigation"
  >
    <ul class="${nsp("footer__nav-list")}">
      ${navigationItems}
    </ul>
  </nav>`;

  const socialMediaHTML = (socialMediaLinks ?? [])
    .map(link =>
      linkTemplate({
        linkText: link.label,
        linkUrl: link.url,
        linkIconBefore: "external-link",
        linkExtraClasses: "footer__social-link"
      })
    )
    .join("");

  const serviceHTML = (serviceLinks ?? [])
    .map(link =>
      linkTemplate({
        linkText: link.label,
        linkUrl: link.url,
        linkExtraClasses: "footer__service-link"
      })
    )
    .join("");

  const socialMediaHeadingHTML = headingTemplate({
    headingText: socialMediaHeading,
    headingExtraClasses: "footer__heading",
    layout: 3,
    style: 5
  });

  const newsletterHTML = html`<div class="${nsp("footer__newsletter")}">
    ${headingTemplate({
      headingText: "Newsletter",
      headingExtraClasses: "footer__heading",
      layout: 3,
      style: 5
    })}
    ${textTemplate({
      bodytext:
        "Der BSI-Newsletter informiert regelmäßig über Entwicklungen, Warnungen und Maßnahmen zur digitalen Sicherheit."
    })}
    ${linkTemplate({
      linkText: "Zur Newsletter-Anmeldung",
      linkUrl: "#",
      linkIconBefore: "chevron-right",
      linkExtraClasses: "footer__link-button"
    })}
  </div>`;

  const ctaButton = html`
    ${linkTemplate({
      linkIconBefore: "chevron-right",
      linkText: linkButtonText,
      linkExtraClasses: "link--button link--secondary-button"
    })}
  `;

  const certificate = html` <div class="${nsp("footer__image")}">
    <a href="#">
      ${imageTemplate({
        imageSrc: imageSrc,
        imageAlt: imageAlt,
        imageLoading: "lazy",
        imageDescription: imageDescription
      })}
    </a>
  </div>`;

  const scrollTopLink = linkTemplate({
    linkText: "",
    linkUrl: "#",
    linkIconBefore: "to-top",
    additionalLinkAttributes: { "aria-label": "Zum Seitenanfang" },
    linkExtraClasses: "footer__scroll-top"
  });

  return html`
    <footer class="${nsp("footer")}">
      <div class="${nsp("grid")}">
        <div class="${nsp("footer__top")}">
          ${navigationHTML}
          <div class="${nsp("footer__content-wrapper")}">
            <div class="${nsp("footer__content")}">
              ${newsletterHTML} ${certificate}
            </div>

            <div class="${nsp("footer__social")}">
              ${socialMediaHeadingHTML}

              <div class="${nsp("footer__social-links")}">
                ${socialMediaHTML}
              </div>
            </div>
          </div>
        </div>

        <hr />

        <div class="${nsp("footer__bottom")}">
          <span class="${nsp("footer__copyright")}">${copyright}</span>
          <nav class="${nsp("footer__service-nav")}">${serviceHTML}</nav>
          <div class="${nsp("footer__buttons")}">
            ${ctaButton} ${scrollTopLink}
          </div>
        </div>
      </div>
    </footer>
  `;
}
