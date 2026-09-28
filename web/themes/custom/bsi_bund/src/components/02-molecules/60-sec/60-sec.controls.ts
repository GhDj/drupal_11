import type { ArgTypes } from "@storybook/html-vite";
import type { SixtySecTemplateArgs } from "./60-sec.template";

export const sixtySecArgTypes: ArgTypes<SixtySecTemplateArgs> = {
  idPrefix: {
    name: "ID prefix",
    table: { category: "Content" },
    control: { type: "text" }
  },
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
  extraClasses: {
    control: "text",
    table: { category: "Appearance" }
  },
  blockExtraClasses: {
    control: "text",
    table: { category: "Appearance" }
  }
};
