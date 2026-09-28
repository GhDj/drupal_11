import type { ArgTypes } from "@storybook/html-vite";
import type { HeadingTemplateArgs } from "./heading.template";

export const headingArgTypes: ArgTypes<HeadingTemplateArgs> = {
  layout: {
    name: "Semantic level",
    table: { category: "Semantics" },
    control: { type: "select" },
    options: [1, 2, 3, 4, 5, 6]
  },

  style: {
    name: "Visual style",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: [1, 2, 3, 4, 5, 6]
  },

  headingText: {
    name: "Heading text",
    table: { category: "Content" },
    control: { type: "text" }
  },

  headingHref: {
    name: "Heading link URL",
    table: { category: "Content" },
    control: { type: "text" }
  }
};
