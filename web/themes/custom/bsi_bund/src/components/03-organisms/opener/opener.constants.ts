import demoImage1 from "@demo-content/images/demo-image-16_9.jpg";
import demoImage2 from "@demo-content/images/demo-image-4_3.jpg";
import demoImage3 from "@demo-content/images/demo-image-2_1.jpg";

export const openerVariants = [
  "centered",
  "left-inside",
  "left-outside",
  "slider"
] as const;
export type OpenerVariant = (typeof openerVariants)[number];

export interface OpenerSliderItem {
  title: string;
  text: string;
  imageUrl: string;
  imageAlt?: string;
  ctaText?: string;
  ctaUrl?: string;
}

export const openerSliderDefaultItems: OpenerSliderItem[] = [
  {
    title: "Kryptografie",
    text: "IT-Sicherheit erfordert eine kontinuierliche Entwicklung und Evaluierung von kryptografischen Verfahren.",
    imageUrl: demoImage1,
    ctaText: "Mehr zu Phishing und Schutzmaßnahmen",
    ctaUrl: "#"
  },
  {
    title: "Kryptografie",
    text: "IT-Sicherheit erfordert eine kontinuierliche Entwicklung und Evaluierung von kryptografischen Verfahren. Die Kryptografie beschäftigt sich mit den wissenschaftlichen Grundlagen von Informationssicherheit.",
    imageUrl: demoImage2,
    ctaText: "Mehr zu Phishing und Schutzmaßnahmen",
    ctaUrl: "#"
  },
  {
    title: "Kryptografie",
    text: "IT-Sicherheit erfordert eine kontinuierliche Entwicklung und Evaluierung von kryptografischen Verfahren. Die Kryptografie beschäftigt sich mit den wissenschaftlichen Grundlagen von Informationssicherheit, also mit der  Entwicklung und Bewertung kryptografischer Mechanismen als Grundlage für sichere IT-Systeme. IT-Sicherheit erfordert eine kontinuierliche Entwicklung und Evaluierung von kryptografischen Verfahren.",
    imageUrl: demoImage3,
    ctaText: "Mehr zu Phishing und Schutzmaßnahmen",
    ctaUrl: "#"
  }
];
