import { NSP } from "@globals/foundation";

/**
 * Add project namespace to class names.
 *
 * Supports multiple usage patterns:
 * - Single class: nsp("button") => "nsp-button"
 * - Multiple arguments: nsp("button", "primary") => "nsp-button nsp-primary"
 * - Space-separated string: nsp("button primary") => "nsp-button nsp-primary"
 * - Array spreading: nsp(...["button", "primary"]) => "nsp-button nsp-primary"
 *
 * @param classNames - One or more class names to prefix with the namespace
 * @returns Space-separated string of namespaced class names
 *
 * @example
 * import { nsp } from "@globals";
 *
 * nsp("button")            // "nsp-button"
 * nsp("button", "primary") // "nsp-button nsp-primary"
 * nsp("button primary")    // "nsp-button nsp-primary"
 */
export const nsp = (...classNames: string[]) =>
  classNames
    .map(classList => classList.split(/\s+/).filter(Boolean))
    .flat()
    .map(className => `${NSP}-${className}`)
    .join(" ");
