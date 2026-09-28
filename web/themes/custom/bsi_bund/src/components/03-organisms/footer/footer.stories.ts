import type { Meta, StoryObj } from "@storybook/html";

import { footerTemplate, type FooterTemplateArgs } from "./footer.template";

import { footerArgTypes } from "./footer.controls";
import { defaultFooterArgs } from "./footer.constants";

const meta: Meta<FooterTemplateArgs> = {
  title: "Organisms/Footer",
  render: args => footerTemplate(args),

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"],

  argTypes: footerArgTypes
};

export default meta;
type Story = StoryObj<FooterTemplateArgs>;

export const Footer: Story = {
  args: defaultFooterArgs
};
