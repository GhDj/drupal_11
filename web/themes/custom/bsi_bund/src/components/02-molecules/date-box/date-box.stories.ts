import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { dateBoxTemplate, type DateBoxTemplateArgs } from "./date-box.template";
import { dateBoxArgTypes } from "./date-box.controls";

// Register the story and its metadata
const meta: Meta<DateBoxTemplateArgs> = {
  title: "Molecules/Date Box",
  render: dateBoxTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["mobile"],

  // Default values for storybook controls
  args: {
    dates: [
      { year: "2026", day: "28", month: "September", weekday: "Donnerstag" }
    ]
  },

  // Controls shown in the "Controls" panel
  argTypes: dateBoxArgTypes
};

export default meta;

// Reusable Story type based on the template args
type Story = StoryObj<DateBoxTemplateArgs>;

// Story for basic date box
export const dateBox: Story = {};
