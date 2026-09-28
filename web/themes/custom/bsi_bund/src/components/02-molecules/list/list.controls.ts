import type { ArgTypes } from "@storybook/html-vite";

import { listVariants } from "./list.constants";
import type { ListTemplateArgs } from "./list.template";

export const listArgTypes: ArgTypes<ListTemplateArgs> = {
  items: {
    name: "Items",
    table: { category: "Content" },
    control: { type: "object" }
  },
  variant: {
    name: "Variant",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: listVariants
  },
  extraClasses: {
    control: "text",
    table: { category: "Appearance" }
  }
};
