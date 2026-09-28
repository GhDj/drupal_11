import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { reportTemplate } from "./report.template";

// Register the story and its metadata
export default {
  title: "Pages/Report",
  render: reportTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for report page
export const Report: Story = {};
