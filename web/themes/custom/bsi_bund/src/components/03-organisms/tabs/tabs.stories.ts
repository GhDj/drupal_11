import type { Meta, StoryObj } from "@storybook/html-vite";
import { tabsTemplate } from "./tabs.template";
import { html } from "@globals/index";

const meta: Meta = {
  title: "Organisms/Tabs",
  render: () => tabsTemplate(),

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
};

export default meta;

type Story = StoryObj;

// export const Default: Story = {};

export const Tabs: Story = {
  render: () => html` ${tabsTemplate()} `
};
