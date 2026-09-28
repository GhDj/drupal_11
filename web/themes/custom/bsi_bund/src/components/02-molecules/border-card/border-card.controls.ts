import type { ArgTypes } from "@storybook/html";
import type { BorderCardArgs } from "./border-card.template";

export const borderCardArgTypes: ArgTypes<BorderCardArgs> = {
  colorVariant: {
    name: "Variant",
    control: { type: "select" },
    options: ["neutral", "high", "critical", "info", "success", "transparent"],
    table: { category: "Appearance" }
  },

  small: {
    name: "Small",
    control: { type: "boolean" },
    table: { category: "Appearance" }
  },

  extraClasses: {
    control: "text",
    table: { category: "Appearance" }
  },

  title: {
    control: "text",
    table: { category: "Content" }
  },

  date: {
    control: "text",
    table: { category: "Content" }
  },

  linkText: {
    control: "text",
    table: { category: "Content" }
  },

  linkUrl: {
    control: "text",
    table: { category: "Content" }
  },

  badgeInfo: {
    name: "Badge (Info)",
    control: { type: "multi-select" },
    options: ["low", "none", "medium", "high", "higher", "critical", "status"],
    table: { category: "Content" }
  }
};
