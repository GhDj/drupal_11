import type { ArgTypes } from "@storybook/html-vite";
import type { PaginationTemplateArgs } from "./pagination.template";

export const paginationArgTypes: ArgTypes<PaginationTemplateArgs> = {
  paginationCurrentPage: {
    name: "Current Page",
    table: { category: "Content" },
    control: { type: "number" }
  },
  paginationTotalPages: {
    name: "Total Pages",
    table: { category: "Content" },
    control: { type: "number" }
  },
  paginationBaseUrl: {
    name: "Base URL",
    table: { category: "Behaviour" },
    control: { type: "text" }
  }
};
