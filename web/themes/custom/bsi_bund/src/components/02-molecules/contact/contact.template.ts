import { html, nsp } from "@globals/index";

import { headingTemplate } from "@atoms/heading/heading.template";
import { iconTemplate } from "@atoms/icon/icon.template";
import { linkTemplate } from "@molecules/link/link.template";
import { listTemplate } from "@molecules/list/list.template";

export interface ContactTemplateArgs {
  iconName?: string;
  title?: string;
  email?: string;
  emailTitle?: string;
  phone?: string;
  phoneTitle?: string;
  serviceNumber?: string;
  serviceTitle?: string;
}

export function contactTemplate(args: ContactTemplateArgs = {}): string {
  const {
    iconName = "multi-user",
    title = "Service-Center des BSI und Behoerdennummer",
    email = "service-center@bsi.bund.de",
    emailTitle = "E-Mail: service-center@bsi.bund.de",
    phone = "0800 274 1000",
    phoneTitle = "Telefon Buergerservice: 0800 274 1000",
    serviceNumber = "115",
    serviceTitle = "Ihre Behoerdennummer: 115"
  } = args;

  return html`
    <div class="${nsp("contact")}">
      ${iconTemplate({
        iconName,
        iconExtraClasses: "contact__icon"
      })}

      <div class="${nsp("contact__content")}">
        ${headingTemplate({
          headingText: title,
          layout: 2,
          style: 5,
          headingExtraClasses: "contact__title"
        })}
        ${listTemplate({
          items: [
            email
              ? linkTemplate({
                  linkUrl: `mailto:${email}`,
                  linkText: email,
                  linkTitle: emailTitle,
                  linkIconBefore: "mail",
                  linkExtraClasses: "contact__link"
                })
              : "",
            phone
              ? linkTemplate({
                  linkUrl: `tel:${phone}`,
                  linkText: phone,
                  linkTitle: phoneTitle ?? `Telefon: ${phone}`,
                  linkIconBefore: "phone",
                  linkExtraClasses: "contact__link"
                })
              : "",
            serviceNumber
              ? linkTemplate({
                  linkUrl: `tel:${serviceNumber}`,
                  linkText: serviceNumber,
                  linkTitle:
                    serviceTitle ?? `Ihre Behoerdennummer: ${serviceNumber}`,
                  linkIconBefore: "phone",
                  linkExtraClasses: "contact__link"
                })
              : ""
          ],
          variant: "unstyled",
          extraClasses: "contact__details"
        })}
      </div>
    </div>
  `;
}
