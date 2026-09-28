import type { ArgTypes } from "@storybook/html-vite";
import type { LinkTemplateArgs } from "./link.template";
import { iconNames } from "@base/icons/icon-map";

// Controls configuration
export const linkArgTypes: ArgTypes<LinkTemplateArgs> = {
  linkIconBefore: {
    name: "Icon before link",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: { "": "None" }
    },
    options: ["", ...iconNames]
  },
  linkIconAfter: {
    name: "Icon after link",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: { "": "None" }
    },
    options: ["", ...iconNames]
  },
  linkText: {
    name: "Linktext",
    table: { category: "Content" },
    control: { type: "text" }
  },
  linkAddOnText: {
    name: "Add-on text",
    table: { category: "Content" },
    control: { type: "text" }
  },
  linkExtraClasses: {
    table: { disable: true }
  }
};
