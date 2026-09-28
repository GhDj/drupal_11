/**
 * Renders the contact page
 */

// Globals
import { html } from "@globals/index";

// Components
import { pageGridTemplate } from "@templates/page-grid/page-grid.template";
import { formTemplate } from "@organisms/form/form.template";
import { blockTemplate } from "@organisms/block/block.template";
import { toolbarTemplate } from "@molecules/toolbar/toolbar.template";
import { bannerTemplate } from "@organisms/banner/banner.template";
import { breadcrumbTemplate } from "@molecules/breadcrumb/breadcrumb.template";

// Data
import contactFormData from "@organisms/form/form--contact.html?raw";

// Return the HTML string for the contact page
export function contactFormTemplate(): string {
  return html`
    ${pageGridTemplate({
      showStage: false,
      breadcrumb: breadcrumbTemplate({}),
      main: html`
        <!-- Toolbar -->
        ${toolbarTemplate({})}

        <!-- Intro text -->
        ${blockTemplate({
          blockTitle: "Kontaktieren Sie uns",
          blockContent:
            "Lorem ipsum dolor sit amet consectetur. Quis fermentum risus sed malesuada mauris tortor. Hendrerit sit eu aenean risus. Arcu praesent diam dolor magnis posuere. Euismod at tortor odio sed ultricies diam magna mi urna. Mattis etiam cursus arcu amet neque tempus sed laoreet.",
          contentIndented: true,
          headingLayout: 1
        })}

        <!-- Form -->
        ${formTemplate({
          formContent: contactFormData
        })}

        <!-- Banner -->
        ${bannerTemplate()}
      `
    })}
  `;
}
