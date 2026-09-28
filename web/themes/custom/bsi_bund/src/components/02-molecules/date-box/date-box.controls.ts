import type { ArgTypes } from "@storybook/html-vite";
import type { DateBoxTemplateArgs } from "./date-box.template";

export const dateBoxArgTypes: ArgTypes<DateBoxTemplateArgs> = {
  dates: {
    name: "Variant",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: {
        single: "single",
        range: "range",
        series: "series"
      }
    },
    options: ["single", "range", "series"],
    mapping: {
      single: [
        { year: "2026", day: "28", month: "Mai", weekday: "Donnerstag" }
      ],
      range: [
        {
          year: "2026",
          day: "28",
          month: "September",
          weekday: "Donnerstag"
        },
        { year: "2026", day: "4", month: "Oktober", weekday: "Dienstag" }
      ],
      series: [
        {
          year: "2026",
          day: "28",
          month: "September",
          weekday: "Donnerstag"
        },
        { year: "", day: "", month: "" },
        { year: "", day: "", month: "" },
        { year: "", day: "", month: "" }
      ]
    }
  }
};
