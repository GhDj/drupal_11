// Initializes all interactive header behavior: main menu toggles, back buttons,
// and responsive switching between desktop (always-visible) and mobile (popover)
// layout modes for the search bar and main menu.
import {
  MenuBack,
  MenuCollapseL1,
  MenuCollapseL2
} from "@molecules/mainmenu/MainMenu";

export default function Header() {
  const mq: MediaQueryList = window.matchMedia("(min-width: 992px)");

  document
    .querySelectorAll<HTMLButtonElement>('[data-js-mainmenu-toggle="l1"]')
    .forEach(toggle => new MenuCollapseL1(toggle));
  document
    .querySelectorAll<HTMLButtonElement>('[data-js-mainmenu-toggle="l2"]')
    .forEach(toggle => new MenuCollapseL2(toggle));
  document
    .querySelectorAll<HTMLButtonElement>("[data-js-mainmenu-back]")
    .forEach(button => new MenuBack(button));

  // Initialize mobile menus
  mq.addEventListener("change", updateMobileMenus);
  updateMobileMenus();

  function updateMobileMenus(): void {
    // Reset open submenus to avoid stale state across breakpoints.
    MenuCollapseL2.closeAll();
    MenuCollapseL1.closeAll();

    const mobileMenus = document.querySelectorAll<HTMLElement>("#mainmenu");
    const menuToggle = document.querySelector<HTMLElement>(
      `[popovertarget="mainmenu"]`
    );

    menuToggle?.addEventListener("click", () => {
      MenuCollapseL1.closeAll();
    });

    mobileMenus.forEach(menu => {
      if (mq.matches) {
        // Desktop: always visible, remove from popover layer.
        if (menu.hasAttribute("popover")) {
          menu.showPopover();
          menu.removeAttribute("popover");
        }
      } else {
        // Mobile: activate popover, start hidden.
        if (!menu.hasAttribute("popover")) {
          menu.setAttribute("popover", "");
        }

        menu.hidePopover();
      }
    });

    moveMetanav();
  }

  // Moves the rendered metanav <nav> between the desktop slot and the mobile mainmenu popover
  function moveMetanav(): void {
    const metanav = document.querySelector<HTMLElement>(".bsi-metanav");
    const mainmenu = document.getElementById("mainmenu");
    const desktopSlot = document.querySelector<HTMLElement>(
      ".bsi-header__meta-navigation"
    );

    if (!metanav || !mainmenu || !desktopSlot) return;

    const target = mq.matches ? desktopSlot : mainmenu;
    if (metanav.parentElement !== target) {
      target.appendChild(metanav);
    }
  }
}
