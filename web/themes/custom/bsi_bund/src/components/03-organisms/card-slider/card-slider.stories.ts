import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  cardSliderTemplate,
  type CardSliderTemplateArgs
} from "./card-slider.template";

import { items } from "./card-slider.constants";

const meta: Meta<CardSliderTemplateArgs> = {
  title: "Organisms/Card Slider",
  render: cardSliderTemplate,
  args: {
    items: items
  },

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
};

export default meta;

type Story = StoryObj<CardSliderTemplateArgs>;

export const CardSlider: Story = {};
