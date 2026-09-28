const modules = import.meta.glob<string>("../../../assets/**/icons/*.svg", {
  eager: true,
  query: "?raw",
  import: "default"
});

export const iconMap: Record<string, string> = Object.fromEntries(
  Object.entries(modules).map(([path, svg]) => [
    path.replace(/^.*\//, "").replace(/\.svg$/, ""),
    svg
  ])
);

/**
 * Array of canonical icon names.
 * Useful for Storybook controls and other scenarios where a list of icon names is needed.
 */
export const iconNames: string[] = Object.keys(iconMap).sort();
