import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { processTemplate, type ProcessTemplateArgs } from "./process.template";

// Data
import processData from "./process.yml";

// Register the story and its metadata
export default {
  title: "Organisms/Process",
  render: processTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["mobile", "desktop"]
} satisfies Meta<ProcessTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<ProcessTemplateArgs>;

export const Process: Story = {
  args: processData as ProcessTemplateArgs,
  argTypes: {
    showAdditionalColumn: {
      table: { category: "Appearance" },
      control: { type: "boolean" }
    },
    steps: {
      table: { disable: true }
    }
  }
};
