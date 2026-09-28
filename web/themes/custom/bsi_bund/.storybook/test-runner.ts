import { waitForPageReady, getStoryContext } from "@storybook/test-runner";
import type { TestRunnerConfig } from "@storybook/test-runner";
import { toMatchImageSnapshot } from "jest-image-snapshot";
import type { Page } from "playwright";
import { getTestViewports } from "./viewports.ts";

declare const expect: jest.Expect;

const customSnapshotsDir = `${process.cwd()}/__snapshots__`;

// Get viewports from shared configuration
const viewports = getTestViewports();
type Viewport = (typeof viewports)[number];

// Viewport tags accepted on stories and on UrlSnapshot entries.
// Kept as a union so a typo (e.g. "mobil") fails at compile time instead of
// silently producing zero snapshots for that entry.
type ViewportTag = "desktop" | "laptop" | "tablet" | "mobile";

interface UrlSnapshot {
  id: string;
  // Storybook args string, e.g. "variant:sidebar-left;showStage:true".
  // Applied on top of the story's default args via the iframe URL.
  args: string;
  viewports?: ViewportTag[];
}

const TAG_TO_VIEWPORT_NAME: Record<ViewportTag, Viewport["name"]> = {
  desktop: "desktop",
  laptop: "laptop",
  tablet: "tablet",
  mobile: "smartphone"
};

/**
 * Resolve viewport tags to viewport configs.
 *
 * - `no-snapshot` short-circuits to an empty list.
 * - `all-viewports` returns every configured viewport.
 * - When no viewport tag is present and `defaultAll` is true, returns all
 *   viewports (used for the base story loop). When false, returns an empty
 *   list (used for urlSnapshots entries where callers fall back themselves).
 */
function resolveViewports(
  tags: readonly string[],
  { defaultAll }: { defaultAll: boolean }
): Viewport[] {
  if (tags.includes("no-snapshot")) {
    return [];
  }
  if (tags.includes("all-viewports")) {
    return viewports;
  }
  const wanted = new Set<Viewport["name"]>();
  for (const tag of tags) {
    const name = TAG_TO_VIEWPORT_NAME[tag as ViewportTag];
    if (name) wanted.add(name);
  }
  if (wanted.size === 0) {
    return defaultAll ? viewports : [];
  }
  return viewports.filter(v => wanted.has(v.name));
}

async function preparePageForSnapshot(page: Page) {
  await page.mouse.move(-100, -100);
  await page.waitForTimeout(500);
}

// Inject once per page navigation, not once per viewport, so style tags don't
// stack and DOM mutation doesn't fire after the "rendered" phase check.
async function injectSnapshotStyles(page: Page) {
  await page.addStyleTag({
    content: [
      "* { transition: none !important; animation: none !important; }",
      "html, body, * { -webkit-font-smoothing: antialiased !important; -moz-osx-font-smoothing: grayscale !important; text-rendering: geometricPrecision !important; }",
      ".frc-captcha { visibility: hidden !important; height: 66px !important; }"
    ].join("\n")
  });
}

// Waits for the Storybook story to finish rendering without requiring networkidle.
// waitForPageReady() calls networkidle which hangs when the story loads proxied
// external resources (e.g. images from din.de). Instead we wait for the
// Storybook preview to signal that the current story has rendered.
async function waitForStoryReady(page: Page) {
  await page.waitForLoadState("load");
  const result = await page.waitForFunction(
    () => {
      const preview = (
        globalThis as unknown as {
          __STORYBOOK_PREVIEW__?: {
            currentRender?: { phase?: string };
          };
        }
      ).__STORYBOOK_PREVIEW__;
      const phase = preview?.currentRender?.phase;
      if (phase === "errored" || phase === "aborted") return "failed";
      if (phase === "rendered" || phase === "completed" || phase === "finished")
        return "ok";
      return false;
    },
    { timeout: 10000 }
  );
  const value = await result.jsonValue();
  if (value === "failed") {
    throw new Error("Storybook story render failed (phase: errored/aborted)");
  }
  await page.evaluate(() => document.fonts.ready);
}

async function captureAndCompare(
  page: Page,
  snapshotId: string,
  label: string,
  failures: Array<{ label: string; error: Error }>
) {
  await preparePageForSnapshot(page);
  const image = await page.screenshot({ fullPage: true });
  try {
    expect(image).toMatchImageSnapshot({
      customSnapshotsDir,
      customSnapshotIdentifier: snapshotId,
      failureThreshold: 0.0002, // 0.02 % of pixels
      failureThresholdType: "percent",
      customDiffConfig: {
        threshold: 0.1, // pixelmatch per-pixel color sensitivity
        includeAA: false // ignore anti-aliased pixels
      },
      storeReceivedOnFailure: true,
      customReceivedDir: `${customSnapshotsDir}/__received__`
    });
  } catch (error) {
    failures.push({ label, error: error as Error });
  }
}

const config: TestRunnerConfig = {
  setup() {
    expect.extend({ toMatchImageSnapshot });
  },
  async postVisit(page, context) {
    const storyContext = await getStoryContext(page, context);
    const storyTags = storyContext.tags || [];
    const viewportsToTest = resolveViewports(storyTags, { defaultAll: true });

    if (viewportsToTest.length === 0) {
      return;
    }

    await waitForPageReady(page);
    await injectSnapshotStyles(page);

    const failures: Array<{ label: string; error: Error }> = [];

    // Base story: one snapshot per viewport
    for (const viewport of viewportsToTest) {
      await page.setViewportSize({
        width: viewport.width,
        height: viewport.height
      });
      await waitForPageReady(page);
      await captureAndCompare(
        page,
        `${context.id}-${viewport.name}`,
        viewport.name,
        failures
      );
    }

    // urlSnapshots: re-run the story under different args by swapping the
    // iframe URL's args= param. Original globals/decorator query params are
    // preserved so themed snapshots match the base loop.
    const urlSnapshots = storyContext.parameters?.["urlSnapshots"] as
      UrlSnapshot[] | undefined;

    if (urlSnapshots?.length) {
      const currentUrl = new URL(page.url());
      const iframeBase = `${currentUrl.origin}${currentUrl.pathname}`;
      const storyId = currentUrl.searchParams.get("id") ?? context.id;

      const seenIds = new Set<string>();
      for (const entry of urlSnapshots) {
        if (seenIds.has(entry.id)) {
          failures.push({
            label: `url:${entry.id}`,
            error: new Error(
              `Duplicate urlSnapshots id "${entry.id}" — snapshot identifiers would collide`
            )
          });
          continue;
        }
        seenIds.add(entry.id);

        // Preserve every original query param except id/args/viewMode, which
        // we set explicitly. This keeps globals=theme:dark and similar state.
        const params = new URLSearchParams(currentUrl.search);
        params.delete("id");
        params.delete("args");
        params.delete("viewMode");
        params.set("id", storyId);
        params.set("args", entry.args);
        params.set("viewMode", "story");
        const targetUrl = `${iframeBase}?${params.toString()}`;

        const entryViewports =
          entry.viewports && entry.viewports.length > 0
            ? resolveViewports(entry.viewports, { defaultAll: false })
            : viewportsToTest;

        try {
          await page.goto(targetUrl, { waitUntil: "domcontentloaded" });
          await waitForStoryReady(page);
          await injectSnapshotStyles(page);
        } catch (error) {
          failures.push({
            label: `url:${entry.id}`,
            error: error as Error
          });
          continue;
        }

        for (const viewport of entryViewports) {
          await page.setViewportSize({
            width: viewport.width,
            height: viewport.height
          });
          // Storybook's render phase is set on mount, not on resize, so
          // waitForStoryReady would return on stale state. Rely on a short
          // settle inside preparePageForSnapshot instead.
          await captureAndCompare(
            page,
            `${context.id}-url-${entry.id}-${viewport.name}`,
            `url:${entry.id}/${viewport.name}`,
            failures
          );
        }
      }
    }

    if (failures.length > 0) {
      const errorMessages = failures
        .map(f => `${f.label}: ${f.error.message}`)
        .join("\n\n");
      throw new Error(
        `Snapshot mismatches in ${failures.length} snapshot(s):\n\n${errorMessages}`
      );
    }
  }
};
export default config;
