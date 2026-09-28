import type { ArgTypes } from "@storybook/html-vite";
import type { CardTemplateArgs } from "./card.template";
import { iconNames } from "@base/icons/icon-map";

// Controls configuration
export const cardArgTypes: ArgTypes<CardTemplateArgs> = {
  cardImagePosition: {
    name: "Image position",
    table: { category: "Appearance" },
    if: { arg: "cardImage", neq: "" },
    control: {
      type: "select",
      labels: {
        "": "Default",
        "image-left": "Image Left",
        "image-right": "Image Right"
      }
    },
    options: ["", "image-left", "image-right"]
  },
  additionalCardAttributes: {
    name: "Theme",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: {
        "{}": "None",
        '{"data-theme":"blue-100"}': "Blue-100"
      }
    },
    options: ["{}", '{"data-theme":"blue-100"}'],
    mapping: {
      "{}": {},
      '{"data-theme":"blue-100"}': { "data-theme": "blue-100" }
    }
  },
  cardImage: {
    name: "Image",
    table: { category: "Content" },
    control: {
      type: "select",
      labels: {
        "": "None",
        "2:1": "2:1 Image",
        "4:3": "4:3 Image",
        "16:9": "16:9 Image"
      }
    },
    mapping: {
      "2:1": { imageSrc: "demo-image-2_1.jpg" },
      "4:3": { imageSrc: "demo-image-4_3.jpg" },
      "16:9": { imageSrc: "demo-image-16_9.jpg" }
    },
    options: ["", "2:1", "4:3", "16:9"]
  },
  cardTopline: {
    name: "Topline",
    table: { category: "Content" },
    control: { type: "boolean" },
    mapping: {
      true: "This is the card topline",
      false: ""
    }
  },
  cardHeading: {
    name: "Heading",
    table: { category: "Content" },
    control: { type: "boolean" },
    mapping: {
      true: "This is the card heading",
      false: ""
    }
  },
  cardHeadingStyle: {
    name: "Heading style",
    table: { category: "Appearance" },
    if: { arg: "cardHeading", truthy: true },
    control: {
      type: "select",
      labels: {
        2: "Style 2",
        3: "Style 3",
        4: "Style 4"
      }
    },
    options: [2, 3, 4]
  },
  cardText: {
    name: "Bodytext",
    table: { category: "Content" },
    control: { type: "boolean" },
    mapping: {
      true: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
      false: ""
    }
  },
  cardCtaText: {
    name: "CTA",
    table: { category: "Content" },
    if: { arg: "cardCtaType", neq: "heading" },
    control: { type: "boolean" },
    mapping: {
      true: "This is the card CTA",
      false: ""
    }
  },
  cardCtaIconBefore: {
    name: "CTA Icon before",
    table: { category: "Appearance" },
    control: {
      type: "select",
      labels: { "": "None" }
    },
    options: ["", ...iconNames]
  },
  hasDivider: {
    name: "Divider",
    table: { category: "Appearance" },
    control: { type: "boolean" }
  },
  cardExtraClasses: { table: { disable: true } }
};
