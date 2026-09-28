import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { contactFormTemplate } from "./contactForm.template";
import { contactSuccessTemplate } from "./contactSuccess.template";

// Register the story and its metadata
export default {
  title: "Pages/Contact",

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for Contact Form Page
export const ContactForm: Story = {
  render: contactFormTemplate
};

// Story for Contact Form Page
export const ContactSuccess: Story = {
  render: contactSuccessTemplate
};
