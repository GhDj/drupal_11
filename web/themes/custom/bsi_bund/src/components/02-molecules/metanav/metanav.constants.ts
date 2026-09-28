import type { MetaNavLink } from "./metanav.template";

export const defaultLinks: MetaNavLink[] = [
  {
    url: "/",
    text: "English",
    extraClasses: "metanav__language",
    iconAfter: "globe"
  },
  {
    url: "/",
    text: "Gebärdensprache",
    extraClasses: "metanav__sign-language",
    iconAfter: "sign-language"
  },
  {
    url: "/",
    text: "Leichte Sprache",
    extraClasses: "metanav__easy-language",
    iconAfter: "easy"
  },
  {
    url: "/",
    text: "Kontakt",
    extraClasses: "metanav__contact",
    iconAfter: "mail"
  },
  {
    url: "/",
    text: "BSI-Portal",
    extraClasses: "metanav__portal",
    iconAfter: "external-link"
  },
  { url: "/", text: "Login", extraClasses: "metanav__login", iconAfter: "user" }
];
