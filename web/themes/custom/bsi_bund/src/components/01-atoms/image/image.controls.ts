import type { ArgTypes } from "@storybook/html-vite";
import type { ImageTemplateArgs } from "./image.template";

export const imageArgTypes: ArgTypes<ImageTemplateArgs> = {
  imageSrc: {
    control: "text",
    table: { category: "Media" }
  },

  imageAlt: {
    control: "text",
    table: { category: "Accessibility" }
  },

  imageLoading: {
    control: "select",
    options: ["lazy", "eager"],
    table: { category: "Behavior" }
  },

  imageCaption: {
    control: "text",
    table: { category: "Content" }
  },

  imageDescription: {
    control: "text",
    table: { category: "Content" }
  },

  imageCopyright: {
    control: "text",
    table: { category: "Content" }
  },

  imageExtraClasses: {
    control: "text",
    table: { category: "Appearance" }
  }
};
