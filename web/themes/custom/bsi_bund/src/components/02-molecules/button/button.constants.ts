export const buttonVisualVariants = [
  "primary",
  "secondary",
  "tertiary",
  "destructive",
  "success"
] as const;

export const inlineActionVariants = ["close", "back", "expand"] as const;

export const buttonVariants = [
  ...buttonVisualVariants,
  ...inlineActionVariants
] as const;

export const buttonStates = [
  "default",
  "hover",
  "active",
  "focused",
  "disabled"
] as const;

export const buttonModes = ["default", "on-color"] as const;

export const buttonSizes = ["default", "lg", "md", "sm"] as const;

export const basicButtonSizes = ["default", "sm"] as const;

export const controlButtonSizes = ["lg", "md", "sm"] as const;

export const controlButtonStates = ["default", "hover", "active"] as const;
