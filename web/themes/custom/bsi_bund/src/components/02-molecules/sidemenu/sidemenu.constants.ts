import type { SideNavLinkType } from "./sidemenu.template";

export const defaultSideNavTel = "0228 99 9582-6388";

export const defaultSideNav: SideNavLinkType[] = [
  {
    text: "Einleitung",
    subnav: [
      {
        text: "Systematik der Lagebewertung",
        url: "#"
      },
      {
        text: "Vorworte und Fazit",
        url: "#"
      },
      {
        text: "Zeitrahl des Berichtszeitraums",
        url: "#"
      },
      {
        text: "Zusammenfassung und Bewertung",
        url: "#"
      },
      {
        text: "Dokumente zum Download",
        url: "#"
      }
    ]
  },
  {
    text: "Lage in Deutschland",
    subnav: [
      {
        text: "01 Bedrohungen",
        subnav: [
          {
            text: "Cyberkriminalität",
            url: "#"
          },
          {
            text: "Staatliche Akteure",
            url: "#"
          }
        ]
      },
      {
        text: "02 Angriffsfläche",
        subnav: [
          {
            text: "Web Angriffsfläche",
            subnav: [
              {
                text: ".de-Domains",
                url: "#"
              },
              {
                text: "Bundesverwaltung",
                url: "#"
              }
            ]
          },
          {
            text: "Schwachstellen",
            subnav: [
              {
                text: "KI-Schwachstellen",
                subnav: [
                  {
                    text: "Schwachstellen in KI-Systemen",
                    url: "#"
                  },
                  {
                    text: "Angriffe auf KI-Systeme",
                    url: "#"
                  }
                ]
              },
              {
                text: "Herausgehobene Vorfälle",
                subnav: [
                  {
                    text: "Kritische Schwachstellen",
                    url: "#"
                  },
                  {
                    text: "Ausgenutzte Schwachstellen",
                    url: "#"
                  }
                ]
              }
            ]
          }
        ]
      },
      {
        text: "03 Gefährdungslage",
        subnav: [
          {
            text: "Aktuelle Gefährdungen",
            url: "#"
          },
          {
            text: "Bedrohungsentwicklung",
            url: "#"
          }
        ]
      },
      {
        text: "04 Schadwirkungen",
        subnav: [
          {
            text: "Auswirkungen auf Organisationen",
            url: "#"
          },
          {
            text: "Gesellschaftliche Auswirkungen",
            url: "#"
          }
        ]
      },
      {
        text: "05 Resilienz",
        subnav: [
          {
            text: "Schutzmaßnahmen",
            url: "#"
          },
          {
            text: "Wiederherstellung",
            url: "#"
          }
        ]
      }
    ]
  },
  {
    text: "Begriffe und Abkürzungen",
    subnav: [
      {
        text: "Abkürzungsverzeichnis",
        url: "#"
      },
      {
        text: "Glossar",
        url: "#"
      }
    ]
  }
];
