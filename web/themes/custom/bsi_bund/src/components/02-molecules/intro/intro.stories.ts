import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { introTemplate } from "./intro.template";

export default {
  title: "Molecules/Intro",
  render: introTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const Intro: Story = {};
