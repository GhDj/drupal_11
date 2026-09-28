import type { Meta, StoryObj } from "@storybook/html-vite";
import { SearchTemplate } from "./search.template";

const meta: Meta = {
  title: "Templates/Search",

  render: args => {
    return SearchTemplate({
      ...args
    });
  }
};

export default meta;

type Story = StoryObj;

export const Default: Story = {
  args: {
    title: "Jahreslagebericht 2025",
    openerTitle: "Die Systematik der Lagebewertung",
    openerText: "Lorem ipsum dolor sit amet.",
    searchTitle: "Lagebericht durchsuchen"
  }
};
