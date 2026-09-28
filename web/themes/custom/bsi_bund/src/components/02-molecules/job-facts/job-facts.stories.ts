import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  jobFactsTemplate,
  type JobFactsTemplateArgs
} from "./job-facts.template";

// Register the story and its metadata
export default {
  title: "Molecules/Job Facts",
  render: jobFactsTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"]
} satisfies Meta<JobFactsTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<JobFactsTemplateArgs>;

// Story for basic heading
export const JobFacts: Story = {};
