# Storybook JS Blueprint

A TypeScript-based component library blueprint using Storybook, Vite, and SCSS.
Components follow Atomic Design principles with a template-driven architecture.

## Contents

- [Prerequisites](#prerequisites)
- [First Install](#first-install)
- [Quick Start](#quick-start)
- [Available Commands](#available-commands)
  - [Development](#development)
  - [Testing](#testing)
  - [Linting & Code Quality](#linting--code-quality)
- [Project Structure](#project-structure)
  - [Component Structure Pattern](#component-structure-pattern)
- [Key Technologies](#key-technologies)
- [Key Features](#key-features)
  - [TypeScript Path Aliases](#typescript-path-aliases)
  - [Global Namespace Utility](#global-namespace-utility)
  - [Icon System](#icon-system)
  - [Template System](#template-system)
  - [Quality Assurance](#quality-assurance)
- [Development Workflow](#development-workflow)
- [Git Hooks & Quality Checks](#git-hooks--quality-checks)
  - [What Happens on git commit](#what-happens-on-git-commit)
  - [What Happens on git push](#what-happens-on-git-push)
  - [Dealing with Linting Errors](#dealing-with-linting-errors)
  - [Dealing with Test Failures](#dealing-with-test-failures)
  - [Bypassing Hooks (Not Recommended)](#bypassing-hooks-not-recommended)
  - [Troubleshooting](#troubleshooting)
- [Additional Resources](#additional-resources)

## Prerequisites

- **Node.js:** Version 22 or higher (^22)
- **Playwright:** `npx playwright install`

## First Install

After you've finished reading this file, use the `FIRST_INSTALL.md` checklist
for the initial setup of this blueprint.

## Quick Start

These are the commands for daily development:

1. **Start Storybook development server**

   ```bash
   npm run storybook
   ```

   This will start Storybook on `http://localhost:6006`

2. **Build the library**

   ```bash
   npm run build
   ```

## Available Commands

If you have installed the
[tasks](https://marketplace.visualstudio.com/items?itemName=actboy168.tasks)-extension,
you can run the most important commands from your status bar.

### Development

| Command                   | Description                                        |
| ------------------------- | -------------------------------------------------- |
| `npm install`             | Install all project dependencies                   |
| `npm run storybook`       | Start Storybook dev server on port 6006            |
| `npm run build`           | Build the component library with Vite              |
| `npm run build:watch`     | Build library in watch mode for active development |
| `npm run build-storybook` | Build static Storybook for deployment              |

### Testing

| Command                       | Description                                                 |
| ----------------------------- | ----------------------------------------------------------- |
| `npm run test:components`     | Build Storybook and run vitest with visual regression tests |
| `npm run test:visual:approve` | Update visual regression test snapshots                     |

#### Controlling Visual Regression Tests with Tags

You can control which viewports are tested for visual regression snapshots by
adding tags to your stories. This helps reduce test time and snapshot storage by
only testing relevant viewports.

**Available Tags:**

- `no-snapshot` - Skip all snapshots for this story
- `mobile` - Only test mobile viewport
- `tablet` - Only test tablet viewport
- `laptop` - Only test laptop viewport
- `desktop` - Only test desktop viewport
- `all-viewports` - Test all viewports (default if no tags specified)

**Combining Tags:**

You can combine multiple viewport tags to test specific combinations:

```typescript
// Meta-level tags (applies to all stories in the file)
export default {
  /* ... */
  tags: ["mobile", "desktop"] // All stories test only mobile and desktop
  /* ... */
} satisfies Meta<MyTemplateArgs>;

// Story-level tags (applies to individual story)
export const MyStory: Story = {
  args: {
    /* ... */
  },
  tags: ["mobile", "desktop"] // Only test mobile and desktop
};
```

**Viewport Mapping:**

- `mobile` → smartphone (352px)
- `tablet` → tablet (834px)
- `laptop` → laptop (1024px)
- `desktop` → desktop (1432px)

### Linting & Code Quality

| Command               | Description                                      |
| --------------------- | ------------------------------------------------ |
| `npm run lint`        | Run all linting checks (TS, SCSS, format, types) |
| `npm run lint:ts`     | Lint TypeScript/JavaScript files with ESLint     |
| `npm run lint:scss`   | Lint SCSS files with Stylelint                   |
| `npm run lint:format` | Check code formatting with Prettier              |
| `npm run lint:types`  | Type-check TypeScript without emitting files     |

## Project Structure

This project follows **Atomic Design** methodology:

```
src/
├── components/
│   ├── 00-base/       # Foundation elements (icon definitions, etc.)
│   ├── 01-atoms/      # Basic building blocks (icon, heading, etc.)
│   ├── 02-molecules/  # Simple component groups (link, etc.)
│   ├── 03-organisms/  # Complex UI components
│   └── 04-templates/  # Page layouts
│   └── 05-pages/      # Full page compositions
├── globals/           # Global utilities, variables, and helpers
└── assets/            # Static assets (icons, images, etc.)
```

### Component Structure Pattern

Each component follows this structure:

```
component-name/
├── component-name.template.ts  # TypeScript template function
├── component-name.stories.ts   # Storybook story definition
├── component-name.controls.ts  # Storybook controls for the component
├── component-name.yml          # Default data/props
└── _component-name.scss        # Component styles
```

## Key Technologies

- **TypeScript**: Strict mode enabled for type safety
- **Vite**: Fast build tool and dev server with PostCSS processing
- **SCSS**: Styling with BEM-like conventions and namespace prefixing
- **Storybook**: Component development and documentation
- **Vitest**: Unit and browser testing with Playwright
- **ESLint + Prettier**: Code linting and formatting
- **Stylelint**: SCSS linting
- **Husky**: Git hooks for pre-commit validation

## Key Features

### TypeScript Path Aliases

The project uses path aliases for cleaner imports:

```typescript
import { ... } from "@globals"         // src/globals/*
import { ... } from "@atoms"           // src/components/01-atoms/*
import { ... } from "@base"            // src/components/00-base/*
import { ... } from "@molecules"       // src/components/02-molecules/*
import { ... } from "@organisms"       // src/components/03-organisms/*
import { ... } from "@templates"       // src/components/04-templates/*
import { ... } from "@pages"           // src/components/05-pages/*
import { ... } from "@demo-content"    // src/demo-content/*
```

### Global Namespace Utility

All components use the `nsp()` utility function to prefix class names:

```typescript
import { nsp } from "@globals";

nsp("button"); // => "nsp-button"
nsp("button", "primary"); // => "nsp-button nsp-primary"
```

The namespace is configurable in `src/globals/config/tokens.01-general.json`.

### Icon System

Icons are auto-imported from `src/assets/default/icons/`:

- Place SVG files in the icons directory
- Use the icon template with full accessibility support
- Includes ARIA attributes, roles, and optional titles/descriptions

### Template System

Components use TypeScript template functions with YAML data files:

- Type-safe template arguments with TypeScript interfaces
- YAML files for static component data
- Template functions return HTML strings

### Quality Assurance

- **Visual Regression Testing**: Automated screenshot comparison with Storybook
  Test Runner
- **Accessibility Testing**: Built-in a11y checks via Storybook addon
- **Pre-commit Hooks**: Husky runs linting and formatting checks before commits
- **Strict TypeScript**: Enhanced type safety with strict configuration

## Git Hooks & Quality Checks

This project uses [Husky](https://typicode.github.io/husky/) to automatically
run quality checks at specific points in your Git workflow. This ensures that
all committed code meets quality standards.

### What Happens on `git commit`

When you run `git commit`, the **pre-commit hook** automatically runs:

```bash
npm run lint
```

This executes all linting checks:

- **TypeScript/JavaScript linting** (ESLint)
- **SCSS linting** (Stylelint)
- **Code formatting** (Prettier)
- **Type checking** (TypeScript compiler)

**If any check fails, the commit will be aborted.** You must fix the issues
before you can commit.

### What Happens on `git push`

When you run `git push`, the **pre-push hook** automatically runs:

```bash
npm run test:components
```

This command:

1. Builds Storybook (`npm run build-storybook`)
2. Runs all component tests (`npm run test:storybook`)

**If any test fails, the push will be aborted.** You must fix the failing tests
before you can push.

### Dealing with Linting Errors

If the pre-commit hook fails due to linting errors:

1. **Review the error output** to see which files and rules are failing
2. **Fix automatically (where possible)**:

   ```bash
   npm run lint:ts -- --fix  # Auto-fix ESLint issues
   npx prettier --write "src/**/*.{js,ts,jsx,tsx,scss,yml,json,mdx,md}"  # Auto-fix formatting
   ```

3. **Fix manually** for issues that can't be auto-fixed
4. **Re-stage your changes** after fixing:

   ```bash
   git add .
   git commit -m "Your message"
   ```

### Dealing with Test Failures

If the pre-push hook fails due to test failures:

1. **Review the test output** to identify which stories are failing
2. **Check for visual changes**:
   - Snapshots that show visual changes are stored in
     `__snapshots__/__diff_output__`
   - If visual changes are **intentional** (you updated the component design):
     ```bash
     npm run test:visual:approve
     ```
     This updates the snapshots. Then commit and push the updated snapshots.
   - If visual changes are **unintentional** (regression):
     - Fix the component code to match the expected design
     - Run tests again: `npm run test:components`
3. **Check for accessibility violations**:
   - Review the a11y error output
   - Fix accessibility issues in your component
   - Re-run tests to verify
4. **After fixing**, run tests locally before pushing:
   ```bash
   npm run test:components
   ```

### Bypassing Hooks (Not Recommended)

In rare cases, you may need to bypass hooks:

```bash
git commit --no-verify  # Skip pre-commit hook
git push --no-verify    # Skip pre-push hook
```

**Warning**: Only use `--no-verify` in exceptional circumstances. Bypassing
hooks can introduce broken code into the repository.

## Additional Resources

- Component examples and patterns in `src/components/`
- Storybook configuration in `.storybook/`
- ESLint configuration in `eslint.config.js`
- Stylelint configuration in `stylelint.config.js`
