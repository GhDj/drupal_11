## Utils - TypeScript Utility Functions

This directory contains TypeScript utility functions for component templates.
These utilities provide type-safe helpers for HTML generation and class name
management.

## Table of Contents

- [Files](#files)
- [Available Utilities](#available-utilities)
  - [`nsp()`](#nsp)
  - [`html`](#html)
- [Import Patterns](#import-patterns)
- [Best Practices](#best-practices)
- [Related Documentation](#related-documentation)

## Files

- **`index.ts`** - Entry point that exports all utilities
- **`nsp.ts`** - Namespace utility for prefixing class names
- **`html.ts`** - HTML template literal tag for type-safe HTML strings

## Available Utilities

### `nsp()`

---

Adds the project namespace prefix to class names. This ensures consistent class
naming across all components and prevents naming conflicts.

**Syntax**

```typescript
nsp(...classNames: string[]): string
```

**Parameters**

- **`classNames`** (rest parameter) - One or more class names to prefix with the
  namespace

**Returns**

A space-separated string of namespaced class names.

**Usage Patterns**

The `nsp()` function supports multiple usage patterns for flexibility:

<u>**Single class:**</u>

```typescript
import { nsp } from "@globals";

nsp("button");
// Result: "nsp-button"
```

<u>**Multiple arguments:**</u>

```typescript
import { nsp } from "@globals";

nsp("button", "primary");
// Result: "nsp-button nsp-primary"
```

<u>**Space-separated string:**</u>

```typescript
import { nsp } from "@globals";

nsp("button primary");
// Result: "nsp-button nsp-primary"
```

<u>**Array spreading:**</u>

```typescript
import { nsp } from "@globals";

const classes = ["button", "primary", "large"];
nsp(...classes);
// Result: "nsp-button nsp-primary nsp-large"
```

<u>**Conditional Classes**</u>

```typescript
import { nsp } from "@globals";

const isActive = true;
const hasError = false;
const size = "large";

const classes = nsp(
  "card",
  isActive ? "card--active" : "",
  hasError ? "card--error" : "",
  `card--${size}`
);

// Result: "nsp-card nsp-card--active nsp-card--large"
```

---

### `html`

---

Template literal tag function for creating HTML strings with type safety.
Ensures all template placeholders are strings to prevent accidental injection of
non-string values.

**Syntax**

```typescript
html`template string ${placeholder}`;
```

**Parameters**

- **Template literal** - String template with placeholders

**Returns**

The constructed HTML string.

**Throws**

Error if any placeholder is not a string.

**Basic Usage**

```typescript
import { html } from "@globals";

const name = "World";
const greeting = html`<h1>Hello ${name}</h1>`;

// Result: "<h1>Hello World</h1>"
```

## Import Patterns

### Import Both Utilities

```typescript
// Recommended: Import from the main globals entry point
import { nsp, html } from "@globals";
```

```typescript
// Alternative: Import from utils module
import { nsp, html } from "@globals/utils";
```

### Import Individually

```typescript
// Import specific utilities from their modules
import { nsp } from "@globals/utils/nsp";
import { html } from "@globals/utils/html";
```

## Best Practices

### DO

- Always use `nsp()` for class names in component templates
- Always use `html` tag for template literals
- Convert non-string values to strings before using in templates
- Use conditional expressions for optional classes
- Follow BEM naming conventions within `nsp()`

### DON'T

- Don't manually prefix class names with the namespace
- Don't pass non-string values to `html` template placeholders
- Don't create class strings without `nsp()` in templates
- Don't use `nsp()` in SCSS files (use `$nsp` variable instead)

## Related Documentation

- **Design Tokens Configuration:**
  [src/globals/config/README.md](../config/README.md) - Namespace configuration
- **Foundation Variables:**
  [src/globals/foundation/README.md](../foundation/README.md) - `NSP` constant
  and TypeScript exports
