/**
 * Load and merge design tokens from separate JSON files
 */

import { readdirSync, readFileSync } from "node:fs";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";

const __dirname = dirname(fileURLToPath(import.meta.url));

export type TokenValue =
  string | number | boolean | null | TokenObject | TokenArray;
export interface TokenObject {
  [key: string]: TokenValue;
}
export type TokenArray = TokenValue[];

export interface DesignTokens extends TokenObject {
  customProperties?: string[];
  tsVars?: string[];
  scssExclude?: string[];
  scssMaps?: string[];
}

/**
 * Load all token files and merge them into a single object.
 * Automatically discovers all files matching the pattern `tokens.*.json`
 * and loads them in alphabetical order (config first, then others alphabetically).
 */
export function loadTokens(): DesignTokens {
  // Discover all token files matching the pattern "tokens.*.json"
  const allFiles = readdirSync(__dirname);
  const tokenFiles = allFiles
    .filter(file => file.startsWith("tokens.") && file.endsWith(".json"))
    .sort((a, b) => {
      // Ensure tokens.config.json is loaded first (for meta-configuration)
      if (a === "tokens.config.json") return -1;
      if (b === "tokens.config.json") return 1;
      return a.localeCompare(b);
    });

  const mergedTokens: DesignTokens = {};

  for (const file of tokenFiles) {
    const filePath = resolve(__dirname, file);
    try {
      const fileContent = readFileSync(filePath, "utf-8");
      const tokens = JSON.parse(fileContent) as TokenObject;

      // Merge tokens into the main object
      Object.assign(mergedTokens, tokens);
    } catch (error) {
      console.warn(`Warning: Could not load ${file}:`, error);
    }
  }

  return mergedTokens;
}
