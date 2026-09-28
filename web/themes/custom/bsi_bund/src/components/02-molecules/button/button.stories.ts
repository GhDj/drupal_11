import type { Meta, StoryObj } from "@storybook/html-vite";

// Components
import { buttonTemplate, type ButtonTemplateArgs } from "./button.template";

// Controls
import { buttonArgTypes } from "./button.controls";
import {
  basicButtonSizes,
  buttonStates,
  inlineActionVariants
} from "./button.constants";

// Register the story and its metadata
export default {
  title: "Molecules/Button",
  render: buttonTemplate,

  // Define, what snapshots should be created
  tags: ["mobile"],

  // Default values for storybook controls
  args: {
    buttonType: "button",
    buttonVariant: "primary",
    buttonState: "default",
    buttonIconBefore: "",
    buttonIconAfter: ""
  },

  // Controls shown in the "Controls" panel
  argTypes: buttonArgTypes
} satisfies Meta<ButtonTemplateArgs>;

// Reusable Story type based on the template args
type Story = StoryObj<ButtonTemplateArgs>;

function fallback<T>(value: T | null | undefined | "", fallbackValue: T): T {
  if (value) {
    return value;
  }

  return fallbackValue;
}

// Story for basic button
export const Basic: Story = {
  args: {
    buttonText: "Button Text",
    buttonSize: "default"
  },
  argTypes: {
    buttonMode: { table: { disable: true } },
    buttonSize: {
      name: "Button size",
      table: { category: "Appearance" },
      control: { type: "select" },
      options: basicButtonSizes,
      labels: {
        default: "Default",
        sm: "Small"
      }
    }
  }
};

// Story for icon as button
export const OnlyIcon: Story = {
  args: {
    buttonIconBefore: "arrow-right",
    buttonAriaLabel: "This label should describe the triggered action"
  },
  argTypes: {
    buttonIconBefore: {
      name: "Icon"
    },
    buttonIconAfter: { table: { disable: true } },
    buttonMode: { table: { disable: true } },
    buttonSize: { table: { disable: true } },
    buttonText: { table: { disable: true } }
  }
};

export const ControlButton: Story = {
  args: {
    buttonIconBefore: "cross",
    buttonAriaLabel: "Close",
    buttonSize: "lg",
    buttonMode: "default"
  },
  argTypes: {
    buttonIconBefore: {
      name: "Icon"
    },
    buttonIconAfter: { table: { disable: true } },
    buttonVariant: { table: { disable: true } },
    buttonText: { table: { disable: true } }
  },
  render: args => {
    const buttonMode = args.buttonMode ?? "default";
    const button = buttonTemplate({
      ...args,
      buttonIconBefore: fallback(args.buttonIconBefore, "cross"),
      buttonAriaLabel: fallback(args.buttonAriaLabel, "Close"),
      buttonSize: args.buttonSize ?? "lg",
      buttonMode,
      buttonState: args.buttonState ?? "default"
    });

    return `
      <div style="${buttonMode === "on-color" ? "background: var(--bsi-color-blue-750);" : ""} display: inline-flex; padding: 32px;">
        ${button}
      </div>
    `;
  }
};

export const InlineAction: Story = {
  name: "Inline Action",
  args: {
    buttonVariant: "close",
    buttonText: "Close Text",
    buttonMode: "default"
  },
  argTypes: {
    buttonIconAfter: { table: { disable: true } },
    buttonIconBefore: { table: { disable: true } },
    buttonSize: { table: { disable: true } },
    buttonVariant: {
      name: "Variant",
      table: { category: "Appearance" },
      control: { type: "select" },
      options: inlineActionVariants
    },
    buttonState: {
      name: "Button state",
      table: { category: "Appearance" },
      control: { type: "select" },
      options: buttonStates.filter(state => state !== "active")
    }
  },
  render: args => {
    const buttonMode = args.buttonMode ?? "default";
    const button = buttonTemplate({
      ...args,
      buttonMode,
      buttonState: args.buttonState ?? "default"
    });

    return `
      <div style="${buttonMode === "on-color" ? "background: var(--bsi-color-blue-750);" : ""} display: inline-flex; padding: 32px;">
        ${button}
      </div>
    `;
  }
};

export const ButtonDock: Story = {
  name: "Button Dock",
  args: {
    buttonText: "Button Text",
    buttonVariant: "primary",
    buttonState: "default",
    buttonExtraClasses: "button--dock"
  },
  argTypes: {
    buttonIconAfter: { table: { disable: true } },
    buttonIconBefore: { table: { disable: true } },
    buttonMode: { table: { disable: true } },
    buttonSize: { table: { disable: true } },
    buttonExtraClasses: { table: { disable: true } }
  },
  render: args => {
    const root = document.createElement("div");

    root.style.padding = "32px";
    root.insertAdjacentHTML(
      "beforeend",
      buttonTemplate({
        ...args,
        buttonExtraClasses: "button--dock"
      })
    );

    return root;
  }
};
