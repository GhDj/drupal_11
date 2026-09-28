import type { Meta, StoryObj } from "@storybook/html-vite";
import { html } from "@globals/index";

import { quoteTemplate, type QuoteTemplateArgs } from "./quote.template";

const meta: Meta<QuoteTemplateArgs> = {
  title: "Molecules/Quote",
  render: args => quoteTemplate(args),

  // Define, what snapshots should be created
  tags: ["mobile", "desktop"],

  decorators: [Story => html` <div class="bsi-grid">${Story()}</div> `]
};

export default meta;
type Story = StoryObj<QuoteTemplateArgs>;

export const Quote: Story = {
  args: {
    imageSrc: "demo-image-1_1.jpg",
    imageAlt: "Claudia Plattner",
    quoteText:
      "Wer Digitalisierung nicht beherrscht, wird auch Sicherheit nicht beherrschen – und umgekehrt. Wir im BSI haben es uns zur Aufgabe gemacht, Brücke zwischen beiden Welten zu sein. Denn Prosperität und Stabilität hängen von unseren digitalen Fähigkeiten ab und davon, wie gut wir den digitalen Raum verteidigen können.",
    authorName: "Claudia Plattner",
    authorRole: "BSI-Präsidentin",
    linkText: "Das BSI",
    linkUrl: "#"
  }
};
