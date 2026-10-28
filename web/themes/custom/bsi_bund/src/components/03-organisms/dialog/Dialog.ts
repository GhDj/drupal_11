declare const Drupal: {
  t: (text: string) => string;
  dialog: (element: HTMLElement) => {
    close: () => void;
  };
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

    if (closeButton) {
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
    }

    // Reposition chart dialogs after content size changes.
    if (!dialog.classList.contains("bsi-chart-dialog")) return;

    //close dialog, one click not in dialog
    const overlay = document.querySelector<HTMLElement>(".ui-widget-overlay");
    overlay?.addEventListener("click", () => {
      Drupal.dialog(element).close();
    });

    element.addEventListener("click", clickEvent => {
      const target = clickEvent.target;

      if (!(target instanceof Element)) return;

      const link = target.closest<HTMLAnchorElement>('a[href^="#"]');
      if (!link) return;

      const hash = link.hash;
      if (!hash || hash === "#") return;

      const targetId = decodeURIComponent(hash.slice(1));
      const anchorTarget = document.getElementById(targetId);

      if (!anchorTarget || dialog.contains(anchorTarget)) return;

      clickEvent.preventDefault();

      // Close the dialog before navigating to the anchor.
      const handleAnchorClose = (closeEvent: Event): void => {
        if (closeEvent.target !== element) return;

        window.removeEventListener("dialog:afterclose", handleAnchorClose);

        requestAnimationFrame(() => {
          history.pushState(null, "", hash);

          anchorTarget.scrollIntoView({
            behavior: "smooth",
            block: "start"
          });

          // Move keyboard focus to the target.
          if (!anchorTarget.hasAttribute("tabindex")) {
            anchorTarget.setAttribute("tabindex", "-1");
            anchorTarget.addEventListener(
              "blur",
              () => anchorTarget.removeAttribute("tabindex"),
              { once: true }
            );
          }

          anchorTarget.focus({ preventScroll: true });
        });
      };

      window.addEventListener("dialog:afterclose", handleAnchorClose);

      Drupal.dialog(element).close();
    });

    let frame = 0;

    const reposition = (): void => {
      cancelAnimationFrame(frame);

      frame = requestAnimationFrame(() => {
        if (!dialog.isConnected) return;

        const rect = dialog.getBoundingClientRect();
        const top = Math.max(0, (window.innerHeight - rect.height) / 2);

        dialog.style.top = `${top}px`;
      });
    };

    // Observe the content instead of the dialog wrapper
    // to avoid unnecessary resize loops.
    const observer = new ResizeObserver(reposition);
    observer.observe(element);

    // Initial positioning.
    reposition();

    // Disconnect when the dialog is closed.
    const handleClose = (closeEvent: Event): void => {
      if (closeEvent.target !== element) return;

      observer.disconnect();
      cancelAnimationFrame(frame);

      window.removeEventListener("dialog:afterclose", handleClose);
    };

    window.addEventListener("dialog:afterclose", handleClose);
  });
};

export default Dialog;
