import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { metaNavTemplate } from "./metanav.template";

// Register the story and its metadata
export default {
  title: "Molecules/MetaNav",
  render: metaNavTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const MetaNav: Story = {};
