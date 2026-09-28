import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { anchorsTemplate, type AnchorsTemplateArgs } from "./anchors.template";

// Register the story and its metadata
export default {
  title: "Organisms/Anchors",
  render: anchorsTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"]
} satisfies Meta<AnchorsTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<AnchorsTemplateArgs>;

// Story for the default anchors navigation
export const Anchors: Story = {};
