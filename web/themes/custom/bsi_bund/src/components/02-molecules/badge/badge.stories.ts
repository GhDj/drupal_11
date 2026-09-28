import type { Meta, StoryObj } from "@storybook/html-vite";

import { badgeTemplate, type BadgeTemplateArgs } from "./badge.template";
import { badgeArgTypes } from "./badge.controls";

export default {
  title: "Molecules/Badge",
  render: badgeTemplate,

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  argTypes: badgeArgTypes,

  args: {
    badgeTitle: "Mittel",
    badgeTheme: "medium"
  }
} satisfies Meta<BadgeTemplateArgs>;

type Story = StoryObj<BadgeTemplateArgs>;

export const Badge: Story = {};
