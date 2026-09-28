import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  dropdownTemplate,
  type DropdownTemplateArgs
} from "./dropdown.template";

// Register the story and its metadata
export default {
  title: "Molecules/Dropdown",
  render: dropdownTemplate,

  // Define, what snapshots should be created
  tags: ["mobile"]
} satisfies Meta<DropdownTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<DropdownTemplateArgs>;

// Story for the default dropdown
export const Dropdown: Story = {};
