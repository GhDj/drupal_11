import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import {
  timelineTemplate,
  type TimelineTemplateArgs
} from "./timeline.template";

// Data
import timelineData from "./timeline.yml";

// Register the story and its metadata
export default {
  title: "Organisms/Timeline",
  render: timelineTemplate,

  // Snapshots for this component will be done in the page templates
  tags: ["mobile", "desktop"]
} satisfies Meta<TimelineTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<TimelineTemplateArgs>;

export const Timeline: Story = {
  args: {
    ...(timelineData as TimelineTemplateArgs),
    isStatic: false
  },
  argTypes: {
    isStatic: {
      control: "boolean"
    }
  }
};
