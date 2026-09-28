import type { ArgTypes } from "@storybook/html-vite";
import type { BannerTemplateArgs } from "./banner.template";

export const bannerArgTypes: ArgTypes<BannerTemplateArgs> = {
  imageSrc: {
    name: "Show Image",
    table: { category: "Appearance" },
    control: { type: "boolean" },
    mapping: {
      true: "demo-image-16_9.jpg",
      false: ""
    }
  },
  text: {
    name: "Show Text",
    table: { category: "Appearance" },
    control: { type: "boolean" },
    mapping: {
      true: "Nutzen Sie das BSI-Portal für sichere Meldungen, Anträge und geschützte Fachverfahren.",
      false: ""
    }
  }
};
