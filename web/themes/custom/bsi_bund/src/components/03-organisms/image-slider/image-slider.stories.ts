import type { Meta, StoryObj } from "@storybook/html";

import { imageSliderTemplate } from "./image-slider.template";

const meta: Meta = {
  title: "Organisms/Image Slider",
  render: imageSliderTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
};

export default meta;
type Story = StoryObj;

export const ImageSlider: Story = {};
