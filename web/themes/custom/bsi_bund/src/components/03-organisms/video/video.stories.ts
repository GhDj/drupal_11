import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { videoTemplate } from "./video.template";

export default {
  title: "Organisms/Video",
  render: videoTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const Video: Story = {};
