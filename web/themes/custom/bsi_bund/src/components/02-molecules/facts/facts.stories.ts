import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  factsAndFiguresTemplate,
  type FactsAndFiguresTemplateArgs
} from "./facts.template";
import { initFactsAndFigures } from "./facts";
import { factsAndFiguresArgTypes } from "./facts.controls";

const meta: Meta<FactsAndFiguresTemplateArgs> = {
  title: "Molecules/Facts",
  render: args => {
    const root = document.createElement("div");

    root.innerHTML = `
      <section class="bsi-container">
        <div class="bsi-grid">${factsAndFiguresTemplate(args)}</div>
      </section>
    `;

    requestAnimationFrame(() => {
      initFactsAndFigures(root);
    });

    return root;
  },

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"],

  argTypes: factsAndFiguresArgTypes
};

export default meta;
type Story = StoryObj<FactsAndFiguresTemplateArgs>;

export const Facts: Story = {
  args: {
    figurePrefix: "",
    figureValue: "8",
    figureSuffix: "",
    text: "Standorte betreibt das BSI bundesweit, um Cyber-Sicherheit flaechendeckend zu gewaehrleisten."
  }
};
