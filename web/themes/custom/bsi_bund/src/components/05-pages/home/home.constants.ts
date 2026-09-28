import { type CardTemplateArgs } from "@molecules/card/card.template";
import { type QuoteTemplateArgs } from "@molecules/quote/quote.template";

export const quoteArgs: QuoteTemplateArgs = {
  imageSrc: "demo-image-1_1.jpg",
  imageAlt: "Claudia Plattner",
  quoteText:
    "Wer Digitalisierung nicht beherrscht, wird auch Sicherheit nicht beherrschen – und umgekehrt. Wir im BSI haben es uns zur Aufgabe gemacht, Brücke zwischen beiden Welten zu sein. Denn Prosperität und Stabilität hängen von unseren digitalen Fähigkeiten ab und davon, wie gut wir den digitalen Raum verteidigen können.",
  authorName: "Claudia Plattner",
  authorRole: "BSI-Präsidentin",
  linkText: "Das BSI",
  linkUrl: "#"
};

// data for featured card
export const featuredCardArgs: CardTemplateArgs = {
  cardExtraClasses: "card--featured",
  cardImagePosition: "image-left",
  cardImage: { imageSrc: "demo-image-2_1.jpg" },
  cardTopline: "28.03.2026",
  cardHeading:
    "BSI informiert über aktuelle Phishing-Kampagnen und gibt Handlungsempfehlungen",
  cardText:
    "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.",
  cardCtaText: "Mehr zu Phishing und Schutzmaßnahmen",
  hasDivider: true
};

// data for the card-group block
export const cardGroupItems: CardTemplateArgs[] = [
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    cardImage: { imageSrc: "demo-image-4_3.jpg" },
    cardTopline: "16.02.2026",
    cardHeading: "Unternehmensnetzwerke sicher betreiben",
    cardText:
      "Ever started designing before the content arrived? Then you know the struggle with lorem ipsum – and the constant copy-pasting into Bricks. It’s a hassle, right?",
    cardCtaText: "Mehr zu den Empfehlungen"
  },
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    cardImage: { imageSrc: "demo-image-4_3.jpg" },
    cardTopline: "11.02.2026",
    cardHeading: "Neue Hinweise zur Absicherung kommunaler IT-Infrastrukturen",
    cardText:
      "Ever started designing before the content arrived? Then you know the struggle with lorem ipsum – and the constant copy-pasting into Bricks. It’s a hassle, right?",
    cardCtaText: "Mehr zur IT-Resilienz in Kommunen"
  },
  {
    additionalCardAttributes: { "data-theme": "blue-100" },
    cardImage: { imageSrc: "demo-image-4_3.jpg" },
    cardTopline: "23.01.2026",
    cardHeading: "BSI beteiligt sich an europäischer Cyber-Sicherheitsübung",
    cardText:
      "Ever started designing before the content arrived? Then you know the struggle with lorem ipsum – and the constant copy-pasting into Bricks. It’s a hassle, right?",
    cardCtaText: "Mehr zur internationalen Zusammenarbeit"
  }
];
