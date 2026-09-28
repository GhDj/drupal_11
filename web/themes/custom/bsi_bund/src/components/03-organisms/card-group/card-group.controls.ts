import type { ArgTypes } from "@storybook/html-vite";

import type { CardGroupTemplateArgs } from "./card-group.template";

export const cardGroupArgTypes: ArgTypes<CardGroupTemplateArgs> = {
  items: {
    control: false
  },
  extraClasses: {
    control: false
  }
};
