const selector = "[data-bsi-60-sec]";
const initializedAttribute = "data-bsi-60-sec-initialized";
const targetAttribute = "data-bsi-60-sec-target";
const questionSelector = "[data-bsi-60-sec-question]";
const panelSelector = "[data-bsi-60-sec-panel]";

function getItems(component: HTMLElement) {
  return {
    questions: Array.from(
      component.querySelectorAll<HTMLButtonElement>(questionSelector)
    ),
    panels: Array.from(component.querySelectorAll<HTMLElement>(panelSelector))
  };
}

function setActivePanel(component: HTMLElement, panelId: string): void {
  const { questions, panels } = getItems(component);

  questions.forEach(question => {
    const isActive = question.getAttribute(targetAttribute) === panelId;

    question.setAttribute("aria-selected", isActive ? "true" : "false");
    question.tabIndex = isActive ? 0 : -1;
  });

  panels.forEach(panel => {
    const isActive = panel.id === panelId;

    panel.hidden = !isActive;
    panel.tabIndex = isActive ? 0 : -1;
  });
}

function handleQuestionKeydown(event: KeyboardEvent): void {
  if (!(event.currentTarget instanceof HTMLButtonElement)) {
    return;
  }

  const question = event.currentTarget;
  const component = question.closest<HTMLElement>(selector);
  const panelId = question.getAttribute(targetAttribute);

  if (!component || !panelId) {
    return;
  }

  const { questions } = getItems(component);
  const currentIndex = questions.indexOf(question);
  const lastIndex = questions.length - 1;
  let nextQuestion: HTMLButtonElement | undefined;

  switch (event.key) {
    case "ArrowDown":
    case "ArrowRight":
      nextQuestion =
        questions[currentIndex === lastIndex ? 0 : currentIndex + 1];
      break;

    case "ArrowUp":
    case "ArrowLeft":
      nextQuestion =
        questions[currentIndex === 0 ? lastIndex : currentIndex - 1];
      break;

    case "Home":
      nextQuestion = questions[0];
      break;

    case "End":
      nextQuestion = questions[lastIndex];
      break;

    default:
      return;
  }

  event.preventDefault();

  if (nextQuestion) {
    const nextPanelId = nextQuestion.getAttribute(targetAttribute);

    if (nextPanelId) {
      setActivePanel(component, nextPanelId);
      nextQuestion.focus();
    }
  }
}

function handleQuestionClick(event: MouseEvent): void {
  if (!(event.currentTarget instanceof HTMLButtonElement)) {
    return;
  }

  const component = event.currentTarget.closest<HTMLElement>(selector);
  const panelId = event.currentTarget.getAttribute(targetAttribute);

  if (component && panelId) {
    setActivePanel(component, panelId);
  }
}

function initSixtySecComponent(component: HTMLElement): void {
  if (component.hasAttribute(initializedAttribute)) {
    return;
  }

  const { questions, panels } = getItems(component);

  component.setAttribute(initializedAttribute, "true");

  if (!questions.length || !panels.length) {
    return;
  }

  const activeQuestion =
    questions.find(
      question => question.getAttribute("aria-selected") === "true"
    ) ?? questions[0];

  if (!activeQuestion) {
    return;
  }

  const activePanelId = activeQuestion.getAttribute(targetAttribute);

  if (activePanelId) {
    setActivePanel(component, activePanelId);
  }

  questions.forEach(question => {
    question.addEventListener("click", handleQuestionClick);
    question.addEventListener("keydown", handleQuestionKeydown);
  });
}

export function initSixtySec(context: ParentNode = document): void {
  const components = Array.from(
    context.querySelectorAll<HTMLElement>(selector)
  );

  components.forEach(initSixtySecComponent);
}

initSixtySec();

document.addEventListener("DOMContentLoaded", () => initSixtySec());

declare global {
  interface Window {
    Drupal?: {
      behaviors?: Record<string, { attach: (context: ParentNode) => void }>;
    };
  }
}

if (window.Drupal?.behaviors) {
  window.Drupal.behaviors.bsiSixtySec = {
    attach(context: ParentNode) {
      initSixtySec(context);
    }
  };
}
