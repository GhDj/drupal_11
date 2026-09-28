import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  pageGridTemplate,
  type PageGridTemplateArgs
} from "./page-grid.template";

// Controls
import { pageGridArgTypes } from "./page-grid.controls";

// Register the story and its metadata
export default {
  title: "Templates/Page Grid",
  decorators: [
    story => {
      const renderedStory = story() as string;
      return `<div class="page-grid-decorator">${renderedStory}</div>`;
    }
  ],
  render: pageGridTemplate,

  // Define, what snapshots should be created
  tags: ["all"],

  // Default values for controls
  args: {
    showStage: true,
    showBreadcrumb: true,
    showAnchors: true
  },

  // Controls shown in the "Controls" panel
  argTypes: pageGridArgTypes
} satisfies Meta<PageGridTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for pageGrid page
export const pageGrid: Story = {};
