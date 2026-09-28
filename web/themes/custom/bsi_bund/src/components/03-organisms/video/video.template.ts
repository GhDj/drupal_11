import { html, nsp } from "@globals/index";

export function videoTemplate(): string {
  return html`
    <div class="${nsp("video")}">
      <video
        controls
        poster="src/demo-content/images/demo-image-16_9.jpg"
        data-icon-path="dist/icons.svg"
        preload="none"
      >
        <source
          src="src/demo-content/media/init-demo-video.mp4"
          type="video/mp4"
        />
        <track
          default
          kind="captions"
          srclang="de"
          label="English"
          src="/example.vtt"
        />
      </video>
      <div class="${nsp("video__text")}">
        <p>
          Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam
          nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat,
          sed diam voluptua. At vero eos et accusam et justo duo dolores et ea
          rebum.
        </p>
      </div>
    </div>
  `;
}
