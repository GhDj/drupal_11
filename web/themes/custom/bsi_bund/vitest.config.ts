import path from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vitest/config";
import { aliases } from "./vite.config";
import { storybookTest } from "@storybook/addon-vitest/vitest-plugin";

const dirname: string =
  typeof __dirname !== "undefined"
    ? __dirname
    : path.dirname(fileURLToPath(import.meta.url));

const storybookDir = path.join(dirname, ".storybook");

export default defineConfig({
  resolve: { alias: aliases },
  css: {
    preprocessorOptions: {
      scss: {
        loadPaths: [path.resolve(dirname, "src")]
      }
    }
  },
  test: {
    projects: [
      {
        extends: true,
        plugins: [storybookTest({ configDir: storybookDir })],
        test: {
          name: "storybook",
          browser: {
            enabled: true,
            headless: true,
            provider: "playwright",
            instances: [{ browser: "chromium" }]
          },
          setupFiles: [path.join(storybookDir, "vitest.setup.ts")]
        }
      }
    ]
  }
});
