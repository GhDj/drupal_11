import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { searchTemplate } from "./search.template";

// Register the story and its metadata
export default {
  title: "Pages/Search",
  render: searchTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for search page
export const search: Story = {};
