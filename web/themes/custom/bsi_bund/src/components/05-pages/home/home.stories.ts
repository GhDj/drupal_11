import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { homeTemplate } from "./home.template";

// Register the story and its metadata
export default {
  title: "Pages/Home",
  render: homeTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for Home page
export const Home: Story = {};
