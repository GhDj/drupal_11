import type { ArgTypes } from "@storybook/html-vite";
import type { OpenerTemplateArgs } from "./opener.template";
import { openerVariants } from "./opener.constants";

export const openerArgTypes: ArgTypes<OpenerTemplateArgs> = {
  variant: {
    name: "Variant",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: openerVariants
  },
  showBadges: {
    name: "Show Badges ",
    table: { category: "Content" },
    control: { type: "boolean" }
  }
};
