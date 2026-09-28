import { html, nsp } from "@globals/index";
import { headingTemplate } from "@atoms/heading/heading.template";
import { iconTemplate } from "@atoms/icon/icon.template";
import { imageTemplate } from "@atoms/image/image.template";
import { linkTemplate } from "@molecules/link/link.template";

import { defaultDownloadsItems } from "./downloads.constants";

export interface DownloadsAction {
  text: string;
  url?: string;
  addOnText?: string;
  iconName?: string;
}

export interface DownloadsMedia {
  type?: "icon" | "thumbnail";
  iconName?: string;
  iconTitle?: string;
  imageSrc?: string;
  imageAlt?: string;
}

export interface DownloadsItem {
  title?: string;
  text?: string;
  media?: DownloadsMedia;
  downloadLink?: DownloadsAction;
  orderLink?: DownloadsAction;
}

export interface DownloadsTemplateArgs {
  blockTitle?: string;
  items?: DownloadsItem[];
  contentIndented?: boolean;
  blockExtraClasses?: string;
  extraClasses?: string;
}

function downloadsMediaTemplate(media: DownloadsMedia = {}): string {
  const type = media.type ?? (media.imageSrc ? "thumbnail" : "icon");

  if (type === "thumbnail" && media.imageSrc) {
    return html`
      <div class="${nsp("downloads__media-frame")}">
        ${imageTemplate({
          imageSrc: media.imageSrc,
          imageAlt: media.imageAlt ?? "",
          imageExtraClasses: "downloads__thumbnail"
        })}
      </div>
    `;
  }

  return html`
    <div class="${nsp("downloads__media-frame")}">
      ${iconTemplate({
        iconName: media.iconName ?? "download",
        iconDecorative: !media.iconTitle,
        iconTitle: media.iconTitle ?? null,
        iconExtraClasses: "downloads__media-icon"
      })}
    </div>
  `;
}

function downloadsItemTemplate(item: DownloadsItem): string {
  const hasTitle = Boolean(item.title);
  const hasText = Boolean(item.text);
  const hasDownloadLink = Boolean(item.downloadLink?.text);
  const hasOrderLink = Boolean(item.orderLink?.text);
  const hasActions = hasDownloadLink || hasOrderLink;

  return html`
    <article class="${nsp("downloads__item")}">
      <div class="${nsp("downloads__media")}">
        ${downloadsMediaTemplate(item.media)}
      </div>

      <div class="${nsp("downloads__body")}">
        ${
          hasTitle
            ? headingTemplate({
                headingText: item.title ?? "",
                layout: 3,
                style: 5,
                headingExtraClasses: "downloads__title"
              })
            : ""
        }
        ${
          hasText
            ? html`<p class="${nsp("downloads__text")}">${item.text}</p>`
            : ""
        }
        ${
          hasActions
            ? html`
                <div class="${nsp("downloads__actions")}">
                  ${
                    hasDownloadLink
                      ? linkTemplate({
                          linkUrl: item.downloadLink!.url ?? "#",
                          linkText: item.downloadLink!.text,
                          linkAddOnText: item.downloadLink!.addOnText ?? "",
                          linkIconBefore: item.downloadLink!.iconName ?? null
                        })
                      : ""
                  }
                  ${
                    hasOrderLink
                      ? linkTemplate({
                          linkUrl: item.orderLink!.url ?? "#",
                          linkText: item.orderLink!.text,
                          linkAddOnText: item.orderLink!.addOnText ?? "",
                          linkIconBefore: item.orderLink!.iconName ?? null
                        })
                      : ""
                  }
                </div>
              `
            : ""
        }
      </div>
    </article>
  `;
}

export function downloadsTemplate({
  blockTitle = "Downloads",
  items = defaultDownloadsItems,
  contentIndented = true,
  blockExtraClasses = "",
  extraClasses = ""
}: DownloadsTemplateArgs = {}): string {
  const filteredItems = (items ?? []).filter(
    item =>
      Boolean(item.title) ||
      Boolean(item.text) ||
      Boolean(item.media) ||
      Boolean(item.downloadLink?.text) ||
      Boolean(item.orderLink?.text)
  );
  const hasBlockTitle = Boolean(blockTitle);

  if (!hasBlockTitle && !filteredItems.length) {
    return "";
  }

  return html`
    <div class="${nsp("block", "grid", blockExtraClasses)}">
      ${
        hasBlockTitle
          ? headingTemplate({
              headingText: blockTitle,
              layout: 2,
              style: 2,
              headingExtraClasses: "block__title"
            })
          : ""
      }

      <div
        class="${nsp(
          "block__content",
          contentIndented ? "block__content--indented" : ""
        )}"
      >
        <div class="${nsp("downloads", extraClasses)}">
          ${filteredItems.map(downloadsItemTemplate).join("")}
        </div>
      </div>
    </div>
  `;
}
