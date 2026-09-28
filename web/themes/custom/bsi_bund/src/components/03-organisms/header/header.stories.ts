import type { Meta, StoryObj } from "@storybook/html";

import { headerTemplate } from "./header.template";

const meta: Meta = {
  title: "Organisms/Header",
  render: headerTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
};

export default meta;
type Story = StoryObj;

export const Header: Story = {};
