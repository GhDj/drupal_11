import type { Meta, StoryObj } from "@storybook/html-vite";

import { searchmenuTemplate } from "./searchmenu.template";

const meta: Meta = {
  title: "Molecules/Searchmenu",
  render: searchmenuTemplate,

  // Define, what snapshots should be created
  tags: ["laptop"]
};

export default meta;
type Story = StoryObj;

export const Searchmenu: Story = {};
