import type { DownloadsItem } from "./downloads.template";

export const defaultDownloadsItems: DownloadsItem[] = [
  {
    title: "Kunstliche Intelligenz sicher nutzen",
    text: "Kunstliche Intelligenz (KI) begegnet uns immer ofter im Alltag. Auf Ihrem Smartphone sind Sie vermutlich bereits mit KI in Beruhrung gekommen: Wenn Sie das Smartphone mundlich bitten, einen Wecker zu stellen, oder Sie den Bildschirm mithilfe der Gesichtserkennung entsperren, ist KI im Spiel. In dieser Broschure geben wir elf Handlungsempfehlungen fur eine moglichst sichere Nutzung von Kunstlicher Intelligenz. Dabei konzentrieren wir uns auf Anwendungen, die Sie womoglich bereits verwenden.",
    media: {
      type: "icon",
      iconName: "download",
      iconTitle: "Download"
    },
    downloadLink: {
      text: "PDF, 1MB herunterladen",
      url: "#",
      iconName: "download"
    },
    orderLink: {
      text: "Druckausgabe bestellen",
      url: "#"
    }
  }
];
