import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  heroSliderTemplate,
  type HeroSliderTemplateArgs
} from "./hero-slider.template";

import HeroSlider from "./hero-slider";

import {
  heroSliderDefaultItems,
  heroSliderDefaultOverlay
} from "./hero-slider.constants";

const meta: Meta<HeroSliderTemplateArgs> = {
  title: "Organisms/Hero Slider",

  render: args => {
    const markup = heroSliderTemplate(args);

    // init nach render
    setTimeout(() => {
      document.querySelectorAll("[data-bsi-hero-slider]").forEach(el => {
        new HeroSlider(el as HTMLElement);
      });
    });

    return markup;
  },

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"],

  args: {
    ariaLabel: "Aktuelle Themen",
    items: heroSliderDefaultItems
  }
};

export default meta;

type Story = StoryObj<HeroSliderTemplateArgs>;

export const Default: Story = {
  args: {
    items: heroSliderDefaultItems,
    overlay: heroSliderDefaultOverlay
  }
};

export const WithoutOverlay: Story = {
  args: {
    items: heroSliderDefaultItems
  }
};
