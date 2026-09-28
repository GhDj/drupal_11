import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  contentTeaserTemplate,
  type ContentTeaserTemplateArgs
} from "./content-teaser.template";

// Controls
import { contentTeaserArgTypes } from "./content-teaser.control";

// Register the story and its metadata
export default {
  title: "Organisms/Content Teaser",
  render: contentTeaserTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"],

  argTypes: contentTeaserArgTypes
} satisfies Meta<ContentTeaserTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<ContentTeaserTemplateArgs>;

// Story for basic card
export const ContentTeaser: Story = {};
