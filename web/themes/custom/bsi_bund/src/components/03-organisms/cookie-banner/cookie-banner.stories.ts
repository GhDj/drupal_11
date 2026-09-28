import type { Meta, StoryObj } from "@storybook/html-vite";
import { cookieBannerTemplate } from "./cookie-banner.template";
import { html } from "@globals/index";

const meta: Meta = {
  title: "Organisms/Cookie Banner",
  render: () => cookieBannerTemplate(),
  tags: ["mobile", "desktop"]
};

export default meta;
type Story = StoryObj;

// export const Default: Story = {};

export const CookieBanner: Story = {
  render: () => html` ${cookieBannerTemplate()} `
};
