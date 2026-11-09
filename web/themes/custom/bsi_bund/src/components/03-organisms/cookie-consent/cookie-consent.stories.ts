import type { Meta, StoryObj } from "@storybook/html-vite";
import { cookieConsentTemplate } from "./cookie-consent.template";
import { html } from "@globals/index";

const meta: Meta = {
  title: "Organisms/Cookie Consent",
  render: () => cookieConsentTemplate(),
  tags: ["mobile", "desktop"]
};

export default meta;

type Story = StoryObj;

export const cookieConsent: Story = {
  render: () => html`${cookieConsentTemplate()}`
};
