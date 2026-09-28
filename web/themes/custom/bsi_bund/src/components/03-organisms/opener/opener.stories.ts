import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { openerTemplate, type OpenerTemplateArgs } from "./opener.template";

// Controls
import { openerArgTypes } from "./opener.controls";

// Register the story and its metadata
export default {
  title: "Organisms/Opener",
  render: openerTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"],

  // Default values for storybook controls
  args: {
    variant: "centered",
    showBadges: true
  },

  // Controls shown in the "Controls" panel
  argTypes: openerArgTypes
} satisfies Meta<OpenerTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<OpenerTemplateArgs>;

// Story for the centerd opener
export const Centered: Story = {
  args: {
    subtitle: "Jahreslagebericht 2025"
  }
};

// Story for the left-inside opener
export const LeftInside: Story = {
  args: {
    variant: "left-inside",
    showFacts: true,
    showBadges: false
  }
};

// Story for the left-outside opener
export const LeftOutside: Story = {
  args: {
    variant: "left-outside",
    showBadges: false
  }
};

// Story for the slider opener
export const Slider: Story = {
  args: {
    variant: "slider",
    showBadges: false
  }
};
