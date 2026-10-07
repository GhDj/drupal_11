import type { Meta, StoryObj } from "@storybook/html";
import { searchfilterTemplate } from "./searchfilter.template";

const filters = [
  {
    id: "format",
    label: "Format",
    options: [
      {
        value: "digital",
        label: "Digital"
      }
    ]
  },
  {
    id: "publikationsart",
    label: "Publikationsart",
    options: [
      {
        value: "broschuere",
        label: "Broschüre"
      }
    ]
  },
  {
    id: "zielgruppe",
    label: "Zielgruppe",
    options: [
      {
        value: "lorem",
        label: "Lorem ipsum"
      }
    ]
  },
  {
    id: "jahr",
    label: "Jahr",
    options: [
      {
        value: "2026",
        label: "2026"
      },
      {
        value: "2025",
        label: "2025"
      },
      {
        value: "2024",
        label: "2024"
      }
    ]
  }
];

const meta: Meta = {
  title: "Molecules/Searchfilter",
  render: args => searchfilterTemplate(args),
  parameters: {
    layout: "padded"
  }
};

export default meta;

type Story = StoryObj;

export const Searchfilter: Story = {
  args: {
    filters,
    activeFilters: ["Broschüre", "Digital", "2024"]
  },
  argTypes: {
    filters: { control: false },
    activeFilters: {
      name: "active Filter",
      table: { category: "Content" },
      control: { type: "boolean" },
      mapping: {
        true: ["Broschüre", "Digital", "2024"],
        false: undefined
      }
    }
  }
};
