import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { placeholderTemplate } from "./placeholder.template";

// Register the story and its metadata
export default {
  title: "Base/Placeholder",
  render: placeholderTemplate,

  // Define, what snapshots should be created
  tags: ["no-snapshot"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for placeholder
export const Placeholder: Story = {};
