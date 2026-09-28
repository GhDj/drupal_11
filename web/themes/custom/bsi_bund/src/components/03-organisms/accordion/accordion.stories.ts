import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  accordionTemplate,
  type AccordionTemplateArgs
} from "./accordion.template";
import { defaultAccordionItems } from "./accordion.constants";

// Register the story and its metadata
export default {
  title: "Organisms/Accordion",
  render: accordionTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"],

  // Default values for storybook controls
  args: {
    items: defaultAccordionItems
  }
} satisfies Meta<AccordionTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<AccordionTemplateArgs>;

// Story for basic heading
export const Accordion: Story = {};
