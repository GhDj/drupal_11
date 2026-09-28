type FormField = HTMLInputElement | HTMLTextAreaElement;

/**
 * Validation message config read from data-js-validation-messages on the form.
 *
 * Example shape:
 * {
 *   valueMissing: {
 *     default: "Please fill out this field.",
 *     textarea: "Please enter a message.",
 *     checkbox: "Please confirm this option."
 *   },
 *   typeMismatch: {
 *     default: "The value is invalid.",
 *     email: "Please enter a valid email address."
 *   }
 * }
 *
 * Supported top-level keys:
 * badInput, customError, patternMismatch, rangeOverflow, rangeUnderflow,
 * stepMismatch, tooLong, tooShort, typeMismatch, valueMissing
 */
type ValidationMessageConfig = Partial<
  Record<keyof ValidityState, Partial<Record<string, string>>>
>;

const lang = document.documentElement.lang;

const validationMessages: ValidationMessageConfig =
  lang === "de"
    ? {
        valueMissing: {
          default: "Bitte füllen Sie dieses Feld aus.",
          text: "Bitte geben Sie einen Wert ein.",
          textarea: "Bitte geben Sie eine Nachricht ein.",
          email: "Bitte geben Sie Ihre E-Mail-Adresse ein.",
          checkbox:
            "Bitte aktivieren Sie dieses Kontrollkästchen, um fortzufahren."
        },
        typeMismatch: {
          default: "Die Eingabe hat ein ungültiges Format.",
          email: "Bitte geben Sie eine gültige E-Mail-Adresse ein."
        },
        tooShort: {
          default: "Die Eingabe ist zu kurz."
        },
        patternMismatch: {
          default: "Das Format ist nicht korrekt."
        }
      }
    : {
        valueMissing: {
          default: "Please fill out this field.",
          text: "Please enter a value.",
          textarea: "Please enter a message.",
          email: "Please enter an email address.",
          checkbox: "Please check this box if you want to proceed."
        },
        typeMismatch: {
          default: "Please enter a valid value.",
          email: "Please enter a valid email address."
        },
        tooShort: {
          default: "Please lengthen this text."
        },
        patternMismatch: {
          default: "Please match the requested format."
        }
      };

/**
 * Returns the field type used for message lookup.
 * textarea is handled separately because it has no useful input.type.
 */
function getFieldType(input: FormField): string {
  if (input instanceof HTMLTextAreaElement) return "textarea";
  return input.type;
}

/**
 * Returns the first active ValidityState key for the field.
 * The order defines which error takes precedence if multiple flags are set.
 */
function getActiveValidityKey(input: FormField): keyof ValidityState | null {
  const keys: (keyof ValidityState)[] = [
    "badInput",
    "customError",
    "patternMismatch",
    "rangeOverflow",
    "rangeUnderflow",
    "stepMismatch",
    "tooLong",
    "tooShort",
    "typeMismatch",
    "valueMissing"
  ];

  return keys.find(key => input.validity[key]) ?? null;
}

/**
 * Resolves the validation message for the field.
 *
 * Lookup order:
 * 1. field-type-specific message from data-validation-messages
 * 2. default message for the active validity key
 * 3. browser-native validationMessage
 */
function getValidationMessage(input: FormField): string {
  const activeKey = getActiveValidityKey(input);
  if (!activeKey) return "";

  if (!validationMessages) return input.validationMessage;

  const messagesForKey = validationMessages[activeKey];
  const fieldType = getFieldType(input);

  return (
    messagesForKey?.[fieldType] ??
    messagesForKey?.default ??
    input.validationMessage
  );
}

export default function FormValidation() {
  const SELECTOR = "input, textarea";
  const ERROR_CLASS = "form-error";

  /**
   * Removes an existing error message and resets ARIA attributes
   */
  function clearError(input: FormField) {
    const parent = getParent(input);
    if (!parent) return;

    const error = parent.querySelector(`.${ERROR_CLASS}`);
    if (error) error.remove();

    input.removeAttribute("aria-describedby");
    input.removeAttribute("aria-invalid");
  }

  /**
   * Returns the element that should contain the error message.
   */
  function getParent(input: FormField) {
    return input.parentElement;
  }

  /**
   * Creates and displays a new error message for the given input
   */
  function showError(input: FormField) {
    const parent = getParent(input);
    if (!parent) return;

    const errorId = `${input.id || "input"}-error`;

    const errorEl = document.createElement("p");
    errorEl.className = ERROR_CLASS;
    errorEl.textContent = getValidationMessage(input);
    errorEl.id = errorId;

    input.setAttribute("aria-describedby", errorId);
    input.setAttribute("aria-invalid", "true");

    parent.appendChild(errorEl);
  }

  /**
   * Revalidate a field by clearing the previous state
   * and rendering a new error if the field is invalid.
   */
  function validate(input: FormField) {
    clearError(input);

    if (input.validity.valid) return;

    showError(input);
  }

  /**
   * Handle invalid events on submit.
   * The listener runs in capture phase because "invalid" does not bubble.
   */
  document.addEventListener(
    "invalid",
    e => {
      const target = e.target;
      if (!(
        target instanceof HTMLInputElement ||
        target instanceof HTMLTextAreaElement
      )) {
        return;
      }
      if (!target.matches(SELECTOR)) return;

      e.preventDefault();

      validate(target);

      document.querySelector<HTMLElement>(`[aria-invalid="true"]`)?.focus();
    },
    true
  );

  /**
   * Handle live validation while the user edits the field.
   */
  document.addEventListener("input", e => {
    const target = e.target;
    if (!(
      target instanceof HTMLInputElement ||
      target instanceof HTMLTextAreaElement
    )) {
      return;
    }
    if (!target.matches(SELECTOR)) return;

    validate(target);
  });
}
