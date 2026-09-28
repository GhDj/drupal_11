import { resolve } from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vite";
import { generateIconSprite } from "./scripts/build-icons.js";

const rootDir = fileURLToPath(new URL(".", import.meta.url));

export const aliases = {
  "@globals": resolve(rootDir, "src/globals"),
  "@base": resolve(rootDir, "src/components/00-base"),
  "@atoms": resolve(rootDir, "src/components/01-atoms"),
  "@molecules": resolve(rootDir, "src/components/02-molecules"),
  "@organisms": resolve(rootDir, "src/components/03-organisms"),
  "@templates": resolve(rootDir, "src/components/04-templates"),
  "@pages": resolve(rootDir, "src/components/05-pages"),
  "@demo-content": resolve(rootDir, "src/demo-content")
};

export default defineConfig(({ mode }) => ({
  base: "./",
  plugins: [
    {
      name: "build-icons-sprite",
      async generateBundle() {
        const sprite = await generateIconSprite(rootDir);

        this.emitFile({
          type: "asset",
          fileName: "icons.svg",
          source: sprite
        });
      }
    }
  ],
  resolve: {
    alias: aliases
  },
  css: {
    devSourcemap: true,
    preprocessorOptions: {
      scss: {
        loadPaths: [resolve(rootDir, "src")]
      }
    }
  },
  build: {
    target: "es2019",
    minify: "terser",
    terserOptions: {
      mangle: {
        reserved: ["_"]
      }
    },
    cssMinify: mode === "production",
    sourcemap: mode !== "production",
    rollupOptions: {
      input: {
        application: resolve(rootDir, "src/applications/default/index.ts"),
        styles: resolve(rootDir, "src/applications/default/index.scss")
      },
      output: {
        format: "es",
        entryFileNames: `index.js`,
        chunkFileNames: `[name].js`,
        assetFileNames: `[name].[ext]`
      }
    }
  }
}));
