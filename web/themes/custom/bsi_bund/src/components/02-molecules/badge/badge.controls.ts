import type { ArgTypes } from "@storybook/html-vite";
import type { BadgeTemplateArgs } from "./badge.template";

export const badgeArgTypes: ArgTypes<BadgeTemplateArgs> = {
  badgeTitle: {
    name: "Label",
    control: "text",
    table: { category: "Content" }
  },

  badgeTheme: {
    name: "Theme",
    control: "select",
    options: ["none", "low", "medium", "high", "critical", "status", "date"],
    table: { category: "Appearance" }
  },

  badgeExtraClasses: {
    name: "Extra Classes",
    control: "text",
    table: { category: "Appearance" }
  }
};
