import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  buttonSlideTemplate,
  type ButtonSlideTemplateArgs
} from "./button-slide.template";
import { initButtonSlides } from "./button-slide";
import { buttonSlideStates } from "./button-slide.constants";

export default {
  title: "Molecules/Button Slide",

  // Define, what snapshots should be created
  tags: ["mobile"]
} satisfies Meta<ButtonSlideTemplateArgs>;

type Story = StoryObj<ButtonSlideTemplateArgs>;

export const ButtonSlide: Story = {
  args: {
    buttonSlideText: "slide to action",
    buttonSlideSuccessText: "action successful",
    buttonSlideState: "default",
    buttonSlideThreshold: 0.85
  },
  argTypes: {
    buttonSlideText: {
      name: "Text",
      table: { category: "Content" },
      control: { type: "text" }
    },
    buttonSlideSuccessText: {
      name: "Success text",
      table: { category: "Content" },
      control: { type: "text" }
    },
    buttonSlideState: {
      name: "State",
      table: { category: "Appearance" },
      control: { type: "select" },
      options: buttonSlideStates
    },
    buttonSlideThreshold: {
      name: "Threshold",
      table: { category: "Behavior" },
      control: { type: "number", min: 0.5, max: 1, step: 0.05 }
    }
  },
  render: args => {
    const root = document.createElement("div");

    root.style.padding = "32px";
    root.insertAdjacentHTML("beforeend", buttonSlideTemplate(args));
    initButtonSlides(root);

    return root;
  }
};
