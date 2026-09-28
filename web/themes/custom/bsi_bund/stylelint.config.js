/** @type {import('stylelint').Config} */
export default {
  extends: [
    "stylelint-config-recommended",
    "stylelint-config-recommended-scss",
    "stylelint-prettier/recommended"
  ],
  plugins: ["stylelint-prettier", "stylelint-scss", "stylelint-order"],
  rules: {
    // Core rules
    "block-no-empty": true,
    "prettier/prettier": true,

    // SCSS-specific rules
    "scss/operator-no-newline-after": null,

    // Allow :export for CSS Modules (if you use them in the future)
    "selector-pseudo-class-no-unknown": [
      true,
      {
        ignorePseudoClasses: ["export"]
      }
    ],

    // Allow custom SCSS property for namespace
    "property-no-unknown": [
      true,
      {
        ignoreProperties: ["scss-nsp"]
      }
    ],

    // Disable overly strict validation rules
    "declaration-property-value-no-unknown": null,
    "at-rule-descriptor-value-no-unknown": null,
    "at-rule-prelude-no-invalid": null,

    // Enforce consistent ordering of SCSS/CSS declarations
    "order/order": [
      {
        type: "at-rule",
        name: "extend"
      },
      {
        type: "at-rule",
        name: "include",
        hasBlock: false
      },
      "custom-properties",
      "dollar-variables",
      "declarations",
      {
        type: "at-rule",
        name: "include",
        hasBlock: true
      },
      "rules"
    ],

    // Alphabetical property ordering
    "order/properties-alphabetical-order": true
  }
};
