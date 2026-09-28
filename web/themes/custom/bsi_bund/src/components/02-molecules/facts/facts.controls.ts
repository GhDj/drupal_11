import type { ArgTypes } from "@storybook/html-vite";
import type { FactsAndFiguresTemplateArgs } from "./facts.template";

export const factsAndFiguresArgTypes: ArgTypes<FactsAndFiguresTemplateArgs> = {
  figurePrefix: {
    control: "text",
    table: { category: "Content" }
  },
  figureValue: {
    control: "text",
    table: { category: "Content" }
  },
  figureSuffix: {
    control: "text",
    table: { category: "Content" }
  },
  text: {
    control: "text",
    table: { category: "Content" }
  },
  extraClasses: {
    control: "text",
    table: { category: "Appearance" }
  }
};
