import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { searchbarTemplate } from "./searchbar.template";

// Register the story and its metadata
export default {
  title: "Molecules/Searchbar",
  render: searchbarTemplate,

  // Define, what snapshots should be created
  tags: ["laptop"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for basic heading
export const Searchbar: Story = {};
