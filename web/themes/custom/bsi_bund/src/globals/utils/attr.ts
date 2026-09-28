export type AttributeType =
  Record<string, string | number | boolean> | undefined;
export const attr = (additionalAttributes: AttributeType) =>
  Object.entries(additionalAttributes ?? "")
    .map(([key, value]) => {
      if (typeof value === "boolean") {
        return value ? key : "";
      }
      return `${key}="${String(value)}"`;
    })
    .filter(Boolean)
    .join(" ");
