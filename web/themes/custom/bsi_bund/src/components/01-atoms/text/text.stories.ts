import type { Meta, StoryObj } from "@storybook/html";
import { html, nsp } from "@globals/index";
import { textTemplate, type TextTemplateArgs } from "./text.template";
import { bodytextExampleContent } from "./text.constants";

// -----------------------------------------------
// Storybook metadata
// -----------------------------------------------

const meta: Meta<TextTemplateArgs> = {
  title: "Atoms/Text",
  render: args => textTemplate(args),

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  decorators: [Story => html`<div class="${nsp("grid")}">${Story()}</div>`]
};

export default meta;

type Story = StoryObj<TextTemplateArgs>;

// Stories
// -----------------------------------------------

export const Default: Story = {
  name: "Default text",
  args: {
    textContent: "Lorem ipsum dolor sit amet."
  }
};

export const Bodytext: Story = {
  args: {
    bodytext: bodytextExampleContent
  }
};
