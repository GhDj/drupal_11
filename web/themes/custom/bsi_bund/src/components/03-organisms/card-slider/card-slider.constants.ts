import type { CardTemplateArgs } from "@molecules/card/card.template";

const demoImageSrc = "demo-image-4_3.jpg";

export const items: CardTemplateArgs[] = [
  {
    cardImage: { imageSrc: demoImageSrc },
    cardHeading: "Technologie und Forschung",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Technologie und Forschung",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  },
  {
    cardImage: { imageSrc: demoImageSrc },
    cardHeading: "Produkten und Dienstleistungen",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Produkten und Dienstleistungen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  },
  {
    cardImage: { imageSrc: demoImageSrc },
    cardHeading: "Organisationen",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Organisationen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  },
  {
    cardDate: {
      dates: [{ year: "2026", day: "28", month: "Mai", weekday: "Donnerstag" }]
    },
    cardTopline: "Berlin",
    cardHeading: "Lorem ipsum dolor",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Lorem ipsum dolor",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  },
  {
    cardDate: {
      dates: [
        {
          year: "2026",
          day: "28",
          month: "September",
          weekday: "Donnerstag"
        },
        { year: "2026", day: "4", month: "Oktober", weekday: "Dienstag" }
      ]
    },
    cardTopline: "Berlin",
    cardHeading: "Lorem ipsum dolor",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Organisationen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  },
  {
    cardDate: {
      dates: [
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
    },
    cardTopline: "Berlin",
    cardHeading: "Lorem ipsum dolor",
    cardText: "Lorem ipsum dolor sit amet...",
    cardCtaText: "Mehr zu Organisationen",
    additionalCardAttributes: {
      "data-theme": "blue-100"
    },
    hasDivider: true
  }
];
