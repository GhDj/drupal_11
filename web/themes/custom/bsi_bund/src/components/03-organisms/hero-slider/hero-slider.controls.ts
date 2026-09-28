import type { ArgTypes } from "@storybook/html-vite";

import type { HeroSliderTemplateArgs } from "./hero-slider.template";

export const heroSliderArgTypes: ArgTypes<HeroSliderTemplateArgs> = {
  ariaLabel: {
    control: "text",
    description: "Accessible label for the slider region."
  },
  items: {
    control: false
  }
};
