import type { ArgTypes } from "@storybook/html-vite";
import type { PillTemplateArgs, PillVariant } from "./pill.template";

export const pillArgTypes: ArgTypes<PillTemplateArgs> = {
  pillTitle: {
    name: "Label",
    control: "text",
    table: { category: "Content" }
  },

  pillVariant: {
    name: "Variant",
    control: "select",
    options: [
      "default",
      "selected",
      "read-only",
      "disabled",
      "brand"
    ] satisfies PillVariant[],
    table: { category: "Content" }
  },

  pillExtraClasses: {
    name: "Extra Classes",
    control: "text",
    table: { category: "Appearance" }
  }
};
