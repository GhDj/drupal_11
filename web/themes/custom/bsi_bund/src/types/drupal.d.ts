export {};

declare global {
  /* -------------------------
    Drupal Global
  ------------------------- */
  interface Window {
    Drupal?: {
      behaviors?: Record<
        string,
        {
          attach: (context: ParentNode) => void;
        }
      >;
    };
  }

  /* -------------------------
    Drupal once()
  ------------------------- */
  function once(id: string, elements: NodeListOf<Element>): Element[];

  function once(id: string, element: Element): Element;
}
