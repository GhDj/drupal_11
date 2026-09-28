import type { ArgTypes } from "@storybook/html-vite";
import type { PageGridTemplateArgs } from "./page-grid.template";

// Controls configuration
export const pageGridArgTypes: ArgTypes<PageGridTemplateArgs> = {
  showStage: {
    name: "Show Stage",
    table: { category: "Content" },
    control: { type: "boolean" }
  },
  showBreadcrumb: {
    name: "Show Breadcrumb",
    table: { category: "Content" },
    control: { type: "boolean" }
  },
  showAnchors: {
    name: "Show Anchors",
    table: { category: "Content" },
    control: { type: "boolean" }
  }
};
