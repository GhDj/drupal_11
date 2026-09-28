import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

import { defaultSixtySecItems } from "./60-sec.constants";
import { sixtySecArgTypes } from "./60-sec.controls";
import { sixtySecTemplate, type SixtySecTemplateArgs } from "./60-sec.template";
import { initSixtySec } from "./60-sec";

const meta: Meta<SixtySecTemplateArgs> = {
  title: "Molecules/60 Sec",
  render: args => {
    window.requestAnimationFrame(() => initSixtySec());

    return sixtySecTemplate(args);
  },

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"],

  argTypes: sixtySecArgTypes,

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;
type Story = StoryObj<SixtySecTemplateArgs>;

export const Default: Story = {
  args: {
    blockTitle: "In 60 Sekunden",
    items: defaultSixtySecItems
  }
};
