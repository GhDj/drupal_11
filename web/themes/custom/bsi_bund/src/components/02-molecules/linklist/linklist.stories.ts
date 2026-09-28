import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  linkListTemplate,
  type LinkListTemplateArgs
} from "./linklist.template";

// Register the story and its metadata
export default {
  title: "Molecules/Link List",
  render: linkListTemplate,

  // Default values for controls
  args: {
    title: "Vollständige Berichte"
  },

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta<LinkListTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<LinkListTemplateArgs>;

// Story for basic heading
export const LinkList: Story = {};
