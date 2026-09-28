/**
 * Renders the page grid layouts
 *
 */

// Globals
import { nsp, html } from "@globals/index";

// Components
import { headerTemplate } from "@organisms/header/header.template";
import { footerTemplate } from "@organisms/footer/footer.template";
import { defaultFooterArgs } from "@organisms/footer/footer.constants";
import { placeholderTemplate } from "@base/placeholder/placeholder.template";
import { sidemenuTemplate } from "@molecules/sidemenu/sidemenu.template";

// Arguments accepted by the template
export interface PageGridTemplateArgs {
  showStage: boolean;
  showAnchors?: boolean;
  showBreadcrumb?: boolean;
  showSidemenu?: boolean;
  stage?: string;
  breadcrumb?: string;
  anchors?: string;
  main?: string;
}

// Return the HTML string for the Hub page
export function pageGridTemplate({
  showStage,
  showAnchors,
  showBreadcrumb = true,
  showSidemenu = false,
  stage: stageSlot,
  breadcrumb: breadcrumbSlot,
  anchors: anchorsSlot,
  main: mainSlot
}: PageGridTemplateArgs): string {
  // Render the stage slot, or a placeholder if not provided
  const stage = showStage
    ? `<div class="${nsp("page-grid__stage")}">${stageSlot ?? placeholderTemplate({ label: "Stage" })}</div>`
    : "";

  // Render the breadcrumb slot, or a placeholder if not provided
  const breadcrumb = showBreadcrumb
    ? `<div class="${nsp("page-grid__breadcrumb")}">${breadcrumbSlot ?? placeholderTemplate({ label: "Breadcrumb" })}</div>`
    : "";

  const sidemenu = showSidemenu ? sidemenuTemplate() : "";

  // Render the anchors slot
  const anchors = showAnchors
    ? `<div class="${nsp("page-grid__anchors")}">${anchorsSlot ?? placeholderTemplate({ label: "Anchors" })}</div>`
    : "";

  // Render the main content slot, or a placeholder if not provided
  const mainSlotContent =
    mainSlot ?? `${placeholderTemplate({ label: "Main Content" })}`;

  return html`
    <div class="${nsp("page-grid")}">
      <!-- Header-->
      ${headerTemplate({})}

      <!-- Breadcrumb -->
      ${breadcrumb}

      <!-- Sidemenu -->
      ${sidemenu}

      <!-- Stage -->
      ${stage}

      <!-- Anchors -->
      ${anchors}

      <!-- Main Content -->
      <main class="${nsp("page-grid__main")}">${mainSlotContent}</main>

      <!-- Footer -->
      ${footerTemplate(defaultFooterArgs)}
    </div>
  `;
}
