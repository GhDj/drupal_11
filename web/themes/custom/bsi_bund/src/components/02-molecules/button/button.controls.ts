import type { ArgTypes } from "@storybook/html-vite";
import type { ButtonTemplateArgs } from "./button.template";
import { iconNames } from "@base/icons/icon-map";
import {
  buttonModes,
  buttonStates,
  buttonVisualVariants,
  controlButtonSizes
} from "./button.constants";

// Controls configuration
export const buttonArgTypes: ArgTypes<ButtonTemplateArgs> = {
  buttonVariant: {
    name: "Button variant",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: buttonVisualVariants
  },
  buttonSize: {
    name: "Button size",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: controlButtonSizes
  },
  buttonMode: {
    name: "Button mode",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: buttonModes
  },
  buttonState: {
    name: "Button state",
    table: { category: "Appearance" },
    control: { type: "select" },
    options: buttonStates
  },
  buttonIconBefore: {
    name: "Icon before text",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: { "": "None" }
    },
    options: ["", ...iconNames]
  },
  buttonIconAfter: {
    name: "Icon after text",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: { "": "None" }
    },
    options: ["", ...iconNames]
  },
  buttonText: {
    name: "Button text",
    table: { category: "Content" },
    control: { type: "text" }
  },
  buttonAriaLabel: {
    table: { disable: true }
  },
  buttonType: { table: { disable: true } }
};
