import type { ArgTypes } from "@storybook/html-vite";
import type { FactsAndFiguresGroupTemplateArgs } from "./facts-group.template";

export const factsAndFiguresGroupArgTypes: ArgTypes<FactsAndFiguresGroupTemplateArgs> =
  {
    factsAndFiguresGroupItems: {
      control: false
    },
    factsAndFiguresGroupExtraClasses: {
      control: false
    }
  };
