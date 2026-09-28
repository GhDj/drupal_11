import type { LinkListTemplateArgs } from "@molecules/linklist/linklist.template";

export const defaultLinkBox: LinkListTemplateArgs[] = [
  {
    title: "Vollständige Berichte",
    links: [
      {
        linkUrl: "#",
        linkText: "PDF, 1 MB herunterladen",
        linkAddOnText: "Download Umwelterklärung 2025",
        linkType: "download"
      },
      {
        linkUrl: "#",
        linkText: "PDF, 1 MB herunterladen",
        linkAddOnText: "Download Umwelterklärung 2024",
        linkType: "download"
      }
    ]
  },
  {
    title: "Weitere Informationen",
    links: [
      {
        linkUrl: "#",
        linkText: "BSI veröffentlicht erste Umwelterklärung nach EMAS",
        linkType: "internal"
      },
      {
        linkUrl: "#",
        linkText: "Koordinierungsstelle Klimaneutrale Bundesverwaltung (KKB)",
        linkType: "external"
      }
    ]
  }
];
