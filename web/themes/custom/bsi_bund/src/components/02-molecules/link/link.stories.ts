import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { linkTemplate, type LinkTemplateArgs } from "./link.template";

// Controls
import { linkArgTypes } from "./link.controls";

// Register the story and its metadata
export default {
  title: "Molecules/Link",
  render: linkTemplate,

  // Define, what snapshots should be created
  tags: ["mobile"],

  // Default values for controls
  args: {
    linkIconBefore: "",
    linkIconAfter: ""
  },

  // Controls shown in the "Controls" panel
  argTypes: linkArgTypes
} satisfies Meta<LinkTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<LinkTemplateArgs>;

// Story for basic link
export const Basic: Story = {
  args: {
    linkText: "Internal Link"
  }
};

// Story for icon as link
export const OnlyIcon: Story = {
  args: {
    linkIconBefore: "arrow-right",
    linkAriaLabel: "Pfeil rechts"
  }
};

// Story to showcase the button variants of the link-component
export const ButtonStyles: Story = {
  argTypes: {
    linkExtraClasses: {
      name: "Button variant",
      table: { category: "Appearance", disable: false },
      control: {
        type: "select",
        labels: {
          "link--button link--primary-button": "Primary",
          "link--button link--secondary-button": "Secondary",
          "link--button link--tertiary-button": "Tertiary"
        }
      },
      options: [
        "link--button link--primary-button",
        "link--button link--secondary-button",
        "link--button link--tertiary-button"
      ]
    }
  },
  args: {
    linkText: "Link als Button",
    linkIconBefore: "chevron-right",
    linkExtraClasses: "link--button link--primary-button"
  }
};

// CTA link
export const CTA: Story = {
  args: {
    linkText: "CTA Link",
    linkIconBefore: "chevron-right",
    linkExtraClasses: "link--button link--secondary-button link--cta"
  }
};
