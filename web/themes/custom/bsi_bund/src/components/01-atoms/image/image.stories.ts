import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { imageTemplate, type ImageTemplateArgs } from "./image.template";

// Controls
import { imageArgTypes } from "./image.controls";

const meta: Meta<ImageTemplateArgs> = {
  title: "Atoms/Image",
  render: args => imageTemplate(args),

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  argTypes: imageArgTypes,

  args: {
    imageSrc: "demo-image-16_9.jpg",
    imageAlt: "Demo image",
    imageLoading: "lazy",
    imageCaption: "",
    imageDescription: "",
    imageCopyright: "",
    imageExtraClasses: ""
  }
};

export default meta;

type Story = StoryObj<ImageTemplateArgs>;

export const Basic: Story = {};

export const WithCaption: Story = {
  args: {
    imageCaption: "Das ist eine Bildunterschrift",
    imageDescription: "Zusätzliche Beschreibung für Screenreader"
  }
};

export const WithCopyright: Story = {
  args: {
    imageCopyright: "© BSI / Fotograf XY"
  }
};

export const FullExample: Story = {
  args: {
    imageCaption: "Beispiel Caption",
    imageDescription: "Zusätzliche Beschreibung",
    imageCopyright: "© BSI"
  }
};
