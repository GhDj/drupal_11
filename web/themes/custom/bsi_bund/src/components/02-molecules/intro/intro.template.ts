import { html, nsp } from "@globals/index";

export function introTemplate(): string {
  return html`
    <section class="${nsp("intro", "grid")}">
      <div class="${nsp("intro__content")}">
        <div class="${nsp("intro__lead")}">
          <p>
            Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam
            nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam
            erat, sed diam
          </p>
        </div>

        <div class="${nsp("intro__body")}">
          <p>
            Have you ever begun creating your designs while still waiting for
            your clients to send the content? If yes, then you’re surely
            familiar with lorem ipsum! You know, the hassle of constantly
            swapping windows just to copy and paste your lorem ipsum text into
            Bricks? What if you could skip that whole step? Imagine having smart
            placeholder text right where you need it, built right into your
            design tool. No more tab-switching, no more copy-paste chaos – just
            seamless workflow.
          </p>
        </div>
      </div>
    </section>
  `;
}
