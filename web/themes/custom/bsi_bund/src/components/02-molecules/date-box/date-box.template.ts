import { nsp, html } from "@globals/index";

export interface DateBoxTemplateArgs {
  dates: {
    year?: string;
    day?: string;
    month?: string;
    weekday?: string;
    series?: false;
  }[];
}

export function dateBoxTemplate({
  dates = [
    { year: "2026", day: "28", month: "September", weekday: "Donnerstag" }
  ]
}: DateBoxTemplateArgs): string {
  const variantClass =
    dates.length > 2
      ? "date-box--series"
      : dates.length === 2
        ? "date-box--range"
        : "";
  const dateSection = dates
    .map(date => {
      return html` <div class="${nsp("date-box__date")}">
        <span class="${nsp("date-box__month")}">${date.month}</span>
        ${date.day ? `<span class="${nsp("date-box__day")}">${date.day}</span>` : ""}
        ${date.weekday ? `<span class="${nsp("date-box__weekday")}">${date.weekday}</span>` : ""}
        ${date.year ? `<span class="${nsp("date-box__year")}">${date.year}</span>` : ""}
      </div>`;
    })
    .join("");
  return html`
    <div class="${nsp("date-box", variantClass)}">${dateSection}</div>
  `;
}
