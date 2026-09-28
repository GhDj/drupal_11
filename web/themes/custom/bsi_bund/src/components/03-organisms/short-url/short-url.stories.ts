import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { shortURLTemplate } from "./short-url.template";

// Register the story and its metadata
export default {
  title: "Organisms/Short Url",
  render: shortURLTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const ShortUrl: Story = {};
