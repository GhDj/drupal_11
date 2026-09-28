/**
 * Generate both TypeScript and SCSS from design tokens
 * Run with: npm run tokens:generate
 */

import { execSync } from "node:child_process";
import { dirname } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

console.log("Generating design tokens...\n");

try {
  // Run TypeScript generator
  execSync("tsx ./generate-ts.ts", {
    cwd: __dirname,
    stdio: "inherit"
  });

  // Run SCSS generator
  execSync("tsx ./generate-scss.ts", {
    cwd: __dirname,
    stdio: "inherit"
  });

  console.log("\n✓ All design tokens generated successfully!");
  console.log("Running Stylelint to format generated SCSS files...\n");

  // Run Stylelint fix on generated SCSS files
  execSync("stylelint ../foundation/*.scss --fix", {
    cwd: __dirname,
    stdio: "inherit"
  });

  console.log("✓ Generated files formatted!");
} catch {
  console.error("\n✗ Error generating design tokens");
  process.exit(1);
}
