import type { Meta, StoryObj } from "@storybook/html-vite";

// Controls
import { paginationArgTypes } from "./pagination.controls";

// Components
import {
  paginationTemplate,
  type PaginationTemplateArgs
} from "./pagination.template";

// Register the story and its metadata
export default {
  title: "Molecules/Pagination",
  render: paginationTemplate,

  // Define, what snapshots should be created
  tags: ["mobile"],

  // Default values for storybook controls
  args: {
    paginationCurrentPage: 1,
    paginationTotalPages: 20,
    paginationBaseUrl: "#"
  },

  // Controls shown in the "Controls" panel
  argTypes: paginationArgTypes
} satisfies Meta<PaginationTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<PaginationTemplateArgs>;

// Story for pagination
export const Pagination: Story = {};
