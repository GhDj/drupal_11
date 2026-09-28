/**
 * Generate SCSS variables from design tokens
 * Run with: npm run tokens:generate
 *
 * This generator is fully abstract and works with any token structure.
 * Developers only need to edit the tokens.*.json files to add, remove, or modify tokens.
 *
 * Rules:
 * - Nested objects → flat variables with hyphens (e.g., {color: {primary: "#000"}} → $color-primary: #000)
 * - Arrays → SCSS maps (e.g., [400, 700] → (400, 700))
 * - Objects in arrays/nested deeply → SCSS maps
 * - camelCase keys → kebab-case
 * - Primitive values at top level → simple variables (e.g., "nsp": "myapp" → $nsp: myapp)
 */

import { writeFileSync } from "node:fs";
import { resolve, dirname } from "node:path";
import { fileURLToPath } from "node:url";
import {
  loadTokens,
  type DesignTokens,
  type TokenValue,
  type TokenObject
} from "./load-tokens";

const __dirname = dirname(fileURLToPath(import.meta.url));

/**
 * Converts a camelCase string to kebab-case
 */
function toKebabCase(str: string): string {
  return str
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .replace(/([A-Z])([A-Z][a-z])/g, "$1-$2")
    .toLowerCase();
}

/**
 * Checks if a value should be treated as a SCSS map (array or deeply nested object)
 */
function shouldBeMap(
  value: TokenValue,
  key?: string,
  scssMaps?: string[]
): boolean {
  // If scssMaps is defined, ONLY treat explicitly listed keys as maps
  if (scssMaps && scssMaps.length > 0) {
    // Check if this key is explicitly marked as a map
    if (key && scssMaps.includes(key)) {
      return true;
    }
    // If scssMaps exists but this key isn't in it, continue with normal logic
    // for arrays (but not for nested objects)
    if (Array.isArray(value)) return true;
    return false;
  }

  // Legacy behavior (when scssMaps is not defined):
  // Arrays are always maps
  if (Array.isArray(value)) return true;
  // Nested objects become maps
  if (typeof value === "object" && value !== null) {
    // Check if any nested value is an object or array
    return Object.values(value).some(v => typeof v === "object" && v !== null);
  }
  return false;
}

/**
 * Formats a value for SCSS output
 */
function formatScssValue(value: TokenValue, indent = 0): string {
  const indentStr = "  ".repeat(indent);

  if (typeof value === "string") {
    // Don't quote values that are already quoted, CSS keywords, CSS values, or SCSS expressions
    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'")) ||
      /^[0-9]/.test(value) ||
      value.startsWith("#") ||
      value.startsWith("$") || // SCSS variable reference
      value.startsWith("calc(") ||
      value.startsWith("var(") ||
      value.startsWith("rgb") ||
      value.startsWith("hsl") ||
      value.includes(" * ") || // SCSS arithmetic expression
      value.includes(" + ") ||
      value.includes(" - ") ||
      value.includes(" / ") ||
      value === "inherit" ||
      value === "initial" ||
      value === "unset" ||
      value === "auto" ||
      value === "none" ||
      // Check if it's a simple identifier (for things like font family names without spaces)
      /^[a-zA-Z][a-zA-Z0-9]*$/.test(value)
    ) {
      return value;
    }
    // If the string contains double quotes, use single quotes to avoid escaping
    // Otherwise use double quotes (Prettier default for singleQuote: false)
    if (value.includes('"')) {
      const escaped = value.replace(/'/g, "\\'");
      return `'${escaped}'`;
    } else {
      return `"${value}"`;
    }
  }

  if (typeof value === "number" || typeof value === "boolean") {
    return String(value);
  }

  if (value === null) {
    return "null";
  }

  if (Array.isArray(value)) {
    if (value.length === 0) return "()";
    const items = value.map(v => formatScssValue(v, indent)).join(", ");
    return `(${items})`;
  }

  if (typeof value === "object") {
    const entries = Object.entries(value);
    if (entries.length === 0) return "()";

    const lines: string[] = ["("];
    entries.forEach(([key, val], index) => {
      const isLast = index === entries.length - 1;
      const formattedKey = toKebabCase(key);
      const formattedValue = formatScssValue(val, indent + 1);
      const comma = isLast ? "" : ",";

      // Always quote map keys for consistency
      lines.push(`${indentStr}  "${formattedKey}": ${formattedValue}${comma}`);
    });
    lines.push(`${indentStr})`);
    return lines.join("\n");
  }

  return String(value);
}

/**
 * Recursively flattens nested objects into flat variable names with hyphens
 */
function flattenTokens(
  obj: TokenObject,
  prefix = "",
  scssMaps?: string[]
): { name: string; value: TokenValue }[] {
  const result: { name: string; value: TokenValue }[] = [];

  for (const [key, value] of Object.entries(obj)) {
    const kebabKey = toKebabCase(key);
    const fullKey = prefix ? `${prefix}-${kebabKey}` : kebabKey;

    if (shouldBeMap(value, key, scssMaps)) {
      // This value should become a SCSS map
      result.push({ name: fullKey, value });
    } else if (
      typeof value === "object" &&
      value !== null &&
      !Array.isArray(value)
    ) {
      // Continue flattening
      result.push(...flattenTokens(value, fullKey, scssMaps));
    } else {
      // Primitive value
      result.push({ name: fullKey, value });
    }
  }

  return result;
}

/**
 * Generates SCSS variables from design tokens
 */
function generateSCSS(tokens: DesignTokens): string {
  const lines: string[] = [];

  lines.push(
    "// -----------------------------------------------------------------------------"
  );
  lines.push("// Design Tokens - Generated from tokens.*.json files");
  lines.push("// DO NOT EDIT THIS FILE MANUALLY");
  lines.push(
    "// -----------------------------------------------------------------------------"
  );
  lines.push("");

  // Filter out meta-keys from token processing
  const {
    customProperties: _customProperties,
    tsVars: _tsVars,
    scssExclude,
    scssMaps,
    ...actualTokens
  } = tokens;

  // Filter out excluded tokens
  const tokensToProcess = { ...actualTokens };
  if (scssExclude) {
    for (const excludeKey of scssExclude) {
      delete tokensToProcess[excludeKey];
    }
  }

  // Flatten all tokens
  const flatTokens = flattenTokens(tokensToProcess, "", scssMaps);

  // Generate SCSS variables
  for (const { name, value } of flatTokens) {
    const formattedValue = formatScssValue(value);
    lines.push(`$${name}: ${formattedValue} !default;`);
  }

  lines.push("");

  return lines.join("\n");
}

/**
 * Generates CSS custom properties
 */
function generateCustomProperties(tokens: DesignTokens): string {
  const lines: string[] = [];

  lines.push(
    "// -----------------------------------------------------------------------------"
  );
  lines.push("// CSS Custom Properties - Generated from tokens.*.json files");
  lines.push("// DO NOT EDIT THIS FILE MANUALLY");
  lines.push(
    "// -----------------------------------------------------------------------------"
  );
  lines.push("");

  // Only generate if customProperties array exists
  if (!tokens.customProperties || tokens.customProperties.length === 0) {
    lines.push("// No CSS custom properties defined.");
    lines.push(
      "// Add a 'customProperties' array to tokens.00-config.json to generate CSS variables."
    );
    lines.push("");
    return lines.join("\n");
  }

  lines.push('@use "./variables" as *;');
  lines.push("");
  lines.push(":root {");

  // Filter out meta-keys from token processing
  const {
    customProperties: _customProperties2,
    tsVars: _tsVars2,
    scssExclude,
    scssMaps,
    ...actualTokens
  } = tokens;

  // Get all flattened variables (including excluded ones for CSS vars)
  const allVariables = flattenTokens(actualTokens, "", scssMaps);

  // Create set of excluded variable names for checking
  const excludedVarNames = new Set<string>();
  if (scssExclude) {
    for (const excludeKey of scssExclude) {
      const tokenValue = actualTokens[excludeKey];
      if (tokenValue !== undefined) {
        const excluded = flattenTokens(
          {
            [excludeKey]: tokenValue
          },
          "",
          scssMaps
        );
        for (const { name } of excluded) {
          excludedVarNames.add(name);
        }
      }
    }
  }

  // Generate CSS custom properties
  const generatedVars = new Set<string>();

  for (const varNameOrCategory of tokens.customProperties) {
    // Handle dot notation for nested categories (e.g., "space.scale")
    if (varNameOrCategory.includes(".")) {
      const parts = varNameOrCategory.split(".");
      const prefix = parts.map(p => toKebabCase(p)).join("-");

      // Find all variables that match this prefix
      const matchingVars = allVariables.filter(
        v => v.name === prefix || v.name.startsWith(`${prefix}-`)
      );

      for (const { name, value } of matchingVars) {
        // Skip SCSS maps (can't be CSS custom properties)
        if (shouldBeMap(value)) continue;

        if (!generatedVars.has(name)) {
          // If variable is excluded from SCSS, output raw value; otherwise reference SCSS var
          if (excludedVarNames.has(name)) {
            lines.push(`  --#{$nsp}-${name}: ${formatScssValue(value)};`);
          } else {
            lines.push(`  --#{$nsp}-${name}: #{$${name}};`);
          }
          generatedVars.add(name);
        }
      }
    } else {
      const kebabName = toKebabCase(varNameOrCategory);

      // Check if this is a category (exists as top-level key in tokens)
      if (varNameOrCategory in actualTokens) {
        const categoryValue = actualTokens[varNameOrCategory];

        // Only process if it's an object (category with children)
        if (
          typeof categoryValue === "object" &&
          !Array.isArray(categoryValue) &&
          categoryValue !== null
        ) {
          // Generate CSS vars for all children in this category
          const categoryVars = allVariables.filter(v => {
            // Match exact category name or children (e.g., "space" matches "space-5xs" but not "space-base" if "spaceBase" is separate)
            if (v.name === kebabName) return true;
            if (!v.name.startsWith(`${kebabName}-`)) return false;

            // Make sure this is actually from this category, not another top-level key
            // by checking if the remaining part after kebabName- exists as a key in categoryValue
            const suffix = v.name.substring(kebabName.length + 1); // +1 for the hyphen
            const firstPart = suffix.split("-")[0];
            return (
              suffix in categoryValue ||
              (firstPart !== undefined && firstPart in categoryValue)
            );
          });

          for (const { name, value } of categoryVars) {
            // Skip SCSS maps (can't be CSS custom properties)
            if (shouldBeMap(value)) continue;

            if (!generatedVars.has(name)) {
              // If variable is excluded from SCSS, output raw value; otherwise reference SCSS var
              if (excludedVarNames.has(name)) {
                lines.push(`  --#{$nsp}-${name}: ${formatScssValue(value)};`);
              } else {
                lines.push(`  --#{$nsp}-${name}: #{$${name}};`);
              }
              generatedVars.add(name);
            }
          }
        } else {
          // It's a primitive value (not a category)
          if (!generatedVars.has(kebabName)) {
            // If variable is excluded from SCSS, output raw value; otherwise reference SCSS var
            if (excludedVarNames.has(kebabName)) {
              const varData = allVariables.find(v => v.name === kebabName);
              if (varData) {
                lines.push(
                  `  --#{$nsp}-${kebabName}: ${formatScssValue(varData.value)};`
                );
              }
            } else {
              lines.push(`  --#{$nsp}-${kebabName}: #{$${kebabName}};`);
            }
            generatedVars.add(kebabName);
          }
        }
      } else {
        // Generate CSS var for specific variable
        if (!generatedVars.has(kebabName)) {
          // Find the variable value
          const varData = allVariables.find(v => v.name === kebabName);
          if (varData && excludedVarNames.has(kebabName)) {
            lines.push(
              `  --#{$nsp}-${kebabName}: ${formatScssValue(varData.value)};`
            );
          } else {
            lines.push(`  --#{$nsp}-${kebabName}: #{$${kebabName}};`);
          }
          generatedVars.add(kebabName);
        }
      }
    }
  }

  lines.push("}");
  lines.push("");

  return lines.join("\n");
}

// Main execution
try {
  const variablesOutputPath = resolve(
    __dirname,
    "../foundation/_variables.scss"
  );
  const customPropsOutputPath = resolve(
    __dirname,
    "../foundation/_custom-properties.scss"
  );

  const tokens = loadTokens();

  const scss = generateSCSS(tokens);
  const customProps = generateCustomProperties(tokens);

  writeFileSync(variablesOutputPath, scss, "utf-8");
  writeFileSync(customPropsOutputPath, customProps, "utf-8");

  console.log("✓ Generated SCSS variables at foundation/_variables.scss");
  console.log(
    "✓ Generated CSS custom properties at foundation/_custom-properties.scss"
  );
} catch (error) {
  console.error("Error generating SCSS:", error);
  process.exit(1);
}
