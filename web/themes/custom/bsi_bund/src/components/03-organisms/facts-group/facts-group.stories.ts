import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  factsAndFiguresGroupTemplate,
  type FactsAndFiguresGroupTemplateArgs
} from "./facts-group.template";
import { initFactsAndFigures } from "@molecules/facts/facts";
import { factsAndFiguresGroupArgTypes } from "./facts-group.controls";

const meta: Meta<FactsAndFiguresGroupTemplateArgs> = {
  title: "Organisms/Facts Group",
  render: args => {
    const root = document.createElement("div");

    root.innerHTML = `<div class="bsi-grid">${factsAndFiguresGroupTemplate(args)}</div>`;

    requestAnimationFrame(() => {
      initFactsAndFigures(root);
    });

    return root;
  },

  // Define, what snapshots should be created
  tags: ["no-snapshot"],
  argTypes: factsAndFiguresGroupArgTypes
};

export default meta;
type Story = StoryObj<FactsAndFiguresGroupTemplateArgs>;

export const FactsGroup: Story = {};
