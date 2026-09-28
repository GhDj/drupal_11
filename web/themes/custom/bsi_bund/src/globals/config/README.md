## Design Tokens Configuration

This directory contains the design tokens configuration system, which serves as
the **single source of truth** for all design values used throughout the
project. The design tokens system automatically generates TypeScript constants,
SCSS variables, and CSS custom properties from modular JSON configuration files.
This ensures consistency across the entire codebase and makes it easy to update
design values in one place.

## Table of Contents

- [Files](#files)
- [How To Use](#how-to-use)
  - [Editing Design Tokens](#editing-design-tokens)
  - [Adding New Token Types](#adding-new-token-types)
  - [Automatic Generation](#automatic-generation)
- [Best Practices](#best-practices)
- [Related Documentation](#related-documentation)

## Files

- **`tokens.*.json`** - Modular design token files (merged automatically)
- **`load-tokens.ts`** - Loads and merges all token files automatically
- **`generate-ts.ts`** - Generates TypeScript constants from tokens
- **`generate-scss.ts`** - Generates SCSS variables and CSS custom properties
- **`generate-all.ts`** - Runs both generators and formats output

## How To Use

### Editing Design Tokens

The design tokens are organized into modular files that are automatically merged
during generation. This makes it easier to manage different categories of
tokens.

1. Edit the appropriate `tokens.*.json` file with your desired values:
   - `tokens.00-config.json` - Meta-configuration (customProperties, tsVars,
     scssExclude, scssMaps)
   - `tokens.01-general.json` - General settings (namespace/NSP)
   - `tokens.02-breakpoint.json` - Responsive breakpoints
   - `tokens.03-color.json` - Color palette
   - `tokens.04-space.json` - Spacing scale
   - `tokens.05-typography.json` - Pre-defined font styles
   - `tokens.06-font.json` - Font family and weight definitions
2. Run the generator to update all files:

```bash
npm run tokens:generate
```

This will automatically:

- Merge all `tokens.*.json` files (in numerical order)
- Generate `src/globals/foundation/variables.ts` (TypeScript constants)
- Generate `src/globals/foundation/_variables.scss` (SCSS variables)
- Generate `src/globals/foundation/_custom-properties.scss` (CSS custom
  properties)
- Format the generated SCSS files with Stylelint

### Adding New Token Types

To add a new category of design tokens:

#### Option 1: Add to Existing File

Simply add your new tokens to the appropriate existing `tokens.*.json` file.

#### Option 2: Create New Token File

1. **Create a new token file** following the naming pattern
   `tokens.XX-name.json` (where XX is a number determining load order):

   ```json
   {
     "myNewCategory": {
       "value1": "...",
       "value2": "..."
     }
   }
   ```

2. **Configure generation** in `tokens.00-config.json`:

   ```json
   {
     "customProperties": ["color", "space", "myNewCategory"],
     "tsVars": ["NSP", "breakpoint", "myNewCategory"],
     "scssExclude": [],
     "scssMaps": []
   }
   ```

   - `customProperties`: Array of token categories to export as CSS custom
     properties
   - `tsVars`: Array of token categories to export as TypeScript constants
   - `scssExclude`: Array of token categories to exclude from SCSS variables
     (but can still be in CSS custom properties)
   - `scssMaps`: Array of token categories to keep as nested SCSS maps

3. **Run the generator** with `npm run tokens:generate`
4. **Use the new tokens** in your components via TypeScript or SCSS imports

### Automatic Generation

The build process automatically regenerates tokens:

```bash
npm run build  # Includes token generation
```

## Token File Organization

Token files are loaded in alphabetical order, with the numerical prefix
controlling the sequence:

- `tokens.00-config.json` - **Always loaded first** - Contains
  meta-configuration (`customProperties`, `tsVars`, `scssExclude`)
- `tokens.01-*.json` through `tokens.99-*.json` - Loaded in numerical order
- Files with the same number are loaded alphabetically by name

This ordering matters when tokens reference other tokens (e.g., spacing that
uses base values).

### Configuration Options

The `tokens.00-config.json` file controls how tokens are generated:

```json
{
  "customProperties": ["color", "spaceBase", "space"],
  "tsVars": ["NSP", "breakpoint"],
  "scssExclude": ["space"],
  "scssMaps": ["typography", "fontWeights"]
}
```

- **`customProperties`**: Array of token categories to export as CSS custom
  properties in `:root`. These become `--nsp-category-name` variables.
- **`tsVars`**: Array of token categories to export as TypeScript constants.
  These become named exports in `variables.ts`.
- **`scssExclude`**: Array of token categories to exclude from SCSS `$variables`
  (useful for tokens that are only calculated/used in CSS custom properties).
- **`scssMaps`**: Array of token categories to keep as nested SCSS maps instead
  of flattening into individual variables. Useful for complex structures that
  need to be accessed via `map.get()` in SCSS.

### SCSS Maps vs Flattened Variables

By default, nested token objects are flattened into individual SCSS variables
with hyphen-separated names. However, you can control this behavior using the
`scssMaps` configuration option.

#### Default Behavior (Flattened)

When a token category is **NOT** in `scssMaps`, nested objects are flattened:

**Token File (`tokens.03-color.json`):**

```json
{
  "color": {
    "primary": "#004164",
    "primary-100": "#e6ecf0",
    "primary-200": "#ccd9e0",
    "secondary": "#028369",
    "secondary-200": "#cce6e1"
  }
}
```

**Generated SCSS (`_variables.scss`):**

```scss
$color-primary: #004164 !default;
$color-primary-100: #e6ecf0 !default;
$color-primary-200: #ccd9e0 !default;
$color-secondary: #028369 !default;
$color-secondary-200: #cce6e1 !default;
```

**Usage:**

```scss
.component {
  color: $color-primary; // Direct variable access
  background: $color-primary-100;
}
```

#### Map Behavior (Nested)

When a token category **IS** in `scssMaps`, it remains as a nested map:

**Configuration (`tokens.00-config.json`):**

```json
{
  "scssMaps": ["typography"]
}
```

**Token File (`tokens.05-typography.json`):**

```json
{
  "typography": {
    "small": {
      "fontSize": "16px",
      "lineHeight": "24px",
      "letterSpacing": "inherit"
    },
    "large": {
      "fontSize": "20px",
      "lineHeight": "24px",
      "letterSpacing": "inherit"
    }
  }
}
```

**Generated SCSS (`_variables.scss`):**

```scss
$typography: (
  "small": (
    "font-size": 16px,
    "line-height": 24px,
    "letter-spacing": inherit
  ),
  "large": (
    "font-size": 20px,
    "line-height": 24px,
    "letter-spacing": inherit
  )
) !default;
```

**Usage:**

```scss
@use "sass:map";

.text-small {
  font-size: map.get($typography, "small", "font-size");
  line-height: map.get($typography, "small", "line-height");
  letter-spacing: map.get($typography, "small", "letter-spacing");
}
```

#### When to Use Maps vs Flattened Variables

**Use Flattened Variables (default) when:**

- You want simple, direct access to values
- Each token is independent (e.g., colors, spacing values)
- You need better IDE autocomplete support
- You prefer cleaner, more readable SCSS

**Use Maps (`scssMaps`) when:**

- You have deeply nested, related data structures
- You need to iterate over token sets
- You want to preserve logical groupings
- You need to pass entire sets of values to mixins/functions

## Best Practices

### DO

- Run `npm run tokens:generate` after editing tokens
- Commit both the token JSON files and generated files to version control
- Organize related tokens into appropriate category files
- Use meaningful numerical prefixes (00-09) for token files to control load
  order

### DON'T

- Never manually edit the generated files (`variables.ts`, `_variables.scss`,
  `_custom-properties.scss`)
- Don't hardcode design values directly in components
- Don't create custom variables outside the token system for design values
- Don't skip the numbering pattern when creating new token files

## Related Documentation

- **Foundation Variables:**
  [src/globals/foundation/README.md](../foundation/README.md) - Generated
  Variables
- **Fonts Configuration:** [src/globals/fonts/README.md](../fonts/README.md)
- **Mixins:** [src/globals/mixins/README.md](../mixins/README.md) - Using Design
  Tokens
- **Utils:** [src/globals/utils/README.md](../utils/README.md) - TypeScript
  Utilities
