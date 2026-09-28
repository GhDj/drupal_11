import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

import { defaultListItems } from "./list.constants";
import { listArgTypes } from "./list.controls";
import { listTemplate, type ListTemplateArgs } from "./list.template";

const meta: Meta<ListTemplateArgs> = {
  title: "Molecules/List",
  render: args => listTemplate(args),
  argTypes: listArgTypes,

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;
type Story = StoryObj<ListTemplateArgs>;

export const Default: Story = {
  args: {
    items: defaultListItems,
    variant: "bullet"
  }
};
