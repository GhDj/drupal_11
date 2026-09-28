/**
 * Renders a job facts overview displaying key information about a job posting.
 * The facts are shown as a list of items with an icon, label and value.
 * Only facts with a defined value are rendered.
 *
 * @param jobFacts - Optional: Object containing job-related information such as location, degree, start date, contract type and reference number.
 * @param extraClasses - Optional: Extra classes for the root container element.
 *
 * @example
 * import { jobFactsTemplate } from "@organisms/job-facts/job-facts.template";
 *
 * jobFactsTemplate({
 *   jobFacts: {
 *     location: "Berlin",
 *     degree: "Master/Diplom",
 *     startDate: "01.08.2026",
 *     contractType: "Unbefristet",
 *     employmentType: "Vollzeit",
 *     payGrade: "E9",
 *     applicationDeadline: "31.06.2026",
 *     referenceNumber: "V-2026-23"
 *   }
 * });
 */

// Globals
import { html, nsp } from "@globals/index";

// Components
import { iconTemplate } from "@atoms/icon/icon.template";

// Types
export interface JobFactsData {
  location?: string;
  degree?: string;
  startDate?: string;
  contractType?: string;
  employmentType?: string;
  payGrade?: string;
  applicationDeadline?: string;
  referenceNumber?: string;
}

interface JobFacts {
  label: string;
  icon: string;
  value?: string | undefined;
}

export interface JobFactsTemplateArgs {
  jobFacts?: JobFactsData;
  extraClasses?: string;
}

// Template
export function jobFactsTemplate({
  jobFacts = {
    location: "Berlin",
    degree: "Master/Diplom",
    startDate: "01.08.2026",
    contractType: "Unbefristet",
    employmentType: "Vollzeit",
    payGrade: "E9",
    applicationDeadline: "31.06.2026",
    referenceNumber: "V-2026-23"
  },
  extraClasses = ""
}: JobFactsTemplateArgs = {}): string {
  const jobFactItems: JobFacts[] = [
    {
      label: "Standort",
      icon: "pin",
      value: jobFacts.location
    },
    {
      label: "Abschluss",
      icon: "diploma",
      value: jobFacts.degree
    },
    {
      label: "Besetzung zum",
      icon: "calendar-date",
      value: jobFacts.startDate
    },
    {
      label: "Befristung",
      icon: "document",
      value: jobFacts.contractType
    },
    {
      label: "Beschäftigungsmodel",
      icon: "job",
      value: jobFacts.employmentType
    },
    {
      label: "Entgeltgruppe",
      icon: "money",
      value: jobFacts.payGrade
    },
    {
      label: "Bewerbungsfrist",
      icon: "calendar-event",
      value: jobFacts.applicationDeadline
    },
    {
      label: "Kennziffer",
      icon: "hash-mark",
      value: jobFacts.referenceNumber
    }
  ];

  return html`
    <div class="${nsp("job-facts", extraClasses)}">
      <div class="${nsp("job-facts__content")}">
        <ul class="${nsp("job-facts__list")}">
          ${jobFactItems
            .filter((fact): fact is JobFacts & { value: string } =>
              Boolean(fact.value)
            )
            .map(
              item => `
          <li class="${nsp("job-facts__item")}">
            <div class="${nsp("job-facts__icon")}">
              ${iconTemplate({
                iconName: item.icon,
                iconTitle: item.label
              })}
            </div>

            <div class="${nsp("job-facts__content")}">
              <div class="${nsp("job-facts__label")}">
                ${item.label}
              </div>

              <div class="${nsp("job-facts__value")}">
                ${item.value}
              </div>
            </div>
          </li>
        `
            )
            .join("")}
        </ul>
      </div>
    </div>
  `;
}
