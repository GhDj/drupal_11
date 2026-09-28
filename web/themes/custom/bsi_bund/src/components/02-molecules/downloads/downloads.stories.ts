import type { Meta, StoryObj } from "@storybook/html-vite";

import { downloadsArgTypes } from "./downloads.controls";
import { defaultDownloadsItems } from "./downloads.constants";
import {
  downloadsTemplate,
  type DownloadsTemplateArgs
} from "./downloads.template";

const meta: Meta<DownloadsTemplateArgs> = {
  title: "Molecules/Downloads",
  render: args => downloadsTemplate(args),

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"],

  argTypes: downloadsArgTypes,
  parameters: {
    layout: "padded"
  }
};

export default meta;

type Story = StoryObj<DownloadsTemplateArgs>;

export const Default: Story = {
  args: {
    blockTitle: "Downloads",
    items: defaultDownloadsItems
  }
};

export const WithThumbnail: Story = {
  args: {
    blockTitle: "Downloads",
    items: [
      {
        ...defaultDownloadsItems[0],
        media: {
          type: "thumbnail",
          imageSrc: "demo-image-9_16.jpg",
          imageAlt: "Broschure Kunstliche Intelligenz sicher nutzen"
        }
      }
    ]
  }
};
