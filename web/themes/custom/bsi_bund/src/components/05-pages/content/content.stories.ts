import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { contentTemplate } from "./content.template";
import { contentArticleTemplate } from "./contentArticle.template";
import { contentEntryTemplate } from "./contentEntry.template";

// Register the story and its metadata
export default {
  title: "Pages/Content",
  render: contentTemplate,

  // Define, what snapshots should be created
  tags: ["all"]
} satisfies Meta;

// Reusable Story type based on the template args
type Story = StoryObj;

// Story for Content page
export const Content: Story = {};

// Story for Article page
export const Article: Story = {
  render: contentArticleTemplate
};

// Story for Entry page
export const Entry: Story = {
  render: contentEntryTemplate
};
