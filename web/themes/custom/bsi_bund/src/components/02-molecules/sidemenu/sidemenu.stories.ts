import type { Meta, StoryObj } from "@storybook/html-vite";

import { sidemenuTemplate } from "./sidemenu.template";

const meta: Meta = {
  title: "Molecules/Sidemenu",
  render: () => sidemenuTemplate(),

  tags: ["laptop"]
};

export default meta;

type Story = StoryObj;

export const Sidemenu: Story = {};
