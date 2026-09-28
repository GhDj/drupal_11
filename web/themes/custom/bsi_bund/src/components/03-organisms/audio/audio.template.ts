import { html, nsp } from "@globals/index";
import { cardTemplate } from "@molecules/card/card.template";

export interface AudioTemplateArgs {
  heading?: string;
  date?: string;
  text?: string;
  imageSrc?: string;
}

export function audioTemplate({
  heading = "Lorem Ipsum",
  date = "17.3.2026",
  text = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.",
  imageSrc = "demo-image-1_1.jpg"
}: AudioTemplateArgs = {}): string {
  const card =
    heading || text || imageSrc
      ? cardTemplate({
          additionalCardAttributes: { "data-theme": "blue-100" },
          cardExtraClasses: "card--small-image audio__card",
          cardImage: {
            imageSrc: imageSrc
          },
          cardImagePosition: "image-left",
          cardHeading: heading,
          cardTopline: date,
          cardText: text,
          cardCtaText: ""
        })
      : "";
  return html`
    <div class="${nsp("audio")}">
      <div class="${nsp("audio__content")}">
        ${card}
        <audio
          id="player"
          preload="auto"
          controls
          data-icon-path="dist/icons.svg"
        >
          <source
            type="audio/mpeg"
            src="src/demo-content/media/demo-audio.mp3"
          />
        </audio>
      </div>
    </div>
  `;
}
