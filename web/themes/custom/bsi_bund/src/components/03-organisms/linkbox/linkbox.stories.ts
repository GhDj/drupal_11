import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  linkBoxTemplate,
  type LinkBoxTemplateArgs
} from "./linkbox.template.ts";

export default {
  title: "Organisms/Link Box",
  render: linkBoxTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta<LinkBoxTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<LinkBoxTemplateArgs>;

// Story for basic heading
export const LinkBox: Story = {};
