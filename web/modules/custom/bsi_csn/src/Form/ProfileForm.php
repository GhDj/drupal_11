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

    // Ensure we have a profile entity for the OH widget (even if unsaved).
    $profile = $this->profileEntity ?? $this->createEmptyProfile();
    $form_state->set('profile_id', $profile->id());

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

    // -- Section: Servicezeiten — use Office Hours module widget.
    $items = $profile->get('field_availability');
    $widget = $this->getAvailabilityWidget($profile);
    $form['field_availability'] = $widget->form($items, $form, $form_state);
    $form['field_availability']['#access'] = TRUE;

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
    $this->profileEntity = $this->loadProfile();
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

    // Service times — extract via Office Hours widget.
    $items = $profile->get('field_availability');
    $widget = $this->getAvailabilityWidget($profile);
    $widget->extractFormValues($items, $form, $form_state);

    // Absence → append Office Hours exception entries.
    if ($form_state->getValue('absence')) {
      $start = $form_state->getValue('absence_start');
      $end = $form_state->getValue('absence_end');
      if ($start && $end) {
        $current = new \DateTime($start);
        $end_date = new \DateTime($end);
        while ($current <= $end_date) {
          $items->appendItem([
            'day' => (int) $current->format('Ymd'),
            'starthours' => 0,
            'endhours' => 0,
            'comment' => '',
          ]);
          $current->modify('+1 day');
        }
      }
    }

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
   * Get the Office Hours widget instance for field_availability.
   */
  private function getAvailabilityWidget(mixed $profile): mixed {
    return \Drupal::service('plugin.manager.field.widget')->getInstance([
      'field_definition' => $profile->getFieldDefinition('field_availability'),
      'form_mode' => 'default',
      'prepare' => TRUE,
      'configuration' => [
        'type' => 'office_hours_list',
        'settings' => [],
      ],
    ]);
  }

  /**
   * Create an empty (unsaved) profile entity for widget rendering.
   */
  private function createEmptyProfile(): mixed {
    return \Drupal::entityTypeManager()->getStorage('profile')->create([
      'type' => 'first_responder',
      'uid' => \Drupal::currentUser()->id(),
    ]);
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

}
