import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  breadcrumbTemplate,
  type BreadcrumbTemplateArgs
} from "./breadcrumb.template";

// Register the story and its metadata
const meta = {
  title: "Molecules/Breadcrumb",
  render: breadcrumbTemplate,

  // Define what snapshots should be created
  tags: ["mobile", "desktop"]
} satisfies Meta<BreadcrumbTemplateArgs>;

export default meta;

// Reusable Story type based on the template args
type Story = StoryObj<typeof meta>;

// Story for breadcrumb
export const Breadcrumb: Story = {};
