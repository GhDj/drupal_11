# 🧩 Component Guidelines

## 🎯 Goal

Build components that are:

- consistent
- scalable
- easy to maintain
- compatible with Storybook and Drupal

---

## ✅ 1. Core Principle

> Components are **content-driven, not state-driven**

---

### ❌ Avoid

```ts
showImage: true;
showTitle: false;
showLink: true;
```

---

### ✅ Prefer

```ts
imageSrc?: string
title?: string
linkText?: string
```

👉 Behaviour is derived from content:

```ts
const hasImage = Boolean(imageSrc);
```

---

## ✅ 2. Component Structure

Each component follows the same structure:

```
component/
├── component.template.ts
├── component.controls.ts
├── component.stories.ts
└── component.constants.ts (optional)
```

---

## ✅ 3. Responsibilities

### 🔹 template.ts

Responsible for:

- HTML rendering
- logic (e.g. `hasImage`, `hasLink`)
- class generation

---

### 🔹 controls.ts

Responsible for:

- Storybook controls

## ✅ Keep simple

### 🔹 stories.ts

Responsible for:

- examples / variants

✅ minimal ❌ no layout hacks ❌ no inline HTML

---

### 🔹 constants.ts (optional)

Contains:

- variants
- select options
- reusable values

---

## ✅ 4. Props Design

👉 Props must describe **content**, not behaviour

---

### ✅ Do

```ts
title?: string
date?: string
imageSrc?: string
linkText?: string
```

---

### ❌ Don't

```ts
cardShowImage?: boolean
cardEnableLink?: boolean
```

---

## ✅ 5. Rendering Logic

👉 Logic belongs in the template

```ts
const hasLink = Boolean(linkText);

return hasLink ? `<a>...</a>` : "";
(use our link - or what needed component, if available!)
```

---

### ❌ Not in controls

```ts
if: { arg: "showLink" }
```

---

## ✅ 6. Styling Convention

Use namespace + modifier pattern:

```
card
card--featured
card--no-image
```

---

### Example

```ts
const modifierClasses = [
  variant !== "default" ? `card--${variant}` : "",
  !hasImage ? "card--no-image" : ""
]
  .filter(Boolean)
  .join(" ");
```

---

## ✅ 7. Storybook Guidelines

### ✅ Do

- provide a clear default
- keep stories minimal
- expose only useful controls

---

### ❌ Don’t

- dynamic argTypes
- inline HTML in stories
- excessive disabled controls
- external YAML/JSON files

---

## ✅ 8. No External Story Data

❌ Avoid

- `.yml`
- `.json`

✅ Use

```ts
constants.ts;
```

---

## ✅ 9. Composition

Components should use other components:

```ts
headingTemplate();
buttonTemplate();
badgeTemplate();
```

---

✅ ensures consistency ✅ reduces duplication

---

## ✅ 10. Default Values

Set defaults directly in the template:

```ts
linkText = "Learn more";
```

---

## ✅ 11. Accessibility

- use semantic HTML
- always provide `alt`
- avoid fake clickable elements

---

## 🎯 TL;DR

- Content-driven over state-driven
- Keep components simple
- Avoid unnecessary abstraction
- Stay consistent across the system
