import type { Meta, StoryObj } from "@storybook/html-vite";

import {
  cardGroupTemplate,
  type CardGroupTemplateArgs
} from "./card-group.template";
import { fourColumnsCardGroupItems } from "./card-group.constants";

const meta: Meta<CardGroupTemplateArgs> = {
  title: "Organisms/Card Group",
  render: cardGroupTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["no-snapshot"]
};

export default meta;

type Story = StoryObj<CardGroupTemplateArgs>;

export const Default: Story = {};

// Story for two columns
export const TwoColumns: Story = {
  args: {
    extraClasses: "card-group--two-columns"
  }
};

// Story for four columns
export const FourColumns: Story = {
  args: {
    extraClasses: "card-group--four-columns",
    items: fourColumnsCardGroupItems
  }
};
