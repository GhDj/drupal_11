# Setup Checklist

Use this checklist to guide you through the first-time setup of the Storybook
Blueprint project. Check off each item as you complete it.

## ⚠️⚠️⚠️ Before you continue with this checklist READ and UNDERSTAND the `README.md` ⚠️⚠️⚠️

## Prerequisites

- [ ] **Node.js Version**: Verify you have Node.js version 22 or higher
      installed

  ```bash
  node --version
  ```

  - If needed, install Node.js from [nodejs.org](https://nodejs.org/)

## Initial Setup

### 1. Install Dependencies

- [ ] Install all project dependencies
  ```bash
  npm install
  ```

### 2. Configure CSS Namespace

The namespace is used as a prefix for all CSS class names (e.g., `nsp-button`,
`nsp-card`).

- [ ] Open `src/globals/config/tokens.01-general.json`
- [ ] Update the `NSP` value from `"nsp"` to your project namespace:

  ```json
  {
    "NSP": "your-project-prefix"
  }
  ```

- [ ] Regenerate variables from tokens:
  ```bash
  npm run tokens:generate
  ```
- [ ] Verify that custom-properties and variables in `src/globals/foundation/`
      were updated with your new namespace

### 3. Update Package Metadata

Customize `package.json` with your project information.

- [ ] Open `package.json` in your editor
- [ ] Update the following fields:
  - [ ] `name`: Change from `"storybook-js-blueprint"` to your package name
  - [ ] `description`: Update with your project description
  - [ ] `repository.url`: Update with your Git repository URL
- [ ] Save the file

### 4. Verify Installation

Test that everything is working correctly.

- [ ] Start Storybook development server:
  ```bash
  npm run storybook
  ```
- [ ] Open browser to `http://localhost:6006`
- [ ] Verify that Storybook loads without errors
- [ ] Browse through the component library
- [ ] Check that your namespace appears in the class names (inspect elements in
      browser DevTools)

### 5. Run Quality Checks

Ensure code quality and tests pass.

- [ ] Run all linting checks:

  ```bash
  npm run lint
  ```

  - This runs TypeScript linting, SCSS linting, format checking, and type
    checking
  - Fix any errors that appear

- [ ] Run component tests:

  ```bash
  npm run test:components
  ```

  - This builds Storybook and runs visual regression tests
  - If tests fail due to namespace changes, update snapshots:
    ```bash
    npm run test:visual:approve
    ```

### 6. Build the Library

Create a production build to verify the build process works.

- [ ] Build the library:
  ```bash
  npm run build
  ```
- [ ] Verify that `dist/` directory was created
- [ ] Check that `dist/` contains:
  - `index.js` - Compiled JavaScript
  - `index.css` - Compiled stylesheets

## Post-Setup

### Documentation Updates

- [ ] Review and update `README.md` if needed
- [ ] Add project-specific documentation as needed

### Optional Customizations

- [ ] Configure Storybook addons in `.storybook/main.ts`

---

.

# Setup Complete! 🎉

You're now ready to build your component library.

---

.

## Next Steps

Once you've completed the initial setup, you can customize and extend the
blueprint for your needs:

### Customize Design Tokens

You can customize colors, spacing, typography, fonts, and breakpoints to match
your design system.

- [ ] Review token files in `src/globals/config/`:
  - [ ] **`tokens.01-general.json`** - Namespace configuration
  - [ ] **`tokens.02-breakpoint.json`** - Responsive breakpoints
    - Default breakpoints: mobile (352px), tablet (834px), laptop (1024px),
      desktop (1432px)
    - You may have to edit the breakpoints in `.storybook/viewports.ts`, to
      match the viewport-addon to your breakpoints
  - [ ] **`tokens.03-color.json`** - Color palette
    - Define your brand colors, semantic colors, and color scales
  - [ ] **`tokens.04-space.json`** - Spacing scale
    - Update spacing values (xs, sm, md, lg, xl, etc.)
  - [ ] **`tokens.05-typography.json`** - Typography styles
    - Configure font sizes, line heights, letter spacing
  - [ ] **`tokens.06-font.json`** - Font family and weights
    - Update font families and available weights

- [ ] After editing any token files, regenerate the variables:
  ```bash
  npm run tokens:generate
  ```
- [ ] Verify that generated files in `src/globals/foundation/` reflect your
      changes:
  - `variables.ts` - TypeScript constants
  - `_variables.scss` - SCSS variables
  - `_custom-properties.scss` - CSS custom properties

### Update Font Files

If you're using custom fonts:

- [ ] Add your font files to `src/assets/default/fonts/`
- [ ] Update `src/components/00-base/fonts/_fonts.scss` with new `@font-face`
      declarations
- [ ] Update `tokens.06-font.json` with your font family names and weights
- [ ] Run `npm run tokens:generate` to regenerate variables

### Update Icons

To add your own icon set:

- [ ] Add SVG icon files to `src/assets/default/icons/`
  - Icons are auto-imported via `src/components/00-base/icons/icon-map.ts`
  - Use kebab-case filenames (e.g., `arrow-right.svg`)
- [ ] Remove default icons if not needed
- [ ] Verify icons appear in Storybook (restart if needed)
