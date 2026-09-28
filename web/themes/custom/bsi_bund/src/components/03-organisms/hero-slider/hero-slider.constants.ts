import demoImage1 from "@demo-content/images/demo-image-16_9.jpg";
import demoImage2 from "@demo-content/images/demo-image-4_3.jpg";
import demoImage3 from "@demo-content/images/demo-image-2_1.jpg";

import type { CardTemplateArgs } from "@molecules/card/card.template";

export interface HeroSliderItem {
  title: string;
  text: string;
  imageUrl: string;
  imageAlt?: string;
  ctaText?: string;
  ctaUrl?: string;
}

export const heroSliderDefaultItems: HeroSliderItem[] = [
  {
    title: "Welt(un)ordnung im digitalen Raum",
    text: "Have you ever begun creating your designs while still waiting for your clients to send the content? If yes, then you're surely familiar with lorem ipsum! You know, the hassle of constantly swapping windows just to copy and paste your lorem ipsum text into Bricks? What if you could skip that whole step? Imagine having smart placeholder text right where you need it, built right into your design tool. No more tab-switching, no more copy-paste chaos - just seamless workflow.",
    imageUrl: demoImage1,
    ctaText: "Zum Wheel of Distortion",
    ctaUrl: "#"
  },
  {
    title: "Cyber-Sicherheit im Fokus",
    text: "Aktuelle Informationen, Empfehlungen und Veranstaltungen fuer eine sichere digitale Verwaltung, Wirtschaft und Gesellschaft.",
    imageUrl: demoImage2,
    ctaText: "Zur Lage",
    ctaUrl: "#"
  },
  {
    title: "Souveraen digital handeln",
    text: "Orientierung fuer Organisationen, die sichere digitale Angebote planen, betreiben und langfristig weiterentwickeln.",
    imageUrl: demoImage3,
    ctaText: "Mehr erfahren",
    ctaUrl: "#"
  }
];

// Störer
export const heroSliderDefaultOverlay: CardTemplateArgs = {
  cardTopline: "24.05.2026",
  cardHeading: "Mobile World Congress 2026",
  cardText: "Ever started designing before the content arrived?...",
  cardCtaText: "Zur Veranstaltung"
};
