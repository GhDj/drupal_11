import type { NavLinkType } from "./mainmenu.template";

export const defaultMainNav: NavLinkType[] = [
  {
    text: "Das BSI",
    cardTitle: "Erste Umwelterklärung nach EMAS",
    cardDate: "2019-2022",
    cardLink: "#",
    cardLinkText: "Umwelterklärung herunterladen",
    cardAddOnText: "PDF, 2MB",
    subnav: [
      {
        text: "Normung verstehen"
      },
      {
        text: "Normen mitgestalten"
      },
      {
        text: "Normen kaufen"
      }
    ]
  },
  {
    text: "Lage"
  },
  {
    text: "Cybersicherheit",
    subnav: [
      {
        text: "Technologie und Forschung",
        subnav: [
          { text: "Kryptographie" },
          { text: "Quantentechnologie" },
          { text: "Post-Quantum-Kryptographie" },
          { text: "Künstliche Intelligenz" }
        ]
      },
      {
        text: "Produkte und Dienstleistungen",
        subnav: [
          { text: "Unterseite 1" },
          { text: "Unterseite 2" },
          { text: "Unterseite 3" },
          { text: "Unterseite 4" }
        ]
      },
      {
        text: "Organisationen"
      },
      {
        text: "Verbraucherinnen und Verbraucher",
        subnav: [
          { text: "Subpage 1" },
          { text: "Subpage 2" },
          { text: "Subpage 3" },
          { text: "Subpage 4" }
        ]
      },
      {
        text: "Security Operations"
      },
      {
        text: "Kooperationen"
      }
    ]
  },
  {
    text: "Melden"
  },
  {
    text: "Karriere"
  },
  {
    text: "Service"
  }
];
