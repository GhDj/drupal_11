import type { Meta, StoryObj } from "@storybook/html";
import { searchResultTemplate } from "./search-result.template";

const result = {
  cardExtraClasses: "card--search",
  cardTopline: "Publikation  ·  10.06.2026",
  cardHeading:
    "Cybersicherheitsmonitor 2026: Ergebnispräsentation zur Befragung",
  cardText:
    "Fokusthema „Online-Betrug & Künstliche Intelligenz“ zum Cybersicherheitsmonitor 2026 der Polizeilichen Kriminalprävention der Länder und des Bundes (ProPK) und des Bundesamts für Sicherheit in der Informationstechnik (BSI)",
  cardCtaText: "PDF, 909KB herunterladen",
  cardCtaIconBefore: "download"
};

const meta: Meta = {
  title: "Molecules/Search Result",
  render: args => searchResultTemplate(args),
  parameters: {
    layout: "padded"
  }
};

export default meta;

type Story = StoryObj;

export const searchResult: Story = {
  args: {
    result: [result, result, result]
  }
};
