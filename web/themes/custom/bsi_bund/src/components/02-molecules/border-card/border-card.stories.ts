import type { Meta, StoryObj } from "@storybook/html";
import { html } from "@globals/index";

import {
  borderCardTemplate,
  type BorderCardArgs
} from "./border-card.template";

import { borderCardArgTypes } from "./border-card.controls";

const meta: Meta<BorderCardArgs> = {
  title: "Molecules/BorderCard",
  render: args => borderCardTemplate(args),

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"],

  argTypes: borderCardArgTypes,

  decorators: [
    Story => html`
      <section class="bsi-container">
        <div class="bsi-grid">${Story()}</div>
      </section>
    `
  ]
};

export default meta;
type Story = StoryObj<BorderCardArgs>;

export const BorderCard: Story = {
  args: {
    variant: ["neutral"],

    title: "Das ist ein Border Card Titel",
    date: "12.04.2026",

    linkText: "Mehr erfahren",
    linkUrl: "intern",

    badgeInfo: ["medium", "low"]
  }
};
