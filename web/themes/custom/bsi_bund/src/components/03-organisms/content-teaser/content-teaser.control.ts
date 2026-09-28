import type { ArgTypes } from "@storybook/html-vite";
import type { ContentTeaserTemplateArgs } from "./content-teaser.template";

// Controls configuration
export const contentTeaserArgTypes: ArgTypes<ContentTeaserTemplateArgs> = {
  imagePosition: {
    name: "Image position",
    table: { category: "Appearance" },
    if: { arg: "image", neq: "" },
    control: {
      type: "select",
      labels: {
        "image-left": "Image Left",
        "image-right": "Image Right"
      }
    },
    options: ["image-left", "image-right"]
  }
};
