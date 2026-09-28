/**
 * Renders an image with optional caption, description and copyright.
 */

import { nsp, html } from "@globals/index";
import { copyrightTemplate } from "@atoms/copyright/copyright.template";

export type ImageLoading = "lazy" | "eager";

export interface ImageTemplateArgs {
  imageSrc?: string;
  imageAlt?: string;

  imageLoading?: ImageLoading;
  imageExtraClasses?: string;

  imageCaption?: string;
  imageDescription?: string;
  imageCopyright?: string;
}

export function imageTemplate({
  imageSrc = "demo-image-16_9.jpg",
  imageAlt = "Demo image",

  imageLoading = "lazy",
  imageExtraClasses = "",

  imageCaption,
  imageDescription,
  imageCopyright
}: ImageTemplateArgs = {}): string {
  const hasCaption = Boolean(imageCaption) || Boolean(imageDescription);
  const hasCopyright = Boolean(imageCopyright);

  const image = html`
    <img
      src="${imageSrc}"
      alt="${imageAlt}"
      class="${nsp("image__image")}"
      loading="${imageLoading}"
    />
  `;

  const captionHtml = hasCaption
    ? `<div class="${nsp("image__caption-text")}"><p>${imageCaption ?? ""}</p></div>`
    : "";

  const copyrightHtml = html`
    ${
      hasCopyright && imageCopyright
        ? copyrightTemplate({
            copyrightText: imageCopyright,
            copyrightExtraClasses: "image__copyright"
          })
        : ""
    }
  `;

  const figcaption =
    hasCaption || hasCopyright
      ? `
          <figcaption class="${nsp("image__caption")}">
            ${copyrightHtml}
            ${captionHtml}
          </figcaption>
        `
      : "";

  return html`
    <figure class="${nsp("image", imageExtraClasses)}">
      ${image} ${figcaption}
    </figure>
  `;
}
