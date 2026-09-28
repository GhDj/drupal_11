export default function AutocompletePopover(): void {
  // const popover = document.getElementById("header-search");
  const popover = document.querySelector<HTMLElement>(
    "#header-search .bsi-grid"
  );

  if (!popover) {
    return;
  }

  const observer = new MutationObserver(() => {
    const menu = document.querySelector<HTMLElement>(
      ".search-api-autocomplete-search"
    );

    if (menu && menu.parentElement !== popover) {
      popover.appendChild(menu);
    }
  });

  observer.observe(document.body, {
    childList: true,
    subtree: true
  });
}
