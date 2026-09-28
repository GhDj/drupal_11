import type { ArgTypes } from "@storybook/html-vite";

import type { FactsBoxTemplateArgs } from "./facts-box.template";

export const factsBoxArgTypes: ArgTypes<FactsBoxTemplateArgs> = {
  additionalAttributes: {
    name: "Theme",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: {
        "{}": "None",
        '{"data-theme":"blue-100"}': "Blue-100",
        '{"data-theme":"blue-900"}': "Blue-900"
      }
    },
    options: ["{}", '{"data-theme":"blue-100"}', '{"data-theme":"blue-900"}'],
    mapping: {
      "{}": {},
      '{"data-theme":"blue-100"}': { "data-theme": "blue-100" },
      '{"data-theme":"blue-900"}': { "data-theme": "blue-900" }
    }
  },
  items: {
    name: "Items",
    table: { disable: true }
  },
  iconName: {
    name: "Icon",
    table: { disable: true }
  },
  title: {
    name: "Title",
    table: { disable: true }
  }
};
