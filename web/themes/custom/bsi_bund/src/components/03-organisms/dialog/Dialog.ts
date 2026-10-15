declare const Drupal: {
  t: (text: string) => string;
};

const Dialog = (): void => {
  window.addEventListener("dialog:aftercreate", event => {
    const element = event.target;

    if (!(element instanceof HTMLElement)) return;
    if (!element.matches(".ui-dialog-content")) return;

    const dialog = element.closest<HTMLElement>(".ui-dialog");
    if (!dialog) return;

    const titlebar = dialog.querySelector(".ui-dialog-titlebar");
    const closeButton = dialog.querySelector<HTMLButtonElement>(
      ".ui-dialog-titlebar-close"
    );

    // Remove unnecessary jQuery UI classes.
    dialog.classList.remove("ui-helper-clearfix");
    titlebar?.classList.remove("ui-helper-clearfix");
    closeButton?.classList.remove("ui-button-icon-only");

    if (!closeButton) return;

    // Translate the close button label.
    const label = Drupal.t("Close");

    closeButton.title = label;
    closeButton.setAttribute("aria-label", label);

    // Replace the button text while preserving the existing icon.
    const textNode = [...closeButton.childNodes].find(
      node => node.nodeType === Node.TEXT_NODE && node.textContent?.trim()
    );

    if (textNode) {
      textNode.textContent = label;
    }
  });
};

export default Dialog;
