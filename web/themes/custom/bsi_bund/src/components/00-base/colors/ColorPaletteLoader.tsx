/**
 * Component that dynamically loads colors from CSS custom properties
 * and renders them using Storybook's ColorPalette doc block.
 *
 * This ensures a single source of truth: src/globals/config/tokens.03-color.json
 */

import React, { useMemo } from "react";
import { ColorPalette, ColorItem } from "@storybook/addon-docs/blocks";
// import { NSP } from "@globals/foundation/variables";
import colorTokens from "@globals/config/tokens.03-color.json";

interface ColorData {
  name: string;
  variable: string;
  value: string;
}

type ColorTokenValue = string | { [key: string]: ColorTokenValue };

function formatColorName(colorKey: string): string {
  return colorKey
    .split("-")
    .map(part => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

function flattenColorTokens(
  tokens: Record<string, ColorTokenValue>,
  prefix = ""
): Record<string, string> {
  return Object.entries(tokens).reduce<Record<string, string>>(
    (result, [key, value]) => {
      const colorKey = prefix ? `${prefix}-${key}` : key;

      if (typeof value === "string") {
        result[colorKey] = value;
      } else {
        Object.assign(result, flattenColorTokens(value, colorKey));
      }

      return result;
    },
    {}
  );
}

export const ColorPaletteLoader: React.FC = () => {
  const colors = useMemo(() => {
    // Load color names dynamically from design tokens
    const colorEntries = Object.entries(
      flattenColorTokens(colorTokens.color as Record<string, ColorTokenValue>)
    );

    const foundColors: ColorData[] = colorEntries.map(([key, tokenValue]) => {
      const variable = `--#{$nsp}-color-${key}`;
      const value = getComputedStyle(document.documentElement)
        .getPropertyValue(variable)
        .trim();

      return {
        name: formatColorName(key),
        variable,
        value: value || tokenValue
      };
    });

    // Sort alphabetically by name for consistent order
    foundColors.sort((a, b) => a.name.localeCompare(b.name));

    return foundColors;
  }, []);

  if (colors.length === 0) {
    return <p>Loading colors...</p>;
  }

  return (
    <ColorPalette>
      {colors.map(color => (
        <ColorItem
          key={color.variable}
          title={color.name}
          subtitle={`var(${color.variable})`}
          colors={[color.value]}
        />
      ))}
    </ColorPalette>
  );
};
