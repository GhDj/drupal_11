/**
 * Template literal tag function for HTML strings with type safety.
 *
 * Ensures all template placeholders are strings to prevent injection of
 * non-string values into HTML templates.
 *
 * @param strings - Template literal string parts
 * @param placeholders - Values to interpolate (must be strings)
 * @returns The constructed HTML string
 * @throws Error if any placeholder is not a string
 *
 * @example
 * import { html } from "@globals";
 *
 * const name = "World";
 * const template = html`<div>Hello ${name}</div>`;
 */
const tag = (
  strings: { raw: ArrayLike<string> | readonly string[] },
  ...placeholders: unknown[]
) => {
  for (const placeholder of placeholders) {
    if (typeof placeholder !== "string") {
      throw Error("Invalid input: All template placeholders must be strings");
    }
  }
  return String.raw(strings, ...placeholders);
};

/**
 * HTML template literal tag function.
 *
 * Use this to create HTML template strings with type-safe interpolation.
 *
 * @example
 * import { html } from "@globals";
 *
 * const greeting = html`<h1>Hello World</h1>`;
 */
export const html = tag;
