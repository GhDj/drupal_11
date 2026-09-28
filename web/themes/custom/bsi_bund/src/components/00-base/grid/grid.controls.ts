import type { ArgTypes } from "@storybook/html-vite";

// Custom types
export type GridVariant = "" | "two-items" | "three-items" | "four-items";

export interface GridControlsArgs {
  gridVariant?: GridVariant;
}

// Controls configuration
export const gridArgTypes: ArgTypes<GridControlsArgs> = {
  gridVariant: {
    name: "Grid variant",
    table: { category: "Layout" },
    control: {
      type: "select",
      labels: {
        "": "None",
        "two-items": "Two items",
        "three-items": "Three items",
        "four-items": "Four items"
      }
    },
    options: ["", "two-items", "three-items", "four-items"]
  }
};
