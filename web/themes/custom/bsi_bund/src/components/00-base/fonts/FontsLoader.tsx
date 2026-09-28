/**
 * Component that dynamically loads font configuration from design tokens
 * and renders font specimens.
 *
 * This ensures a single source of truth: src/globals/config/tokens.06-font.json
 */

import React, { useMemo } from "react";
import fontTokens from "@globals/config/tokens.06-font.json";

interface FontWeightData {
  name: string;
  weight: number;
  file: string;
}

interface FontSizeExample {
  label: string;
  size: number;
}

interface ConfigProperty {
  key: string;
  value: string | number;
}

const sampleText = "The quick brown fox jumps over the lazy dog";
const pangram =
  "ABCDEFGHIJKLMNOPQRSTUVWXYZ abcdefghijklmnopqrstuvwxyz 0123456789";

// Auto-generate font size examples based on a range
const FONT_SIZE_EXAMPLES: FontSizeExample[] = [12, 16, 20, 24].map(size => ({
  label:
    size === 16
      ? "Base"
      : size < 16
        ? "Small"
        : size === 20
          ? "Large"
          : "XLarge",
  size
}));

function formatWeightName(weightKey: string): string {
  return weightKey
    .split("-")
    .map(part => part.charAt(0).toUpperCase() + part.slice(1))
    .join(" ");
}

export const FontsLoader: React.FC = () => {
  const fontWeights = useMemo(() => {
    const weights = Object.entries(fontTokens.fontWeights).map(
      ([key, data]) => ({
        name: formatWeightName(key),
        weight: data.weight,
        file: data.file
      })
    );

    // Sort by weight value
    weights.sort((a, b) => a.weight - b.weight);

    return weights;
  }, []);

  const configProperties = useMemo(() => {
    // Automatically generate config table from all non-weight properties
    const properties: ConfigProperty[] = [];

    Object.entries(fontTokens).forEach(([key, value]) => {
      if (key !== "fontWeights" && value !== null && value !== undefined) {
        properties.push({
          key,
          value:
            typeof value === "object" ? JSON.stringify(value) : String(value)
        });
      }
    });

    return properties;
  }, []);

  const fontFamilySans = fontTokens.fontFamilySans;

  return (
    <div>
      <h2>Font Specimens</h2>

      {fontWeights.map((fontWeight: FontWeightData) => (
        <div
          key={fontWeight.weight}
          style={{
            marginBottom: "3rem",
            padding: "1.5rem",
            border: "1px solid #e0e0e0",
            borderRadius: "4px"
          }}
        >
          <div
            style={{
              marginBottom: "1rem",
              paddingBottom: "0.5rem",
              borderBottom: "2px solid #f0f0f0"
            }}
          >
            <h3 style={{ margin: 0, fontSize: "1rem", fontWeight: 700 }}>
              {fontWeight.name}
            </h3>
            <div
              style={{
                fontSize: "0.875rem",
                color: "#666",
                marginTop: "0.25rem"
              }}
            >
              <code>font-weight: {fontWeight.weight}</code>
              <span style={{ margin: "0 0.5rem" }}>•</span>
              <code>font-family: {fontFamilySans}</code>
            </div>
            <div
              style={{
                fontSize: "0.75rem",
                color: "#999",
                marginTop: "0.25rem"
              }}
            >
              {fontWeight.file}
            </div>
          </div>

          <div
            style={{
              fontFamily: `${fontFamilySans}, sans-serif`,
              fontWeight: fontWeight.weight
            }}
          >
            <p style={{ fontSize: "2rem", margin: "1rem 0", lineHeight: 1.2 }}>
              {sampleText}
            </p>
            <p style={{ fontSize: "1rem", margin: "1rem 0", lineHeight: 1.5 }}>
              {pangram}
            </p>
            <div
              style={{
                display: "grid",
                gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))",
                gap: "1rem",
                marginTop: "1rem"
              }}
            >
              {FONT_SIZE_EXAMPLES.map(example => (
                <div key={example.size}>
                  <div
                    style={{
                      fontSize: "0.75rem",
                      color: "#666",
                      marginBottom: "0.25rem"
                    }}
                  >
                    {example.label} ({example.size}px)
                  </div>
                  <div style={{ fontSize: `${example.size}px` }}>
                    {sampleText}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      ))}

      <div
        style={{
          marginTop: "3rem",
          padding: "1.5rem",
          backgroundColor: "#f9f9f9",
          borderRadius: "4px"
        }}
      >
        <h3 style={{ marginTop: 0 }}>Font Configuration</h3>
        <table
          style={{
            width: "100%",
            borderCollapse: "collapse",
            fontSize: "0.875rem"
          }}
        >
          <thead>
            <tr style={{ borderBottom: "2px solid " }}>
              <th style={{ textAlign: "left", padding: "0.5rem" }}>Property</th>
              <th style={{ textAlign: "left", padding: "0.5rem" }}>Value</th>
            </tr>
          </thead>
          <tbody>
            {configProperties.map((prop, index) => (
              <tr
                key={prop.key}
                style={{
                  borderBottom:
                    index < configProperties.length - 1
                      ? "1px solid #eee"
                      : "none"
                }}
              >
                <td style={{ padding: "0.5rem" }}>
                  <code>{prop.key}</code>
                </td>
                <td style={{ padding: "0.5rem" }}>{prop.value}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};
