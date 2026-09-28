import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";
import SVGSpriter from "svg-sprite";

const themeRoot = path.resolve(fileURLToPath(new URL("..", import.meta.url)));

export function generateIconSprite(rootDir = themeRoot) {
  const config = {
    mode: {
      symbol: {
        dest: ".",
        sprite: "icons.svg"
      }
    }
  };

  const spriter = new SVGSpriter(config);
  const iconsDir = path.resolve(rootDir, "src/assets/default/icons");

  fs.readdirSync(iconsDir).forEach(file => {
    if (file.endsWith(".svg")) {
      const filePath = path.join(iconsDir, file);
      const svg = fs.readFileSync(filePath, "utf-8");

      spriter.add(filePath, file, svg);
    }
  });

  return new Promise((resolvePromise, rejectPromise) => {
    spriter.compile((error, result) => {
      if (error) {
        rejectPromise(error);
        return;
      }

      const sprite = result.symbol.sprite;
      resolvePromise(sprite.contents);
    });
  });
}

if (process.argv[1] === fileURLToPath(import.meta.url)) {
  generateIconSprite()
    .then(sprite => {
      const output = path.resolve(themeRoot, "dist/icons.svg");

      fs.mkdirSync(path.dirname(output), { recursive: true });
      fs.writeFileSync(output, sprite);

      console.log("✅ icons.svg generated:", output);
    })
    .catch(error => {
      console.error(error);
      process.exitCode = 1;
    });
}
