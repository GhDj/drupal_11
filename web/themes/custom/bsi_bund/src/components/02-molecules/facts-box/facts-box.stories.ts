import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

import { defaultFactsBoxItems } from "./facts-box.constants";
import { factsBoxArgTypes } from "./facts-box.controls";
import {
  factsBoxTemplate,
  type FactsBoxTemplateArgs
} from "./facts-box.template";

const meta: Meta<FactsBoxTemplateArgs> = {
  title: "Molecules/Facts Box",
  render: args => factsBoxTemplate(args),

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"],

  argTypes: factsBoxArgTypes,

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;
type Story = StoryObj<FactsBoxTemplateArgs>;

export const FactsBox: Story = {
  args: {
    iconName: "info-circle",
    title: "Informationen zum Ereignis",
    items: defaultFactsBoxItems,
    additionalAttributes: {
      "data-theme": "blue-900"
    }
  }
};
