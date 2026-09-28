import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { contactTemplate, type ContactTemplateArgs } from "./contact.template";

import {
  contactDetailTemplate,
  type ContactDetailTemplateArgs
} from "./contact-detail.template";

// Register the story and its metadata
export default {
  title: "Molecules/Contact",
  render: contactTemplate,

  // Define, what snapshots should be created
  tags: ["mobile", "laptop"]
} satisfies Meta<ContactTemplateArgs>;

// Reusable Story type based on the template args
export const Default: StoryObj<ContactTemplateArgs> = {};

// Story for contact detail
export const Detail = {
  render: contactDetailTemplate,
  args: {
    title: "Pressekontakt",
    linkItems: [
      {
        label: "Adresse",
        text: "Bonner Talweg 100<br>Postfach: 12345<br>53113 Bonn<br>"
      },
      {
        label: "E-Mail",
        text: "presse@bsi.bund.de",
        url: "mailto:presse@bsi.bund.de"
      },
      {
        label: "Tel",
        text: "+49 228 99 9582-0",
        url: "tel:+492289995820"
      },
      {
        label: "Website",
        text: "www.bsi.bund.de",
        url: "https://www.bsi.bund.de"
      }
    ]
  }
} satisfies StoryObj<ContactDetailTemplateArgs>;
