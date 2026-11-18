<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Frontend profile form for CSN Ersthelfer:in (first_responder profile type).
 */
final class ProfileForm extends FormBase {

  /**
   * Fields that become read-only after initial submission.
   */
  private const LOCK_AFTER_INITIAL = [
    'field_academic_title',
    'field_given_name',
    'field_family_name',
    'field_deh_mail',
    'field_confirm_data_processing',
    'field_confirm_terms_conditions',
    'field_confirm_volunteer',
  ];

  /**
   * The profile entity being edited (or NULL for new).
   */
  private mixed $profileEntity = NULL;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsi_csn_profile_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $this->profileEntity = $this->loadProfile();
    $is_initial = $this->profileEntity === NULL;

    $account = \Drupal::entityTypeManager()->getStorage('user')->load(\Drupal::currentUser()->id());
    $email = $account?->getEmail() ?? '';
    $username = $account?->getAccountName() ?? '';

    $form['#theme'] = 'csn_profile_form';
    $form['#is_initial'] = $is_initial;
    $form['#user_email'] = Html::escape($email ?: $username);

    // -- Section: Kontaktdaten für CSN Team.
    $form['academic_title'] = $this->buildField('field_academic_title', $this->t('Titel'), FALSE);
    $form['given_name'] = $this->buildField('field_given_name', $this->t('Vorname'), TRUE);
    $form['family_name'] = $this->buildField('field_family_name', $this->t('Nachname'), TRUE);
    $form['phone'] = $this->buildField('field_phone', $this->t('Telefonnummer'), TRUE, [
      '#type' => 'tel',
      '#attributes' => ['placeholder' => '+49 ...'],
    ]);

    // Address subfields (address module stores as single field, we split in form).
    $address = $this->getAddressValues();
    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Straße, Hausnummer'),
      '#required' => TRUE,
      '#default_value' => $address['address_line1'],
      '#attributes' => ['class' => ['form-element']],
    ];
    $form['postal_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('PLZ'),
      '#required' => TRUE,
      '#default_value' => $address['postal_code'],
      '#attributes' => ['class' => ['form-element']],
      '#size' => 10,
    ];
    $form['locality'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Ort'),
      '#required' => TRUE,
      '#default_value' => $address['locality'],
      '#attributes' => ['class' => ['form-element']],
    ];
    $form['country_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Land'),
      '#required' => TRUE,
      '#default_value' => $address['country_code'] ?: 'DE',
      '#attributes' => ['class' => ['form-element']],
    ];

    // -- Section: Kontaktdaten für Hilfesuchende (DEH).
    $form['deh_phone'] = $this->buildField('field_deh_phone', $this->t('Telefonnummer'), FALSE, [
      '#type' => 'tel',
      '#attributes' => ['placeholder' => '+49 ...'],
    ]);
    $form['deh_mail'] = $this->buildField('field_deh_mail', $this->t('E-Mail-Adresse'), TRUE, [
      '#type' => 'email',
    ]);

    // Sprache — multi-select with chip widget.
    $form['deh_language'] = [
      '#type' => 'select',
      '#title' => $this->t('Sprache'),
      '#options' => $this->getLanguageOptions(),
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $this->getSelectedLanguageIds(),
      '#attributes' => [
        'data-csn-language-select' => TRUE,
        'data-placeholder' => $this->t('Bitte auswählen'),
        'class' => ['form-element'],
      ],
    ];

    // Lock fields after initial submission.
    if (!$is_initial) {
      foreach (self::LOCK_AFTER_INITIAL as $field_name) {
        $form_key = str_replace('field_', '', $field_name);
        if (isset($form[$form_key])) {
          $form[$form_key]['#disabled'] = TRUE;
          $form[$form_key]['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
        }
      }
    }

    // -- Section: Servicezeiten (Office Hours rendered as custom widget).
    // Office Hours stores data as [{day, starthours, endhours}, ...].
    $num_slots = $form_state->get('num_service_slots');
    if ($num_slots === NULL) {
      $num_slots = max(1, $this->countAvailabilitySlots());
      $form_state->set('num_service_slots', $num_slots);
    }
    // On submit, form state storage may not reflect AJAX-added rows.
    // Cross-check with user input to ensure all submitted rows are built.
    $input = $form_state->getUserInput();
    if (isset($input['service_times']) && is_array($input['service_times'])) {
      $input_count = 0;
      foreach ($input['service_times'] as $key => $value) {
        if (is_numeric($key) && is_array($value)) {
          $input_count++;
        }
      }
      if ($input_count > $num_slots) {
        $num_slots = $input_count;
        $form_state->set('num_service_slots', $num_slots);
      }
    }

    $form['service_times'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['csn-profile-service-times']],
      '#prefix' => '<div id="service-times-wrapper">',
      '#suffix' => '</div>',
    ];

    $weekdays = [
      '' => $this->t('- Bitte wählen -'),
      0 => $this->t('Sonntag'),
      1 => $this->t('Montag'),
      2 => $this->t('Dienstag'),
      3 => $this->t('Mittwoch'),
      4 => $this->t('Donnerstag'),
      5 => $this->t('Freitag'),
      6 => $this->t('Samstag'),
    ];

    for ($i = 0; $i < $num_slots; $i++) {
      $defaults = $this->getAvailabilitySlotDefaults($i);
      $form['service_times'][$i] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['csn-profile-service-time-row']],
      ];
      $form['service_times'][$i]['day'] = [
        '#type' => 'select',
        '#title' => $this->t('Tag'),
        '#options' => $weekdays,
        '#required' => TRUE,
        '#default_value' => $defaults['day'],
        '#attributes' => ['class' => ['form-element', 'form-element--type-select']],
      ];
      $form['service_times'][$i]['time_from'] = [
        '#type' => 'bsi_time',
        '#title' => $this->t('Von'),
        '#required' => TRUE,
        '#default_value' => $defaults['time_from'],
        '#attributes' => ['class' => ['form-element']],
      ];
      $form['service_times'][$i]['time_to'] = [
        '#type' => 'bsi_time',
        '#title' => $this->t('Bis'),
        '#required' => TRUE,
        '#default_value' => $defaults['time_to'],
        '#attributes' => ['class' => ['form-element']],
      ];
      if ($i > 0) {
        $form['service_times'][$i]['remove'] = [
          '#type' => 'submit',
          '#value' => $this->t('Löschen'),
          '#name' => 'remove_slot_' . $i,
          '#submit' => ['::removeSlotCallback'],
          '#ajax' => [
            'callback' => '::slotsAjaxCallback',
            'wrapper' => 'service-times-wrapper',
          ],
          '#attributes' => ['class' => ['csn-profile-service-time-remove']],
          '#limit_validation_errors' => [],
        ];
      }
    }

    $form['add_service_time'] = [
      '#type' => 'submit',
      '#value' => $this->t('Servicezeit hinzufügen'),
      '#submit' => ['::addSlotCallback'],
      '#ajax' => [
        'callback' => '::slotsAjaxCallback',
        'wrapper' => 'service-times-wrapper',
      ],
      '#attributes' => ['class' => ['csn-profile-add-service-time']],
      '#limit_validation_errors' => [],
    ];

    // -- Section: Abwesenheit.
    $absence_defaults = $this->getAbsenceDefaults();
    $form['absence'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich möchte eine Abwesenheit melden'),
      '#default_value' => $absence_defaults['active'] ? 1 : 0,
      '#attributes' => ['class' => ['form-boolean']],
    ];
    $form['absence_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Beginn'),
      '#default_value' => $absence_defaults['start'],
      '#attributes' => ['class' => ['form-element']],
      '#states' => [
        'visible' => [':input[name="absence"]' => ['checked' => TRUE]],
        'required' => [':input[name="absence"]' => ['checked' => TRUE]],
      ],
    ];
    $form['absence_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Ende'),
      '#default_value' => $absence_defaults['end'],
      '#attributes' => ['class' => ['form-element']],
      '#states' => [
        'visible' => [':input[name="absence"]' => ['checked' => TRUE]],
        'required' => [':input[name="absence"]' => ['checked' => TRUE]],
      ],
    ];

    // -- Section: Rechtliches.
    $form['confirm_data_processing'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich bin mit der Verarbeitung meiner Daten einverstanden.'),
      '#required' => $is_initial,
      '#default_value' => $this->getFieldValue('field_confirm_data_processing', 0),
      '#disabled' => !$is_initial,
      '#attributes' => ['class' => ['form-boolean']],
    ];
    $form['confirm_terms_conditions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich akzeptiere die AGB.'),
      '#required' => $is_initial,
      '#default_value' => $this->getFieldValue('field_confirm_terms_conditions', 0),
      '#disabled' => !$is_initial,
      '#attributes' => ['class' => ['form-boolean']],
    ];
    $form['confirm_volunteer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich akzeptiere die Ehrenamtsvereinbarung.'),
      '#required' => $is_initial,
      '#default_value' => $this->getFieldValue('field_confirm_volunteer', 0),
      '#disabled' => !$is_initial,
      '#attributes' => ['class' => ['form-boolean']],
    ];

    if (!$is_initial) {
      $form['confirm_data_processing']['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
      $form['confirm_terms_conditions']['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
      $form['confirm_volunteer']['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
    }

    // -- Buttons.
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Abbrechen'),
      '#url' => \Drupal\Core\Url::fromRoute('<front>'),
      '#attributes' => ['class' => ['bsi-button', 'bsi-button--secondary']],
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $is_initial ? $this->t('Freigabe des Accounts anfordern') : $this->t('Profil speichern'),
      '#attributes' => ['class' => ['bsi-button', 'bsi-button--control']],
    ];

    $form['#attached']['library'][] = 'bsi_csn/profile_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Validate absence dates.
    if ($form_state->getValue('absence')) {
      if (empty($form_state->getValue('absence_start'))) {
        $form_state->setErrorByName('absence_start', $this->t('Bitte geben Sie ein Startdatum für die Abwesenheit an.'));
      }
      if (empty($form_state->getValue('absence_end'))) {
        $form_state->setErrorByName('absence_end', $this->t('Bitte geben Sie ein Enddatum für die Abwesenheit an.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $storage = \Drupal::entityTypeManager()->getStorage('profile');
    $is_new = $this->profileEntity === NULL;

    if ($is_new) {
      $profile = $storage->create([
        'type' => 'first_responder',
        'uid' => \Drupal::currentUser()->id(),
      ]);
    }
    else {
      $profile = $this->profileEntity;
    }

    // Simple text/phone/email fields.
    $field_map = [
      'academic_title' => 'field_academic_title',
      'given_name' => 'field_given_name',
      'family_name' => 'field_family_name',
      'phone' => 'field_phone',
      'deh_phone' => 'field_deh_phone',
      'deh_mail' => 'field_deh_mail',
    ];

    foreach ($field_map as $form_key => $field_name) {
      if (!$is_new && in_array($field_name, self::LOCK_AFTER_INITIAL)) {
        continue;
      }
      $profile->set($field_name, $form_state->getValue($form_key));
    }

    // Address field.
    $profile->set('field_address', [
      'country_code' => $form_state->getValue('country_code') ?: 'DE',
      'address_line1' => $form_state->getValue('address_line1'),
      'postal_code' => $form_state->getValue('postal_code'),
      'locality' => $form_state->getValue('locality'),
    ]);

    // Languages.
    $lang_values = array_filter($form_state->getValue('deh_language') ?? []);
    $profile->set('field_deh_language', array_map(fn($tid) => ['target_id' => $tid], array_values($lang_values)));

    // Service times → Office Hours format: [{day, starthours, endhours}, ...].
    $slots = $form_state->getValue('service_times') ?? [];
    $oh_values = [];
    foreach ($slots as $entry) {
      if (!is_array($entry) || empty($entry['day']) && $entry['day'] !== '0') {
        continue;
      }
      $time_from = $entry['time_from'] ?? '';
      $time_to = $entry['time_to'] ?? '';
      if ($time_from === '' && $time_to === '') {
        continue;
      }
      $oh_values[] = [
        'day' => (int) $entry['day'],
        'starthours' => $this->timeToMinutes($time_from),
        'endhours' => $this->timeToMinutes($time_to),
        'comment' => '',
      ];
    }
    // Absence → Office Hours exception entries (day = Unix date as Ymd int).
    if ($form_state->getValue('absence')) {
      $start = $form_state->getValue('absence_start');
      $end = $form_state->getValue('absence_end');
      if ($start && $end) {
        $current = new \DateTime($start);
        $end_date = new \DateTime($end);
        while ($current <= $end_date) {
          $oh_values[] = [
            'day' => (int) $current->format('Ymd'),
            'starthours' => 0,
            'endhours' => 0,
            'comment' => '',
          ];
          $current->modify('+1 day');
        }
      }
    }

    $profile->set('field_availability', $oh_values);

    // Legal checkboxes.
    if ($is_new) {
      $profile->set('field_confirm_data_processing', $form_state->getValue('confirm_data_processing') ? 1 : 0);
      $profile->set('field_confirm_terms_conditions', $form_state->getValue('confirm_terms_conditions') ? 1 : 0);
      $profile->set('field_confirm_volunteer', $form_state->getValue('confirm_volunteer') ? 1 : 0);
    }

    $profile->save();

    $this->messenger()->addStatus($this->t('Ihr Profil wurde gespeichert.'));
    $form_state->setRedirect('bsi_csn.profile');
  }

  /**
   * AJAX: add a service time slot.
   */
  public function addSlotCallback(array &$form, FormStateInterface $form_state): void {
    $form_state->set('num_service_slots', $form_state->get('num_service_slots') + 1);
    $form_state->setRebuild();
  }

  /**
   * AJAX: remove a service time slot.
   */
  public function removeSlotCallback(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $idx = (int) str_replace('remove_slot_', '', $trigger['#name']);
    $values = $form_state->getValue('service_times') ?? [];
    unset($values[$idx]);
    $form_state->setValue('service_times', array_values($values));
    $form_state->set('num_service_slots', max(1, $form_state->get('num_service_slots') - 1));
    $form_state->setRebuild();
  }

  /**
   * AJAX: return the service times wrapper.
   */
  public function slotsAjaxCallback(array &$form, FormStateInterface $form_state): array {
    return $form['service_times'];
  }

  /**
   * Load the current user's first_responder profile.
   */
  private function loadProfile(): mixed {
    $profiles = \Drupal::entityTypeManager()->getStorage('profile')->loadByProperties([
      'type' => 'first_responder',
      'uid' => \Drupal::currentUser()->id(),
    ]);
    return $profiles ? reset($profiles) : NULL;
  }

  /**
   * Build a simple form field with defaults from the profile entity.
   */
  private function buildField(string $field_name, mixed $label, bool $required, array $overrides = []): array {
    return [
      '#type' => $overrides['#type'] ?? 'textfield',
      '#title' => $label,
      '#required' => $required,
      '#default_value' => $this->getFieldValue($field_name),
      '#attributes' => array_merge(
        ['class' => ['form-element']],
        $overrides['#attributes'] ?? [],
      ),
    ] + $overrides;
  }

  /**
   * Get a scalar field value from the profile entity.
   */
  private function getFieldValue(string $field_name, mixed $default = ''): mixed {
    if (!$this->profileEntity || !$this->profileEntity->hasField($field_name)) {
      return $default;
    }
    return $this->profileEntity->get($field_name)->value ?? $default;
  }

  /**
   * Get address subfield values.
   */
  private function getAddressValues(): array {
    $defaults = ['address_line1' => '', 'postal_code' => '', 'locality' => '', 'country_code' => 'DE'];
    if (!$this->profileEntity || !$this->profileEntity->hasField('field_address')) {
      return $defaults;
    }
    $address = $this->profileEntity->get('field_address')->first();
    if (!$address) {
      return $defaults;
    }
    return [
      'address_line1' => $address->address_line1 ?? '',
      'postal_code' => $address->postal_code ?? '',
      'locality' => $address->locality ?? '',
      'country_code' => $address->country_code ?? 'DE',
    ];
  }

  /**
   * Get all language options sorted with Deutsch first.
   */
  private function getLanguageOptions(): array {
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
      'vid' => 'csn_languages',
    ]);
    $deutsch = [];
    $others = [];
    foreach ($terms as $term) {
      if ($term->getName() === 'Deutsch') {
        $deutsch[$term->id()] = $term->getName();
      }
      else {
        $others[$term->id()] = $term->getName();
      }
    }
    asort($others);
    return $deutsch + $others;
  }

  /**
   * Get selected language term IDs.
   */
  private function getSelectedLanguageIds(): array {
    if (!$this->profileEntity || !$this->profileEntity->hasField('field_deh_language')) {
      return [];
    }
    $ids = [];
    foreach ($this->profileEntity->get('field_deh_language') as $item) {
      if ($item->target_id) {
        $ids[] = $item->target_id;
      }
    }
    return $ids;
  }

  /**
   * Count existing Office Hours regular slots (excludes exceptions).
   */
  private function countAvailabilitySlots(): int {
    return count($this->getRegularSlots());
  }

  /**
   * Get regular (non-exception) Office Hours items.
   */
  private function getRegularSlots(): array {
    if (!$this->profileEntity || !$this->profileEntity->hasField('field_availability')) {
      return [];
    }
    $slots = [];
    foreach ($this->profileEntity->get('field_availability') as $item) {
      // Regular days are 0-6 (Sun-Sat). Exceptions have day >= 100000.
      if (($item->day ?? 0) <= 6) {
        $slots[] = $item;
      }
    }
    return $slots;
  }

  /**
   * Get defaults for an availability slot.
   */
  private function getAvailabilitySlotDefaults(int $index): array {
    $defaults = ['day' => '', 'time_from' => '', 'time_to' => ''];
    $slots = $this->getRegularSlots();
    if (isset($slots[$index])) {
      $item = $slots[$index];
      $defaults['day'] = $item->day ?? '';
      $defaults['time_from'] = $this->minutesToTime($item->starthours ?? 0);
      $defaults['time_to'] = $this->minutesToTime($item->endhours ?? 0);
    }
    return $defaults;
  }

  /**
   * Get absence date range from Office Hours exception entries.
   */
  private function getAbsenceDefaults(): array {
    $defaults = ['active' => FALSE, 'start' => '', 'end' => ''];
    if (!$this->profileEntity || !$this->profileEntity->hasField('field_availability')) {
      return $defaults;
    }
    $exception_dates = [];
    foreach ($this->profileEntity->get('field_availability') as $item) {
      if (($item->day ?? 0) > 6) {
        $exception_dates[] = (int) $item->day;
      }
    }
    if (!empty($exception_dates)) {
      sort($exception_dates);
      $defaults['active'] = TRUE;
      $first = (string) reset($exception_dates);
      $last = (string) end($exception_dates);
      $defaults['start'] = substr($first, 0, 4) . '-' . substr($first, 4, 2) . '-' . substr($first, 6, 2);
      $defaults['end'] = substr($last, 0, 4) . '-' . substr($last, 4, 2) . '-' . substr($last, 6, 2);
    }
    return $defaults;
  }

  /**
   * Convert HH:MM string to minutes-since-midnight (Office Hours format).
   */
  private function timeToMinutes(string $time): int {
    if (!preg_match('/^(\d{2}):(\d{2})$/', $time, $m)) {
      return 0;
    }
    return ((int) $m[1] * 100) + (int) $m[2];
  }

  /**
   * Convert Office Hours integer (HHMM) to HH:MM string.
   */
  private function minutesToTime(int $value): string {
    $hours = intdiv($value, 100);
    $minutes = $value % 100;
    return sprintf('%02d:%02d', $hours, $minutes);
  }

}
