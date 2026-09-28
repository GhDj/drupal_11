import type { Meta, StoryObj } from "@storybook/html-vite";
// Component
import {
  copyrightTemplate,
  type CopyrightTemplateArgs
} from "./copyright.template";

const meta: Meta<CopyrightTemplateArgs> = {
  title: "Atoms/Copyright",
  render: args => copyrightTemplate(args),

  // Define, what snapshots should be created
  tags: ["mobile"],

  args: {
    copyrightText: "Bundesamt für Sicherheit in der Informationstechnik",
    copyrightPrefix: "Quelle:",
    copyrightExtraClasses: ""
  }
};

export default meta;

type Story = StoryObj<CopyrightTemplateArgs>;

export const Copyright: Story = {};
