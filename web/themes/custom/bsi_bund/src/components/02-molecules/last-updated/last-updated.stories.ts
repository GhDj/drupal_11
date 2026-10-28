import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  lastUpdatedTemplate,
  type LastUpdatedTemplateArgs
} from "./last-updated.template";

const meta = {
  title: "Molecules/Last Updated",
  render: lastUpdatedTemplate,
  tags: ["no-snapshot"],
  argTypes: {
    date: { control: "text" },
    dateTime: { control: "text" }
  },
  args: {
    date: "15.03.2026",
    dateTime: "2026-03-15"
  }
} satisfies Meta<LastUpdatedTemplateArgs>;

export default meta;

type Story = StoryObj<typeof meta>;

export const LastUpdated: Story = {};
