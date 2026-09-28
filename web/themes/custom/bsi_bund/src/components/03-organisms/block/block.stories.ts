import type { Meta, StoryObj } from "@storybook/html-vite";

// Templates
import { blockTemplate, type BlockTemplateArgs } from "./block.template";

// Example content
import { cardGroupTemplate } from "@organisms/card-group/card-group.template";
import { textTemplate } from "@atoms/text/text.template";
import { tabsTemplate } from "@organisms/tabs/tabs.template";

import { blockArgTypes } from "./block.controls";
const contentOptions = {
  CardGroup: cardGroupTemplate(),
  Text: textTemplate({
    textContent: "Lorem ipsum dolor sit amet..."
  }),
  Tabs: tabsTemplate(),
  Empty: ""
};
/* ---------------------------
  Meta
--------------------------- */

const meta: Meta<BlockTemplateArgs> = {
  title: "Organisms/Block",

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"],

  argTypes: {
    ...blockArgTypes,
    blockContent: {
      name: "Block content",
      control: { type: "select" },
      options: Object.keys(contentOptions)
    }
  },
  render: args =>
    blockTemplate({
      ...args,
      blockContent:
        contentOptions[args.blockContent as keyof typeof contentOptions]
    }),
  args: {
    blockTitle: "Aktuelle Themen",
    blockContent: "CardGroup"
  }
};

export default meta;

/* ---------------------------
  Stories
--------------------------- */

type Story = StoryObj<BlockTemplateArgs>;

/**
 * Block without content → placeholder sichtbar
 */
export const Default: Story = {
  args: {
    blockTitle: "Leerer Block",
    blockContent: ""
  }
};

/**
 * Block mit Meta-Link im Heading
 */
export const WithMetaLink: Story = {
  args: {
    blockTitle: "Aktuelle Themen",
    metaLink: "Mehr erfahren",
    blockContent: ""
  }
};

export const IndentedContent: Story = {
  args: {
    blockTitle: "Eingezogener Content",
    metaLink: "Mehr erfahren",
    blockContent: "",
    contentIndented: true
  }
};
