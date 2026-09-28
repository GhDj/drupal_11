/**
 * Component that dynamically loads fixed spacing values from CSS custom properties.
 *
 * This ensures a single source of truth: src/globals/config/tokens.04-space.json
 */

import React, { useMemo } from "react";
import { NSP } from "@globals/foundation/variables";
import { nsp } from "@globals/utils";
import spaceTokens from "@globals/config/tokens.04-space.json";

interface SpaceData {
  variable: string;
  displayVariable: string;
  value: string;
}

export const SpacesLoader: React.FC = () => {
  const spaces = useMemo(() => {
    const spaceSizes = Object.keys(spaceTokens.space);

    const tempDiv = document.createElement("div");
    tempDiv.style.position = "absolute";
    tempDiv.style.visibility = "hidden";
    document.body.appendChild(tempDiv);

    const foundSpaces: SpaceData[] = spaceSizes
      .map(size => {
        const variable = `--${NSP}-space-${size}`;
        const displayVariable = `--#{$nsp}-space-${size}`;
        tempDiv.style.width = `var(${variable})`;
        const computed = getComputedStyle(tempDiv).width;

        // Only include if the variable is defined (not "auto" or "0px")
        if (computed && computed !== "auto" && computed !== "0px") {
          return { variable, displayVariable, value: computed };
        }
        return null;
      })
      .filter((space): space is SpaceData => space !== null)
      .sort((a, b) => parseFloat(a.value) - parseFloat(b.value));

    document.body.removeChild(tempDiv);

    return foundSpaces;
  }, []);

  if (spaces.length === 0) {
    return <p>Loading spaces...</p>;
  }

  return (
    <div className={nsp("spaces-loader")}>
      {spaces.map(space => (
        <div key={space.variable} className={nsp("spaces-loader__item")}>
          <div className={nsp("spaces-loader__variable")}>
            <code>var({space.displayVariable})</code>
          </div>
          <div
            className={nsp("spaces-loader__visual")}
            style={{ width: `var(${space.variable})` }}
          />
          <div className={nsp("spaces-loader__value")}>({space.value})</div>
        </div>
      ))}
    </div>
  );
};
