const initializedAttribute = "data-button-slide-initialized";

interface SlideElements {
  root: HTMLButtonElement;
  handle: HTMLElement;
  fill: HTMLElement;
}

function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}

function fallback(value: string | undefined, fallbackValue: string): string {
  if (value) {
    return value;
  }

  return fallbackValue;
}

function getThreshold(root: HTMLButtonElement): number {
  const threshold = Number(fallback(root.dataset.slideThreshold, "0.85"));

  return Number.isFinite(threshold) ? clamp(threshold, 0.5, 1) : 0.85;
}

function isCompleted(root: HTMLButtonElement): boolean {
  return (
    root.dataset.slideState === "success" ||
    root.classList.contains("is-success") ||
    Array.from(root.classList).some(className =>
      className.endsWith("button-slide--success")
    )
  );
}

function querySlideElements(root: HTMLButtonElement): SlideElements | null {
  const handle = root.querySelector<HTMLElement>("[data-slide-handle]");
  const fill = root.querySelector<HTMLElement>("[data-slide-fill]");

  if (!handle || !fill) {
    return null;
  }

  return { root, handle, fill };
}

function getMaxOffset({ root, handle }: SlideElements): number {
  return Math.max(root.clientWidth - handle.offsetWidth, 0);
}

function setOffset(elements: SlideElements, offset: number): void {
  const maxOffset = getMaxOffset(elements);
  const safeOffset = clamp(offset, 0, maxOffset);
  const progress = maxOffset ? safeOffset / maxOffset : 0;
  const rootWidth = elements.root.clientWidth;

  if (rootWidth > 0) {
    elements.root.style.setProperty(
      "--bsi-button-slide-width",
      `${rootWidth}px`
    );
    elements.root.style.setProperty("--button-slide-width", `${rootWidth}px`);
  }

  elements.root.style.setProperty(
    "--bsi-button-slide-offset",
    `${safeOffset}px`
  );
  elements.root.style.setProperty("--button-slide-offset", `${safeOffset}px`);
  elements.root.style.setProperty(
    "--bsi-button-slide-progress",
    `${progress * 100}%`
  );
  elements.root.style.setProperty(
    "--button-slide-progress",
    `${progress * 100}%`
  );
}

function setSuccess(elements: SlideElements): void {
  const successText = fallback(
    elements.root.dataset.slideSuccessText,
    "action successful"
  );

  elements.root.classList.add("is-success");
  elements.root.dataset.slideState = "success";
  elements.root.setAttribute("aria-pressed", "true");
  elements.root.setAttribute("aria-label", successText);
  setOffset(elements, getMaxOffset(elements));

  elements.root.dispatchEvent(
    new CustomEvent("button-slide:success", {
      bubbles: true,
      detail: { element: elements.root }
    })
  );
}

function resetSlide(elements: SlideElements): void {
  if (isCompleted(elements.root)) {
    return;
  }

  elements.root.classList.remove("is-dragging");
  setOffset(elements, 0);
}

function completeOrReset(elements: SlideElements, offset: number): void {
  const threshold = getThreshold(elements.root);
  const maxOffset = getMaxOffset(elements);
  const progress = maxOffset ? offset / maxOffset : 0;

  if (progress >= threshold) {
    setSuccess(elements);
    return;
  }

  resetSlide(elements);
}

function initButtonSlide(root: HTMLButtonElement): void {
  if (root.hasAttribute(initializedAttribute)) {
    return;
  }

  const elements = querySlideElements(root);

  if (!elements) {
    return;
  }

  let startX = 0;
  let currentOffset = 0;
  let isDragging = false;

  root.setAttribute(initializedAttribute, "true");
  setOffset(elements, isCompleted(root) ? getMaxOffset(elements) : 0);

  elements.handle.addEventListener("pointerdown", event => {
    if (isCompleted(root)) {
      return;
    }

    isDragging = true;
    startX = event.clientX;
    currentOffset = 0;
    root.classList.add("is-dragging");
    elements.handle.setPointerCapture(event.pointerId);
  });

  elements.handle.addEventListener("pointermove", event => {
    if (!isDragging) {
      return;
    }

    currentOffset = clamp(event.clientX - startX, 0, getMaxOffset(elements));
    setOffset(elements, currentOffset);
  });

  elements.handle.addEventListener("pointerup", event => {
    if (!isDragging) {
      return;
    }

    isDragging = false;
    elements.handle.releasePointerCapture(event.pointerId);
    completeOrReset(elements, currentOffset);
  });

  elements.handle.addEventListener("pointercancel", () => {
    isDragging = false;
    resetSlide(elements);
  });

  root.addEventListener("keydown", event => {
    if (isCompleted(root)) {
      return;
    }

    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      setSuccess(elements);
    }
  });
}

export function initButtonSlides(context: ParentNode = document): void {
  const slides = Array.from(
    context.querySelectorAll<HTMLButtonElement>(".js-button-slide")
  );

  if (
    context instanceof HTMLButtonElement &&
    context.matches(".js-button-slide")
  ) {
    slides.unshift(context);
  }

  slides.forEach(initButtonSlide);
}
