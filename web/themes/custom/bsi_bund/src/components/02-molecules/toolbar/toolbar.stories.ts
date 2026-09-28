import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { toolbarTemplate, type ToolbarTemplateArgs } from "./toolbar.template";

// Register the story and its metadata
export default {
  title: "Molecules/Toolbar",
  render: toolbarTemplate,

  // Snapshots are covered in src/components/05-pages
  tags: ["no-snapshot"]
} satisfies Meta<ToolbarTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<ToolbarTemplateArgs>;

export const Toolbar: Story = {};
