import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { headingTemplate, type HeadingTemplateArgs } from "./heading.template";
import { headingArgTypes } from "./heading.controls";

// Register the story and its metadata
export default {
  title: "Atoms/Heading",
  render: headingTemplate,

  // Define, what snapshots should be created
  tags: ["mobile"],

  // Default values for storybook controls
  args: {
    layout: 2,
    style: 1,
    headingText: "This is a basic heading"
  },

  // Controls shown in the "Controls" panel
  argTypes: headingArgTypes
} satisfies Meta<HeadingTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<HeadingTemplateArgs>;

// Story for basic heading
export const Heading: Story = {};
