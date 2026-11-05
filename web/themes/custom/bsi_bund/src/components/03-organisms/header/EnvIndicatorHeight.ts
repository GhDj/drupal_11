/**
 * Sets the CSS custom property --environment-indicator-height
 * based on the current height of the Drupal environment indicator.
 */
export function initEnvironmentIndicator(): void {
  const indicator = document.getElementById("environment-indicator");

  if (!indicator) {
    document.documentElement.style.setProperty(
      "--environment-indicator-height",
      "0px"
    );

    return;
  }

  const update = (): void => {
    document.documentElement.style.setProperty(
      "--environment-indicator-height",
      `${indicator.offsetHeight}px`
    );
  };

  // initial value
  update();

  // react to height changes
  const observer = new ResizeObserver(update);
  observer.observe(indicator);
}
