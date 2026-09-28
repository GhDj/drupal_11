import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { formTemplate, type FormTemplateArgs } from "./form.template";

// Data
import contactFormData from "./form--contact.html?raw";

// Register the story and its metadata
export default {
  title: "Organisms/Form",
  render: formTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
} satisfies Meta<FormTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<FormTemplateArgs>;

export const Form: Story = {
  args: {
    formContent: contactFormData
  }
};
