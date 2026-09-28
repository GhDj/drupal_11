import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { bannerTemplate, type BannerTemplateArgs } from "./banner.template";

// Controls
import { bannerArgTypes } from "./banner.controls";

// Register the story and its metadata
export default {
  title: "Organisms/Banner",
  render: bannerTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"],

  // Default values for storybook controls
  args: {
    imageSrc: true as unknown as string,
    text: true as unknown as string
  },

  // Controls shown in the "Controls" panel
  argTypes: bannerArgTypes
} satisfies Meta<BannerTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<BannerTemplateArgs>;

// Story for the default banner
export const Banner: Story = {};
