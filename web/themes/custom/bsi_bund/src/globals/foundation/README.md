## Foundation - Design Token Variables

This directory contains auto-generated TypeScript constants, SCSS variables, and
CSS custom properties derived from the design tokens defined in
`src/globals/config/tokens.*.json`. These files serve as the foundation for all
styling and theming throughout the project.

The foundation system provides three output formats from a single source of
truth:

1. **TypeScript constants** (`variables.ts`) - For use in component templates
   and TypeScript code
2. **SCSS variables** (`_variables.scss`) - For use in SCSS stylesheets
3. **CSS custom properties** (`_custom-properties.scss`) - For runtime theming
   and CSS usage

⚠️ All files in this directory are **auto-generated**. Never edit them manually.
⚠️

## Table of Contents

- [Files](#files)
- [Import Patterns](#import-patterns)
- [Auto-Generation](#auto-generation)
- [Best Practices](#best-practices)
- [Related Documentation](#related-documentation)

## Files

- **`index.ts`** - TypeScript entry point, exports all TypeScript constants
- **`variables.ts`** - Generated TypeScript constants (DO NOT EDIT)
- **`_index.scss`** - SCSS entry point, forwards all SCSS modules
- **`_variables.scss`** - Generated SCSS variables (DO NOT EDIT)
- **`_custom-properties.scss`** - Generated CSS custom properties (DO NOT EDIT)

## Import Patterns

### TypeScript

```typescript
// Import from the unified entry point
import { NSP, breakpoint, nsp, html } from "@globals";

// Or import from foundation directly
import { NSP, breakpoint } from "@globals/foundation";

// Import specific constants
import { breakpoint } from "@globals/foundation/variables";
```

### SCSS

```scss
// Import variables without namespace
@use "@globals/foundation/variables" as *;

// Or use with namespace
@use "@globals/foundation/variables" as vars;
```

## Auto-Generation

These files are automatically generated from the design token files in
`src/globals/config/` (all `tokens.*.json` files are merged):

```bash
# Manual regeneration
npm run tokens:generate

# Automatic regeneration (happens during build)
npm run build
```

## Best Practices

### DO

- Regenerate tokens after editing any `tokens.*.json` file
- Commit generated files to version control

### DON'T

- Never manually edit files in this directory
- Don't bypass the design token system for design values

## Related Documentation

- **Design Tokens Configuration:**
  [src/globals/config/README.md](../config/README.md) - Source of all design
  tokens
- **Fonts:** [src/globals/fonts/README.md](../fonts/README.md) - Font
  configuration and setup
- **Mixins:** [src/globals/mixins/README.md](../mixins/README.md) - SCSS mixins
  using foundation variables
- **Utils:** [src/globals/utils/README.md](../utils/README.md) - TypeScript
  utilities
