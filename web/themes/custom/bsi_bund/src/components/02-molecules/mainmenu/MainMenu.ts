const OPEN_CLASS = "bsi-mainmenu__panel--open";

// Base class for all menu collapse components; handles open/close state, aria-expanded, and event wiring
abstract class MainMenuComponent {
  protected toggle: HTMLButtonElement;
  protected menu: HTMLElement;
  protected isOpen = false;

  // Resolves the controlled panel via aria-controls and calls init()
  constructor(toggle: HTMLButtonElement) {
    this.toggle = toggle;

    const menuId = toggle.getAttribute("aria-controls");
    const menuEl = menuId ? document.getElementById(menuId) : null;

    if (!menuEl) throw new Error("Menu element not found");

    this.menu = menuEl;
    this.init();
  }

  // Attaches click and focusout listeners to the toggle and panel
  protected init() {
    // Stop propagation so the document-click handler doesn't immediately close the panel
    this.toggle.addEventListener("click", e => {
      e.stopPropagation();
      this.toggleMenu();
    });

    // Close when focus leaves the panel
    this.menu.addEventListener("focusout", this.handleFocusOut);
  }

  // Delegates to open() or close() based on current state
  protected toggleMenu() {
    if (this.isOpen) {
      this.close();
    } else {
      this.open();
    }
  }

  // Shows the panel and updates aria-expanded
  protected open() {
    this.menu.hidden = false;
    this.menu.classList.add(OPEN_CLASS);
    this.toggle.setAttribute("aria-expanded", "true");
    this.isOpen = true;
  }

  // Hides the panel and updates aria-expanded
  public close() {
    this.menu.hidden = true;
    this.menu.classList.remove(OPEN_CLASS);
    this.toggle.setAttribute("aria-expanded", "false");
    this.isOpen = false;
  }

  // Closes when focus moves outside both the panel and its toggle (e.g. tab away)
  protected handleFocusOut = (e: FocusEvent) => {
    const related = e.relatedTarget as HTMLElement | null;

    // Both the panel and toggle are considered "inside" to allow keyboard navigation between them
    if (!this.menu.contains(related) && !this.toggle.contains(related)) {
      this.close();
    }
  };

  // Closes on desktop when the user clicks outside the header entirely
  protected handleDocumentClick = (e: MouseEvent) => {
    const mq: MediaQueryList = window.matchMedia("(min-width: 992px)");
    const target = e.target as HTMLElement;
    const header = document.getElementById("header");

    // On mobile the popover handles dismissal, so only act on desktop
    if (mq.matches && !header?.contains(target)) this.close();
  };
}

// Controls a top-level (L1) nav item that opens an L2 panel; ensures only one L1 is open at a time
export class MenuCollapseL1 extends MainMenuComponent {
  private static instances: MenuCollapseL1[] = [];
  private handleResize = () => this.updateLevel3Offset();

  // Registers this instance in the shared list for closeAll/closeFor coordination
  constructor(toggle: HTMLButtonElement) {
    super(toggle);
    MenuCollapseL1.instances.push(this);
  }

  // Closes all other L1 panels first, then opens this one and starts tracking resize
  protected override open() {
    MenuCollapseL1.closeAll();
    super.open();
    document.addEventListener("click", this.handleDocumentClick);
    window.addEventListener("resize", this.handleResize);
    this.updateLevel3Offset();
  }

  // Also closes any open L2 panels nested inside before closing itself
  public override close() {
    // close any level 3 panels nested in this level panel
    this.menu
      .querySelectorAll<HTMLButtonElement>(
        '[data-js-mainmenu-toggle="l2"][aria-expanded="true"]'
      )
      .forEach(btn => MenuCollapseL2.closeInstanceFor(btn));

    document.removeEventListener("click", this.handleDocumentClick);
    window.removeEventListener("resize", this.handleResize);
    super.close();
  }

  // Positions the L3 column by calculating the right edge of the L2 submenu list
  private updateLevel3Offset() {
    if (!window.matchMedia("(min-width: 992px)").matches) return;

    const submenu = this.menu.querySelector<HTMLElement>(
      ".bsi-mainmenu__submenu"
    );

    if (!submenu) return;

    // Set the CSS custom property used by the L3 panel to align with the submenu's right edge
    this.menu.style.setProperty(
      "--mainmenu-l3-left",
      `${submenu.offsetLeft + submenu.offsetWidth + 40}px`
    );
  }

  // Closes all registered L1 instances (called on breakpoint change or mobile menu open)
  static closeAll() {
    MenuCollapseL1.instances.forEach(instance => instance.close());
  }

  // Closes only the instance bound to the given toggle button
  static closeFor(toggle: HTMLButtonElement) {
    // Matched by toggle reference so back-button navigation closes the correct panel
    MenuCollapseL1.instances
      .filter(instance => instance.toggle === toggle)
      .forEach(instance => instance.close());
  }
}

// Controls an L2 toggle that opens an L3 panel; ensures only one L3 is open per L2 parent
export class MenuCollapseL2 extends MainMenuComponent {
  private static instances: MenuCollapseL2[] = [];
  private parentPanel: HTMLElement | null;

  // Stores a reference to the enclosing L2 panel for sibling-close and focusout containment
  constructor(toggle: HTMLButtonElement) {
    super(toggle);
    this.parentPanel = toggle.closest<HTMLElement>(
      '[data-js-mainmenu-panel="l2"]'
    );

    // Register globally so closeAll() and closeInstanceFor() can reach this instance
    MenuCollapseL2.instances.push(this);
  }

  // Closes sibling L3 panels in the same L2 parent before opening this one
  protected override open() {
    // Close sibling level 3 panels within the same level 2 parent panel
    MenuCollapseL2.instances.forEach(instance => {
      if (instance !== this && instance.parentPanel === this.parentPanel) {
        instance.close();
      }
    });
    super.open();
  }

  // Widens focusout containment to the L2 panel so shift-tab back into the list doesn't close L3
  protected override handleFocusOut = (e: FocusEvent) => {
    const related = e.relatedTarget as HTMLElement | null;
    const container = this.parentPanel ?? this.menu;

    // Use the L2 parent as the boundary so focus can move freely within it
    if (!container.contains(related)) {
      this.close();
    }
  };

  // Closes the instance bound to the given toggle (used by L1 close cascade)
  static closeInstanceFor(toggle: HTMLButtonElement) {
    MenuCollapseL2.instances
      .filter(instance => instance.toggle === toggle)
      .forEach(instance => instance.close());
  }

  // Closes all L2 instances (called on breakpoint change)
  static closeAll() {
    MenuCollapseL2.instances.forEach(instance => instance.close());
  }
}

// Handles the mobile back button inside an L2 panel
// closes the panel and returns focus to the L1 toggle
export class MenuBack {
  private button: HTMLButtonElement;

  // Registers the click handler on the back button
  constructor(button: HTMLButtonElement) {
    this.button = button;
    this.button.addEventListener("click", this.handleClick);
  }

  // Closes the parent L1 panel and moves focus back to its toggle
  private handleClick = (e: MouseEvent) => {
    e.stopPropagation();

    // Walk up to the enclosing L2 panel to identify which L1 panel owns this back button
    const panel = this.button.closest<HTMLElement>(
      '[data-js-mainmenu-panel="l2"]'
    );
    if (!panel?.id) return;

    // Find the L1 toggle that controls this L2 panel by matching aria-controls
    const l1Toggle = document.querySelector<HTMLButtonElement>(
      `[data-js-mainmenu-toggle="l1"][aria-controls="${panel.id}"]`
    );
    if (!l1Toggle) return;

    // Close the L1 panel (which also closes nested L2 panels via the cascade)
    MenuCollapseL1.closeFor(l1Toggle);

    // Return keyboard focus to the L1 toggle so the user stays in the nav flow
    l1Toggle.focus();
  };
}
