import type { ArgTypes } from "@storybook/html";

import type { FooterTemplateArgs } from "./footer.template";

export const footerArgTypes: ArgTypes<FooterTemplateArgs> = {
  socialMediaHeading: {
    control: "text",
    table: {
      category: "Content"
    }
  },

  imageAlt: {
    control: "text",
    table: {
      category: "Media"
    }
  },

  navigationLinks: {
    control: "object",
    table: {
      category: "Navigation"
    }
  },

  socialMediaLinks: {
    control: "object",
    table: {
      category: "Navigation"
    }
  },

  serviceLinks: {
    control: "object",
    table: {
      category: "Navigation"
    }
  },

  linkButtonText: {
    control: "text",
    table: {
      category: "CTA"
    }
  },

  copyright: {
    control: "text",
    table: {
      category: "Content"
    }
  }
};
