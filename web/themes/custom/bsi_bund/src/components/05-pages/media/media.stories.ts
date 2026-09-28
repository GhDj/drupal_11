import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { mediaTemplate } from "./media.template";

// Register the story and its metadata
export default {
  title: "Pages/Media",
  render: mediaTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for media page
export const Media: Story = {};
