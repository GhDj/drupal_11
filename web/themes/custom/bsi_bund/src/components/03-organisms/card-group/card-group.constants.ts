import type { CardTemplateArgs } from "@molecules/card/card.template";

const demoImageSrc = "demo-image-4_3.jpg";

export const defaultCardGroupItems: CardTemplateArgs[] = [
  {
    cardImage: { imageSrc: demoImageSrc, imageAlt: "" },
    cardTopline: "16.02.2026",
    cardHeading: "Unternehmensnetzwerke sicher betreiben",
    cardCtaText: "Mehr zu den Empfehlungen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    }
  },
  {
    cardImage: { imageSrc: demoImageSrc, imageAlt: "" },
    cardTopline: "11.02.2026",
    cardHeading: "Neue Hinweise zur Absicherung kommunaler IT-Infrastrukturen",
    cardCtaText: "Mehr zur IT-Resilienz in Kommunen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    }
  },
  {
    cardDate: {
      dates: [{ year: "2026", day: "28", month: "Mai", weekday: "Donnerstag" }]
    },
    cardTopline: "23.01.2026",
    cardHeading: "BSI beteiligt sich an europäischer Cyber-Sicherheitsübung",
    cardCtaText: "Mehr zur internationalen Zusammenarbeit",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    }
  }
];

export const fourColumnsCardGroupItems: CardTemplateArgs[] = [
  {
    cardIcon: "leitung",
    cardHeading: "Leitung"
  },
  {
    cardIcon: "meeting",
    cardHeading: "Abteilungen inkl. Organigramm"
  },
  {
    cardIcon: "meeting",
    cardHeading: "Abteilungen inkl. Organigramm"
  },
  {
    cardIcon: "meeting",
    cardHeading: "Abteilungen inkl. Organigramm"
  }
].map(item => ({
  cardImagePosition: "image-left",
  cardTopline: false,
  cardText: false,
  cardCtaText: false,
  cardHeadingHref: "#",
  cardHeadingLevel: 3,
  cardHeadingStyle: 4,
  cardExtraClasses: "card--icon",
  ...item
}));
