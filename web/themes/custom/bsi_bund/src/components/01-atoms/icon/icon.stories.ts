import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { iconTemplate, type IconTemplateArgs } from "./icon.template";

// Icon
import { iconNames } from "@base/icons/icon-map";

// Register the story and its metadata
export default {
  title: "Atoms/Icon",
  render: iconTemplate,

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  // Default values for storybook controls
  args: {
    iconName: "arrow-right"
  },

  // Controls shown in the "Controls" panel
  argTypes: {
    iconName: {
      name: "Icon",
      table: { category: "Appearance" },
      control: { type: "select" },
      options: iconNames
    }
  }
} satisfies Meta<IconTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<IconTemplateArgs>;

// Story for an icon
export const Icon: Story = {};
