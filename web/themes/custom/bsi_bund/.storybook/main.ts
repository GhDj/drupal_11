import type { StorybookConfig } from "@storybook/html-vite";
import { resolve } from "node:path";
import { fileURLToPath } from "node:url";
import ViteYaml from "@modyfi/vite-plugin-yaml";

const rootDir = fileURLToPath(new URL("..", import.meta.url));

const config: StorybookConfig = {
  stories: [
    "../src/components/docs/**/*.mdx",
    "../src/components/**/*.stories.@(js|jsx|ts|tsx)",
    "../src/components/**/*.mdx"
  ],
  addons: [
    "@storybook/addon-docs",
    "@storybook/addon-vitest",
    "@storybook/addon-a11y",
    "storybook-addon-grid"
  ],
  staticDirs: [
    "../src/demo-content/images",
    "../src/demo-content/media",
    "../src/assets/default/logos"
  ],
  framework: {
    name: "@storybook/html-vite",
    options: {}
  },
  core: {
    allowedHosts: ["bsi.bund.ddev.site"],
    disableTelemetry: true
  },
  viteFinal: async cfg => {
    cfg.plugins = [...(cfg.plugins ?? []), ViteYaml()];

    cfg.resolve = {
      ...cfg.resolve,
      tsconfigPaths: true
    };

    // Allow Vite to read files from the theme itself AND from web/core and web/modules
    // (Claro CSS + contrib module CSS live outside the theme root).
    cfg.server = {
      ...cfg.server,
      fs: {
        ...cfg.server?.fs,
        allow: [
          ...(cfg.server?.fs?.allow ?? []),
          rootDir,
          resolve(rootDir, "../../../core"),
          resolve(rootDir, "../../../modules")
        ]
      }
    };

    // Configure SCSS preprocessor options
    cfg.css = {
      ...cfg.css,
      preprocessorOptions: {
        ...cfg.css?.preprocessorOptions,
        scss: {
          ...cfg.css?.preprocessorOptions?.scss,
          loadPaths: [resolve(rootDir, "src")]
        }
      }
    };

    return cfg;
  }
};

export default config;
