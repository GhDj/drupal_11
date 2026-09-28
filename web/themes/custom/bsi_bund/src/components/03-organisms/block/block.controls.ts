import type { ArgTypes } from "@storybook/html-vite";
import type { BlockTemplateArgs } from "./block.template";

export const blockArgTypes: ArgTypes<BlockTemplateArgs> = {
  blockTitle: {
    name: "Block title",
    table: { category: "Content" },
    control: { type: "text" }
  },

  blockContent: {
    name: "Block content",
    description: "HTML content (e.g. CardGroup, Hero, etc.)",
    table: { category: "Content" },
    control: false
  },

  blockExtraClasses: {
    name: "Extra classes",
    table: { category: "Appearance" },
    control: { type: "text" }
  },

  contentIndented: {
    name: "Indented content",
    description: "Adds additional left offset to the block content",
    table: { category: "Layout" },
    control: { type: "boolean" }
  }
};
