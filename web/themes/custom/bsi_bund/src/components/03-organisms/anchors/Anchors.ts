const SELECTOR = "[data-bsi-anchors]";
const ACTIVE_CLASS = "bsi-anchors__link--active";
const ACTIVE_OFFSET = 100;
const DROPDOWN_TEXT_SELECTOR = ".bsi-dropdown__trigger .bsi-button__text";

// Sets the active state on the anchor link matching the given section ID.
function setActive(
  links: NodeListOf<HTMLAnchorElement>,
  activeId: string,
  dropdownText?: HTMLElement | null
): void {
  let activeLink: HTMLAnchorElement | undefined;

  links.forEach(link => {
    const isActive = link.getAttribute("href") === `#${activeId}`;

    link.classList.toggle(ACTIVE_CLASS, isActive);

    if (isActive && !activeLink) {
      activeLink = link;
    }
  });

  if (activeLink && dropdownText) {
    dropdownText.textContent = activeLink.textContent?.trim() ?? "";
  }
}

// Retrieves the current height of the sticky anchor navigation wrapper.
// The height is measured dynamically to account for wrapped navigation items.
function getStickyNavHeight(wrapper: HTMLElement): number {
  const pageGridAnchors = wrapper.closest<HTMLElement>(
    ".bsi-page-grid__anchors"
  );

  return pageGridAnchors?.offsetHeight ?? wrapper.offsetHeight;
}

// Initializes a single anchor navigation instance and sets up its event listeners.
function initAnchorsInstance(wrapper: HTMLElement): void {
  const links =
    wrapper.querySelectorAll<HTMLAnchorElement>(".bsi-anchors__link");

  const sections = Array.from(
    document.querySelectorAll<HTMLElement>("[id^='anchor-']")
  );

  const dropdownText = wrapper.querySelector<HTMLElement>(
    DROPDOWN_TEXT_SELECTOR
  );

  const [firstSection] = sections;

  if (!firstSection) {
    return;
  }

  // Updates the scroll margin of all sections based on the current
  // height of the sticky anchor navigation.
  const updateScrollMargin = (): number => {
    const stickyNavHeight = getStickyNavHeight(wrapper);

    sections.forEach(section => {
      section.style.scrollMarginTop = `${stickyNavHeight}px`;
    });

    return stickyNavHeight;
  };

  let ticking = false;

  // Determines the currently active section based on the sticky navigation position.
  const updateActiveSection = (): void => {
    const stickyNavHeight = updateScrollMargin();
    const activePosition = stickyNavHeight + ACTIVE_OFFSET;

    let activeSection = firstSection;

    for (const section of sections) {
      const top = section.getBoundingClientRect().top;

      if (top <= activePosition) {
        activeSection = section;
      } else {
        break;
      }
    }

    setActive(links, activeSection.id, dropdownText);
    ticking = false;
  };

  // Schedules an active section update using requestAnimationFrame to
  // prevent unnecessary calculations during continuous scrolling.
  const onScroll = (): void => {
    if (ticking) {
      return;
    }

    ticking = true;
    requestAnimationFrame(updateActiveSection);
  };

  // Recalculates the sticky navigation height and active section after
  // the viewport size changes
  const onResize = (): void => {
    updateScrollMargin();
    onScroll();
  };

  window.addEventListener("scroll", onScroll, {
    passive: true
  });

  window.addEventListener("resize", onResize);

  updateActiveSection();
}

// Initializes all anchor navigation instances
export function initAnchors(context: ParentNode = document): void {
  context.querySelectorAll<HTMLElement>(SELECTOR).forEach(wrapper => {
    if (wrapper.dataset.bsiAnchorsInitialized) {
      return;
    }

    wrapper.dataset.bsiAnchorsInitialized = "true";

    initAnchorsInstance(wrapper);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  initAnchors();
});

if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiAnchors = {
    attach(context: ParentNode): void {
      initAnchors(context);
    }
  };
}
