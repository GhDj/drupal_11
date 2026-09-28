import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { audioTemplate } from "./audio.template";

export default {
  title: "Organisms/Audio",
  render: audioTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const Audio: Story = {};
