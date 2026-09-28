import type { FooterTemplateArgs } from "./footer.template";

export const defaultFooterArgs: FooterTemplateArgs = {
  socialMediaHeading: "Folgen Sie uns",
  imageSrc: "/src/demo-content/images/demo-image-iso.png",
  imageAlt: "alt Tag",
  imageDescription: "",
  navigationLinks: [
    {
      label: "Das BSI",
      url: "/bsi"
    },
    {
      label: "Lage",
      url: "/lage"
    },
    {
      label: "Cybersicherheit",
      url: "/cybersicherheit"
    },
    {
      label: "Melden",
      url: "/melden"
    },
    {
      label: "Karriere",
      url: "/karriere"
    },
    {
      label: "Service",
      url: "/service"
    }
  ],
  socialMediaLinks: [
    {
      label: "Instagram",
      url: "/instagram"
    },
    {
      label: "Bluesky",
      url: "/bluesky"
    },
    {
      label: "LinkedIn",
      url: "/linkedin"
    },
    {
      label: "Xing",
      url: "/xing"
    },
    {
      label: "YouTube",
      url: "/youtube"
    },
    {
      label: "Mastodom",
      url: "/mastodom"
    },
    {
      label: "RSS",
      url: "/rss"
    }
  ],
  serviceLinks: [
    {
      label: "Impressum",
      url: "/impressum"
    },
    {
      label: "Datenschutz",
      url: "/datenschutz"
    },
    {
      label: "Nutzungsbedingungen",
      url: "/nutzungsbedingungen"
    },
    {
      label: "Barriere melden",
      url: "/barriere-melden"
    }
  ],
  linkButtonText: "Kontakt aufnehmen",
  copyright: "Bundesamt für Sicherheit in der Informationstechnik"
};
