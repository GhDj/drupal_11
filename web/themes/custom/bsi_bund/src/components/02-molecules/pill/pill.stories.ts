import type { Meta, StoryObj } from "@storybook/html-vite";

import { pillTemplate, type PillTemplateArgs } from "./pill.template";
import { pillArgTypes } from "./pill.controls";

export default {
  title: "Molecules/Pill",
  render: pillTemplate,

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  argTypes: pillArgTypes,

  args: {
    pillTitle: "Value",
    pillVariant: "default"
  }
} satisfies Meta<PillTemplateArgs>;

type Story = StoryObj<PillTemplateArgs>;

export const Pill: Story = {};
