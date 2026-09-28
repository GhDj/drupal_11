import type { Meta, StoryObj } from "@storybook/html-vite";
import { cookieModalTemplate } from "./cookie-modal.template";
import { html } from "@globals/index";

const meta: Meta = {
  title: "Organisms/Cookie Modal",
  render: () => cookieModalTemplate(),
  tags: ["mobile", "desktop"]
};

export default meta;
type Story = StoryObj;

export const CookieModal: Story = {
  render: () => html` ${cookieModalTemplate()} `
};
