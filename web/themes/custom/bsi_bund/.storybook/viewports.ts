/**
 * Shared viewport configuration for Storybook UI and visual regression tests
 */

// Globals
import { breakpoint } from "../src/globals/foundation/variables.ts";

export interface ViewportConfig {
  name: string;
  type: string;
  styles: {
    width: string;
    height: string;
  };
}

export const customViewports = {
  smartphone: {
    name: "SM - Smartphone",
    type: "mobile",
    styles: {
      width: breakpoint.sm,
      height: "600px"
    }
  },
  tablet: {
    name: "MD - Tablet",
    type: "tablet",
    styles: {
      width: breakpoint.md,
      height: "1024px"
    }
  },
  laptop: {
    name: "LG - Laptop",
    type: "tablet",
    styles: {
      width: breakpoint.lg,
      height: "1024px"
    }
  },
  desktop: {
    name: "XL - Desktop",
    type: "desktop",
    styles: {
      width: breakpoint.xl,
      height: "1024px"
    }
  }
} satisfies Record<string, ViewportConfig>;

/**
 * Convert Storybook viewport config to test-runner format
 */
export function getTestViewports() {
  return Object.entries(customViewports).map(([key, viewport]) => ({
    name: key,
    width: parseInt(viewport.styles.width),
    height: parseInt(viewport.styles.height)
  }));
}
