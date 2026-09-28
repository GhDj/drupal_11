interface TimelineProgressParams {
  progress: number;
  viewportHeight: number;
  timelineHeight: number;
}

interface TimelineBounds {
  top: number;
  bottom: number;
  height: number;
}

const MIN_PROGRESS = 0;
const MAX_PROGRESS = 1;

// Near-1.0 avoids floating-point imprecision when the timeline fills the viewport
const FULLY_VISIBLE_THRESHOLD = 0.995;

// Range that caps how far the line advances relative to raw scroll; keeps it from visually outrunning content
const MIN_PROGRESS_SCALE = 0.88;
const MAX_PROGRESS_SCALE = 0.99;

// How far behind scroll position the line is allowed to trail; higher lag for smaller viewports
const MAX_PROGRESS_LAG = 0.08;
const MIN_PROGRESS_LAG = 0.01;

// Normalized progress threshold at which catch-up acceleration begins
const MIN_ACCELERATION_START = 0.92;
const MAX_ACCELERATION_START = 0.985;

// Power-curve exponent for the end snap; higher = sharper catch-up for smaller viewports
const MIN_ACCELERATION_EXPONENT = 2.4;
const MAX_ACCELERATION_EXPONENT = 1.5;

// Extra scroll room (px) so the line fully completes before reaching the page bottom
const TIMELINE_END_OFFSET = 40;

const clamp = (value: number, min: number, max: number): number =>
  Math.min(Math.max(value, min), max);

const lerp = (factor: number, start: number, end: number): number =>
  start + (end - start) * factor;

const getTimelineBounds = (
  element: Element,
  scrollTop: number
): TimelineBounds => {
  const { top, height } = element.getBoundingClientRect();

  // Converts getBoundingClientRect (viewport-relative) to document-absolute coordinates
  return {
    top: scrollTop + top,
    bottom: scrollTop + top + height,
    height
  };
};

const isVisibleInViewport = (
  viewportTop: number,
  viewportBottom: number,
  elementTop: number,
  elementBottom: number
): boolean => viewportBottom > elementTop && viewportTop < elementBottom;

const calculateRawProgress = ({
  scrollTop,
  viewportHeight,
  documentTop,
  documentBottom
}: {
  scrollTop: number;
  viewportHeight: number;
  documentTop: number;
  documentBottom: number;
}): number => {
  // Animation starts when the timeline enters the viewport's vertical midpoint
  const viewportCenter = viewportHeight / 2;

  const startPosition = Math.max(0, documentTop - viewportCenter);

  // Scroll position where progress should reach 100%
  const endPosition = documentBottom - viewportHeight + TIMELINE_END_OFFSET;

  // Prevents division by zero for degenerate or zero-height layouts
  const safeEndPosition = Math.max(endPosition, startPosition + 1);

  return (scrollTop - startPosition) / (safeEndPosition - startPosition);
};

const calculateTimelineProgress = ({
  progress,
  viewportHeight,
  timelineHeight
}: TimelineProgressParams): number => {
  const normalizedProgress = clamp(progress, MIN_PROGRESS, MAX_PROGRESS);

  if (!timelineHeight || !viewportHeight) {
    return normalizedProgress;
  }

  // Fraction of the timeline that fits in the viewport, clamped to [0, 1]
  const viewportRatio = clamp(
    viewportHeight / timelineHeight,
    MIN_PROGRESS,
    MAX_PROGRESS
  );

  // Timeline fits entirely in viewport — no lag or acceleration adjustment needed
  if (viewportRatio >= FULLY_VISIBLE_THRESHOLD) {
    return normalizedProgress;
  }

  // Interpolated cap on maximum progress reached before the acceleration phase
  const progressScale = lerp(
    viewportRatio,
    MIN_PROGRESS_SCALE,
    MAX_PROGRESS_SCALE
  );

  // Maximum distance the line trails behind raw scroll progress
  const progressLag = lerp(viewportRatio, MAX_PROGRESS_LAG, MIN_PROGRESS_LAG);

  const scaledProgress = normalizedProgress * progressScale;

  // Keeps the line from moving backward while allowing it to lag behind scroll
  let adjustedProgress = Math.min(
    Math.max(scaledProgress, normalizedProgress - progressLag),
    normalizedProgress
  );

  // Threshold after which the progress line accelerates to catch up
  const accelerationStart = lerp(
    viewportRatio,
    MIN_ACCELERATION_START,
    MAX_ACCELERATION_START
  );

  if (normalizedProgress < accelerationStart) {
    return clamp(adjustedProgress, MIN_PROGRESS, MAX_PROGRESS);
  }

  // Position within the acceleration zone normalized to [0, 1]
  const endProgress = clamp(
    (normalizedProgress - accelerationStart) / (1 - accelerationStart),
    MIN_PROGRESS,
    MAX_PROGRESS
  );

  // Steeper for smaller viewports so the snap to completion stays sharp
  const exponent = lerp(
    viewportRatio,
    MIN_ACCELERATION_EXPONENT,
    MAX_ACCELERATION_EXPONENT
  );

  // Power-curve easing produces the smooth end-of-timeline snap
  const easedProgress = Math.pow(endProgress, exponent);

  // Blends lagged progress toward 1 using the eased tail fraction
  const acceleratedProgress =
    adjustedProgress + (1 - adjustedProgress) * easedProgress;

  // Cap at normalizedProgress before the threshold to prevent overshooting
  adjustedProgress =
    normalizedProgress < FULLY_VISIBLE_THRESHOLD
      ? Math.min(acceleratedProgress, normalizedProgress)
      : Math.max(acceleratedProgress, normalizedProgress);

  return clamp(adjustedProgress, MIN_PROGRESS, MAX_PROGRESS);
};

const updateProgressLine = (
  timeline: Element,
  viewportHeight: number,
  scrollTop: number,
  viewportBottom: number
): void => {
  const progressLine = timeline.querySelector<HTMLElement>(
    ".bsi-timeline__progress"
  );

  // Guard: some timelines may lack the progress element (e.g. not yet in DOM)
  if (!progressLine) {
    return;
  }

  const {
    top: documentTop,
    bottom: documentBottom,
    height: timelineHeight
  } = getTimelineBounds(timeline, scrollTop);

  // Skip hidden or not-yet-laid-out timelines with no height
  if (!timelineHeight) {
    return;
  }

  const isVisible = isVisibleInViewport(
    scrollTop,
    viewportBottom,
    documentTop,
    documentBottom
  );

  if (!isVisible) {
    // Reset to zero when the timeline is fully scrolled out of view
    progressLine.style.height = "0%";
    return;
  }

  const rawProgress = calculateRawProgress({
    scrollTop,
    viewportHeight,
    documentTop,
    documentBottom
  });

  // Timelines shorter than the viewport are always fully visible, so progress is 1
  const progress =
    documentBottom < viewportHeight ? 1 : clamp(rawProgress, 0, 1);

  const visibleProgress = calculateTimelineProgress({
    progress,
    viewportHeight,
    timelineHeight
  });

  progressLine.style.height = `${visibleProgress * 100}%`;
};

export default function Timeline(): void {
  if (typeof window === "undefined") {
    return;
  }

  const timelines = Array.from(document.querySelectorAll(".bsi-timeline"));

  if (!timelines.length) {
    return;
  }

  // Coalesces rapid scroll/resize events so at most one rAF is queued per frame
  let animationFramePending = false;

  const updateTimelines = (): void => {
    animationFramePending = false;

    const viewportHeight = window.innerHeight || 0;

    // pageYOffset is the legacy alias for scrollY in older browsers
    const scrollTop = window.scrollY || window.pageYOffset || 0;

    const viewportBottom = scrollTop + viewportHeight;

    timelines.forEach(timeline =>
      updateProgressLine(timeline, viewportHeight, scrollTop, viewportBottom)
    );
  };

  const scheduleUpdate = (): void => {
    if (animationFramePending) {
      return;
    }

    animationFramePending = true;

    requestAnimationFrame(updateTimelines);
  };

  // Passive listener lets the browser scroll without waiting for this handler to finish
  window.addEventListener("scroll", scheduleUpdate, { passive: true });

  window.addEventListener("resize", scheduleUpdate);

  // Graceful degradation for browsers without ResizeObserver support
  const resizeObserver =
    typeof ResizeObserver !== "undefined"
      ? new ResizeObserver(scheduleUpdate)
      : null;

  timelines.forEach(timeline => {
    resizeObserver?.observe(timeline);
  });

  // Trigger an initial render without waiting for the first scroll or resize event
  scheduleUpdate();
}
