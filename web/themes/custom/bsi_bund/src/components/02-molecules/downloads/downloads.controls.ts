import type { ArgTypes } from "@storybook/html-vite";
import type { DownloadsTemplateArgs } from "./downloads.template";

export const downloadsArgTypes: ArgTypes<DownloadsTemplateArgs> = {
  blockTitle: {
    name: "Block title",
    table: { category: "Content" },
    control: { type: "text" }
  },
  items: {
    name: "Items",
    table: { category: "Content" },
    control: { type: "object" }
  },
  contentIndented: {
    name: "Content indented",
    table: { category: "Layout" },
    control: { type: "boolean" }
  },
  extraClasses: {
    control: "text",
    table: { category: "Appearance" }
  },
  blockExtraClasses: {
    control: "text",
    table: { category: "Appearance" }
  }
};
