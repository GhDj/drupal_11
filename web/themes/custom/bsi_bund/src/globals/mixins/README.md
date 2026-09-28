## Mixins - Reusable SCSS Mixins

This directory contains reusable SCSS mixins that provide consistent styling
patterns across the project. All mixins are built on top of the design tokens
from `src/globals/foundation/`.

## Table of Contents

- [Files](#files)
- [Available Mixins](#available-mixins)
  - [Breakpoints](#breakpoints)
  - [Typography](#typography)
  - [Font-Face](#font-face)
- [Import Patterns](#import-patterns)
- [Best Practices](#best-practices)
- [Related Documentation](#related-documentation)

## Files

- **`_index.scss`** - Entry point that forwards all mixins
- **`_breakpoints.scss`** - Responsive breakpoint mixins
- **`_typography.scss`** - Typography application mixins
- **`_font-face.scss`** - Font-face declaration mixins

## Available Mixins

### Breakpoints

---

Responsive breakpoint mixins for mobile-first design:

`breakpoint-sm` - Small screens and up

`breakpoint-md` - Tablets and up

`breakpoint-lg` - Desktops and up

`breakpoint-xl` - Large desktops and up

```scss
@use "@globals/mixins/breakpoints" as *;
// or
@use "@globals/mixins" as *;

.component {
  @include breakpoint-sm { ... }

  @include breakpoint-md { ... }

  @include breakpoint-lg { ... }

  @include breakpoint-xl { ... }
}
```

### Typography

---

The typography mixin applies consistent text styles from the design token
typography scale.

**Syntax**

```scss
@include typography(
  $variant,
  $font-weight: 400,
  $font-style: normal,
  $properties: font-family font-size line-height letter-spacing
);
```

**Parameters**

- **`$variant`** (required) - Key of predefined typography styles (see
  "typography" map in `src/globals/config/tokens.05-typography.json`)
- **`$font-weight`** (optional, default: 400) - Font weight value
- **`$font-style`** (optional, default: normal) - Font style (normal, italic,
  oblique)
- **`$properties`** (optional, default: font-family font-size line-height
  letter-spacing) - List of properties to apply

**Basic Usage**

```scss
@use "@globals/mixins/typography" as *;
// or
@use "@globals/mixins" as *;

.heading {
  @include typography("heading-1");
  // Applies:
  // font-size: clamp(2.25rem, 5vw, 3rem);
  // line-height: 1.2;
  // letter-spacing: -0.03125rem;
  // font-style: normal;
  // font-weight: 400;
}
```

**With Custom Weight and Style**

```scss
@use "@globals/mixins" as *;

.heading {
  @include typography("large", 700, italic);
  // Applies same properties but with:
  // - font-weight: 700
  // - font-style: italic
}
```

**Selective Properties**

```scss
@use "@globals/mixins" as *;

.text {
  @include typography("medium", $properties: font-size line-height);
  // Apply only font-size and line-height, skip letter-spacing
}
```

## Import Patterns

```scss
// Import all mixins without namespace
@use "@globals/mixins" as *;
```

```scss
// Import with namespace
@use "@globals/mixins" as mx;
```

```scss
// Import specific mixin modules
@use "@globals/mixins/breakpoints" as *;
@use "@globals/mixins/typography" as *;
```

## Best Practices

### DO

- Use breakpoint mixins for all responsive styles
- Use the typography mixin for consistent text styling
- Import mixins at the top of your SCSS files

### DON'T

- Don't write raw media queries - use breakpoint mixins instead
- Don't hardcode font sizes - use the typography mixin
- Don't override typography properties set by the typography mixin without good
  reason
- Don't manually write `@font-face` declarations - use the font system

## Related Documentation

- **Design Tokens Configuration:**
  [src/globals/config/README.md](../config/README.md) - Typography and
  breakpoint tokens
- **Foundation Variables:**
  [src/globals/foundation/README.md](../foundation/README.md) - SCSS variables
  used by mixins
- **Fonts:** [src/globals/fonts/README.md](../fonts/README.md) - Font
  configuration and font-face mixins
- **Utils:** [src/globals/utils/README.md](../utils/README.md) - TypeScript
  utilities
