import type { Preview } from "@storybook/html-vite";

// Import styles from Drupal core themes and contrib modules (Claro cascade).
import "./drupal-css";

// Global styles
import "../src/applications/default/index.scss";
import "../src/applications/storybook/storybook.scss";

// Global scripts
import "../src/applications/default/index";

// Shared viewport configuration
import { customViewports } from "./viewports.ts";

// Import namespace constant
import { NSP } from "../src/globals/index.ts";

const preview: Preview = {
  parameters: {
    a11y: { test: "error" } as any,
    actions: { disable: true },
    layout: "fullscreen",
    backgrounds: {
      disable: true,
      grid: { disable: true }
    },
    docs: {
      codePanel: true,
      source: {
        language: "html",
        // format source code using Prettier for better readability in the docs
        transform: async (source: string) => {
          const prettier = await import("prettier/standalone");
          const prettierPluginHtml = await import("prettier/plugins/html");
          return prettier.format(source, {
            parser: "html",
            plugins: [prettierPluginHtml]
          });
        },
        excludeDecorators: true
      }
    },
    grid: {
      gridOn: true,
      columns: `var(--${NSP}-grid-columns)`,
      gap: `var(--${NSP}-grid-gutter)`,
      gutter: `var(--${NSP}-grid-padding)`,
      maxWidth: `var(--${NSP}-grid-max-width)`
    },
    viewport: {
      options: {
        ...customViewports
      }
    }
  }
};

export default preview;
