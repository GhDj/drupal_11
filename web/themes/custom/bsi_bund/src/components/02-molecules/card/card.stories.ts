import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { cardTemplate, type CardTemplateArgs } from "./card.template";

// Controls
import { cardArgTypes } from "./card.controls";

// Register the story and its metadata
export default {
  title: "Molecules/Card",
  render: cardTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"],

  // Default values for controls
  args: {
    cardExtraClasses: "",
    additionalCardAttributes: "" as unknown as Record<string, string>,
    cardImagePosition: "",
    cardHeadingStyle: 4,
    cardCtaIconBefore: "chevron-right",
    cardTopline: true,
    cardHeading: true,
    cardText: true,
    cardCtaText: true,
    hasDivider: false
  },

  // Controls shown in the "Controls" panel
  argTypes: cardArgTypes
} satisfies Meta<CardTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<CardTemplateArgs>;

// Story for vertical card
export const Vertical: Story = {
  args: {
    cardImage: {
      imageSrc: "demo-image-4_3.jpg"
    },
    additionalCardAttributes: {
      "data-theme": "blue-100"
    }
  },
  argTypes: {
    cardImage: { table: { disable: true } },
    cardImagePosition: { table: { disable: true } }
  }
};

// Story for horizontal card
export const Horizontal: Story = {
  args: {
    cardImagePosition: "image-left",
    cardImage: {
      imageSrc: "demo-image-2_1.jpg"
    }
  }
};

// Story for icon card
export const Icon: Story = {
  args: {
    cardTopline: false,
    cardText: false,
    cardCtaText: false,
    cardHeadingHref: "#",
    cardIcon: "meeting",
    cardExtraClasses: "card--icon"
  }
};

// Story for featured card
export const Featured: Story = {
  args: {
    cardImagePosition: "image-left",
    cardImage: {
      imageSrc: "demo-image-4_3.jpg"
    },
    cardExtraClasses: "card--featured",
    hasDivider: true
  }
};

// Story for menu card
export const Menu: Story = {
  args: {
    hasDivider: true,
    additionalCardAttributes: {
      "data-theme": "blue-100"
    }
  },
  argTypes: {
    cardImage: { table: { disable: true } },
    cardImagePosition: { table: { disable: true } }
  }
};
