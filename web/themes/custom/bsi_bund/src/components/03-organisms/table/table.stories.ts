import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

import { demoTableMarkup } from "./table.constants";

const meta: Meta = {
  title: "Organisms/Table",

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;

type Story = StoryObj;

export const Default: Story = {
  render: () => html`${demoTableMarkup} `
};
