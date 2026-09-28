import type { Meta, StoryObj } from "@storybook/html-vite";

// Globals
import { nsp, html } from "@globals/index";

// Components
import { placeholderTemplate } from "@base/placeholder/placeholder.template";
import { gridArgTypes, type GridControlsArgs } from "./grid.controls";

// Allowed number of grid items used by the control
type GridItems = 1 | 2 | 3 | 4;

interface Args extends GridControlsArgs {
  elements: GridItems; // number of grid items to render
}

// Register the story and its metadata
export default {
  title: "Base/Grid",

  // Define, what snapshots should be created
  tags: ["no-snapshot"],

  // Default values for controls
  args: {
    elements: 2,
    gridVariant: "two-items"
  },

  // Controls shown in the "Controls" panel
  argTypes: {
    ...gridArgTypes,
    elements: {
      name: "Grid elements",
      table: { category: "Content" },
      control: { type: "range", min: 1, max: 4, step: 1 }
    }
  }
} satisfies Meta<Args>;

// Reusable Story type based on the template args
type Story = StoryObj<Args>;

// Story: Renders the grid
export const Grid: Story = {
  render: args => {
    const { elements, gridVariant = "two-items" } = args;

    // Generate N placeholder items (1..elements)
    const items = Array.from({ length: elements }, () => {
      return placeholderTemplate();
    }).join("");

    // Add variant class if specified
    let variantClass = "";
    if (gridVariant) {
      variantClass = `grid--${gridVariant}`;
    }

    // Build the grid class string
    const gridClasses = nsp("grid", variantClass);

    // Return the final HTML string
    return html` <section class="${gridClasses}">${items}</section> `;
  }
};
