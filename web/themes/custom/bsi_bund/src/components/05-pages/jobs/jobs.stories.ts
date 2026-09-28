import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { jobsTemplate } from "./jobs.template";

// Register the story and its metadata
export default {
  title: "Pages/Jobs",
  render: jobsTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for jobs page
export const Jobs: Story = {};
