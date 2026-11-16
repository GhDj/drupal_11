<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Frontend profile form for CSN Ersthelfer.
 */
final class ProfileForm extends FormBase {

  /**
   * Fields that become read-only after profile approval.
   */
  private const LOCK_AFTER_APPROVAL = [
    'field_public_title',
    'field_public_first_name',
    'field_public_last_name',
    'field_public_email',
    'field_legal_consent',
  ];

  /**
   * The profile node being edited (or NULL for new).
   */
  private ?NodeInterface $profileNode = NULL;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly AccountProxyInterface $currentUser,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_user'),
    );
  }

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
    $this->profileNode = $this->loadProfileNode();
    $is_approved = $this->isApproved();

    $account = $this->entityTypeManager->getStorage('user')->load($this->currentUser->id());
    $email = $account?->getEmail() ?? '';
    $username = $account?->getAccountName() ?? '';

    // Pass variables to the Twig template.
    $form['#theme'] = 'csn_profile_form';
    $form['#is_approved'] = $is_approved;
    $form['#user_email'] = Html::escape($email ?: $username);

    // -- 01. Initial banner (only for unapproved profiles).
    // Handled in template via #is_approved.

    // -- Section: Daten zum Login.
    // Read-only, rendered in template.

    // -- Section: Kontaktdaten für CSN Team.
    $form['csn_team_phone'] = $this->buildTextField('field_csn_team_phone', $this->t('Telefonnummer'), TRUE, [
      '#type' => 'tel',
      '#attributes' => ['placeholder' => '+49 ...'],
    ]);
    $form['csn_team_email'] = $this->buildTextField('field_csn_team_email', $this->t('E-Mail-Adresse'), TRUE, [
      '#type' => 'email',
    ]);
    $form['csn_team_street'] = $this->buildTextField('field_csn_team_street', $this->t('Straße'), TRUE);
    $form['csn_team_house_number'] = $this->buildTextField('field_csn_team_house_number', $this->t('Hausnummer'), TRUE);
    $form['csn_team_zip'] = $this->buildTextField('field_csn_team_zip', $this->t('PLZ'), TRUE);
    $form['csn_team_city'] = $this->buildTextField('field_csn_team_city', $this->t('Ort'), TRUE);
    $form['csn_team_country'] = $this->buildTextField('field_csn_team_country', $this->t('Land'), TRUE);

    // -- Section: Kontaktdaten für Hilfesuchende.
    $form['public_title'] = $this->buildTextField('field_public_title', $this->t('Titel'), FALSE);
    $form['public_first_name'] = $this->buildTextField('field_public_first_name', $this->t('Vorname'), TRUE);
    $form['public_last_name'] = $this->buildTextField('field_public_last_name', $this->t('Nachname'), TRUE);
    $form['public_phone'] = $this->buildTextField('field_public_phone', $this->t('Telefonnummer'), FALSE, [
      '#type' => 'tel',
      '#attributes' => ['placeholder' => '+49 ...'],
    ]);
    $form['public_email'] = $this->buildTextField('field_public_email', $this->t('E-Mail-Adresse'), TRUE, [
      '#type' => 'email',
    ]);

    // Sprache — multi-select with chip widget.
    $language_options = $this->getLanguageOptions();
    $selected_languages = $this->getSelectedLanguageIds();
    $form['public_languages'] = [
      '#type' => 'select',
      '#title' => $this->t('Sprache'),
      '#options' => $language_options,
      '#multiple' => TRUE,
      '#required' => TRUE,
      '#default_value' => $selected_languages,
      '#attributes' => [
        'data-csn-language-select' => TRUE,
        'data-placeholder' => $this->t('Bitte auswählen'),
        'class' => ['form-element'],
      ],
    ];

    // Lock fields after approval.
    if ($is_approved) {
      foreach (self::LOCK_AFTER_APPROVAL as $field_name) {
        $form_key = str_replace('field_', '', $field_name);
        if (isset($form[$form_key])) {
          $form[$form_key]['#disabled'] = TRUE;
          $form[$form_key]['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
        }
      }
    }

    // -- Section: Servicezeiten.
    $num_service_times = $form_state->get('num_service_times');
    if ($num_service_times === NULL) {
      $num_service_times = max(1, $this->countServiceTimes());
      $form_state->set('num_service_times', $num_service_times);
    }

    $form['service_times'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['csn-profile-service-times']],
    ];

    $weekdays = [
      '' => $this->t('- Bitte wählen -'),
      'monday' => $this->t('Montag'),
      'tuesday' => $this->t('Dienstag'),
      'wednesday' => $this->t('Mittwoch'),
      'thursday' => $this->t('Donnerstag'),
      'friday' => $this->t('Freitag'),
      'saturday' => $this->t('Samstag'),
      'sunday' => $this->t('Sonntag'),
    ];

    for ($i = 0; $i < $num_service_times; $i++) {
      $defaults = $this->getServiceTimeDefaults($i);
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
        '#type' => 'textfield',
        '#title' => $this->t('Von'),
        '#required' => TRUE,
        '#size' => 5,
        '#maxlength' => 5,
        '#default_value' => $defaults['time_from'],
        '#attributes' => ['placeholder' => '08:00', 'class' => ['form-element']],
      ];
      $form['service_times'][$i]['time_to'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Bis'),
        '#required' => TRUE,
        '#size' => 5,
        '#maxlength' => 5,
        '#default_value' => $defaults['time_to'],
        '#attributes' => ['placeholder' => '17:00', 'class' => ['form-element']],
      ];
      // Remove button (not for first row).
      if ($i > 0) {
        $form['service_times'][$i]['remove'] = [
          '#type' => 'submit',
          '#value' => $this->t('Löschen'),
          '#name' => 'remove_service_time_' . $i,
          '#submit' => ['::removeServiceTimeCallback'],
          '#ajax' => [
            'callback' => '::serviceTimesAjaxCallback',
            'wrapper' => 'service-times-wrapper',
          ],
          '#attributes' => ['class' => ['csn-profile-service-time-remove']],
          '#limit_validation_errors' => [],
        ];
      }
    }

    $form['service_times']['#prefix'] = '<div id="service-times-wrapper">';
    $form['service_times']['#suffix'] = '</div>';

    $form['add_service_time'] = [
      '#type' => 'submit',
      '#value' => $this->t('Servicezeit hinzufügen'),
      '#submit' => ['::addServiceTimeCallback'],
      '#ajax' => [
        'callback' => '::serviceTimesAjaxCallback',
        'wrapper' => 'service-times-wrapper',
      ],
      '#attributes' => ['class' => ['csn-profile-add-service-time']],
      '#limit_validation_errors' => [],
    ];

    // -- Section: Abwesenheit.
    $form['absence'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich möchte eine Abwesenheit melden'),
      '#default_value' => $this->getFieldValue('field_absence', 0),
      '#attributes' => ['class' => ['form-boolean']],
    ];
    $form['absence_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Beginn'),
      '#default_value' => $this->getFieldValue('field_absence_start'),
      '#attributes' => ['class' => ['form-element']],
      '#states' => [
        'visible' => [':input[name="absence"]' => ['checked' => TRUE]],
        'required' => [':input[name="absence"]' => ['checked' => TRUE]],
      ],
    ];
    $form['absence_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Ende'),
      '#default_value' => $this->getFieldValue('field_absence_end'),
      '#attributes' => ['class' => ['form-element']],
      '#states' => [
        'visible' => [':input[name="absence"]' => ['checked' => TRUE]],
        'required' => [':input[name="absence"]' => ['checked' => TRUE]],
      ],
    ];

    // -- Section: Einverständnis.
    $form['legal_consent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich bin damit einverstanden, dass meine Kontaktdaten für Hilfesuchende auf der CSN Webseite veröffentlicht werden.'),
      '#required' => !$is_approved,
      '#default_value' => $this->getFieldValue('field_legal_consent', 0),
      '#disabled' => $is_approved,
      '#attributes' => ['class' => ['form-boolean']],
    ];
    if ($is_approved) {
      $form['legal_consent']['#wrapper_attributes']['class'][] = 'csn-profile-field--locked';
    }

    // -- Buttons.
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Abbrechen'),
      '#url' => \Drupal\Core\Url::fromRoute('<front>'),
      '#attributes' => ['class' => ['bsi-button', 'bsi-button--secondary']],
    ];

    if (!$is_approved) {
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Freigabe des Accounts anfordern'),
        '#attributes' => ['class' => ['bsi-button', 'bsi-button--control']],
      ];
    }
    else {
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Profil speichern'),
        '#attributes' => ['class' => ['bsi-button', 'bsi-button--control']],
      ];
    }

    $form['#attached']['library'][] = 'bsi_csn/profile_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // Validate time format HH:MM.
    $service_times = $form_state->getValue('service_times') ?? [];
    foreach ($service_times as $i => $entry) {
      if (!is_array($entry)) {
        continue;
      }
      foreach (['time_from', 'time_to'] as $field) {
        $val = $entry[$field] ?? '';
        if ($val && !preg_match('/^\d{2}:\d{2}$/', $val)) {
          $form_state->setErrorByName("service_times][$i][$field", $this->t('Bitte geben Sie die Uhrzeit im Format HH:MM ein.'));
        }
      }
    }

    // Validate absence dates if checkbox is checked.
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
    $node_storage = $this->entityTypeManager->getStorage('node');
    $paragraph_storage = $this->entityTypeManager->getStorage('paragraph');
    $is_new = $this->profileNode === NULL;

    if ($is_new) {
      $node = $node_storage->create([
        'type' => 'csn_profile',
        'uid' => $this->currentUser->id(),
      ]);
    }
    else {
      $node = $this->profileNode;
    }

    // Map form values to node fields.
    $field_map = [
      'csn_team_phone' => 'field_csn_team_phone',
      'csn_team_email' => 'field_csn_team_email',
      'csn_team_street' => 'field_csn_team_street',
      'csn_team_house_number' => 'field_csn_team_house_number',
      'csn_team_zip' => 'field_csn_team_zip',
      'csn_team_city' => 'field_csn_team_city',
      'csn_team_country' => 'field_csn_team_country',
      'public_title' => 'field_public_title',
      'public_first_name' => 'field_public_first_name',
      'public_last_name' => 'field_public_last_name',
      'public_phone' => 'field_public_phone',
      'public_email' => 'field_public_email',
    ];

    foreach ($field_map as $form_key => $field_name) {
      // Skip locked fields on approved profiles.
      if ($this->isApproved() && in_array($field_name, self::LOCK_AFTER_APPROVAL)) {
        continue;
      }
      $node->set($field_name, $form_state->getValue($form_key));
    }

    // Languages (select multiple returns array of term IDs).
    if (!$this->isApproved() || !in_array('field_public_languages', self::LOCK_AFTER_APPROVAL)) {
      $lang_values = array_filter($form_state->getValue('public_languages') ?? []);
      $lang_refs = array_map(fn($tid) => ['target_id' => $tid], array_values($lang_values));
      $node->set('field_public_languages', $lang_refs);
    }

    // Absence.
    $node->set('field_absence', $form_state->getValue('absence') ? 1 : 0);
    $node->set('field_absence_start', $form_state->getValue('absence_start') ?: NULL);
    $node->set('field_absence_end', $form_state->getValue('absence_end') ?: NULL);

    // Legal consent.
    if (!$this->isApproved()) {
      $node->set('field_legal_consent', $form_state->getValue('legal_consent') ? 1 : 0);
    }

    // Auto-generate title.
    $first = $form_state->getValue('public_first_name') ?? '';
    $last = $form_state->getValue('public_last_name') ?? '';
    $node->setTitle(trim($first . ' ' . $last) ?: 'CSN Profile');

    // Service times — rebuild paragraphs.
    $service_times_data = $form_state->getValue('service_times') ?? [];
    $new_paragraphs = [];
    foreach ($service_times_data as $entry) {
      if (!is_array($entry) || empty($entry['day'])) {
        continue;
      }
      $paragraph = $paragraph_storage->create([
        'type' => 'service_time',
        'field_day' => $entry['day'],
        'field_time_from' => $entry['time_from'],
        'field_time_to' => $entry['time_to'],
      ]);
      $paragraph->save();
      $new_paragraphs[] = ['target_id' => $paragraph->id(), 'target_revision_id' => $paragraph->getRevisionId()];
    }
    $node->set('field_service_times', $new_paragraphs);

    $node->save();

    $this->messenger()->addStatus($this->t('Ihr Profil wurde gespeichert.'));
    $form_state->setRedirect('bsi_csn.profile');
  }

  /**
   * AJAX callback: add a service time row.
   */
  public function addServiceTimeCallback(array &$form, FormStateInterface $form_state): void {
    $count = $form_state->get('num_service_times');
    $form_state->set('num_service_times', $count + 1);
    $form_state->setRebuild();
  }

  /**
   * AJAX callback: remove a service time row.
   */
  public function removeServiceTimeCallback(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $idx = (int) str_replace('remove_service_time_', '', $trigger['#name']);
    $values = $form_state->getValue('service_times') ?? [];
    unset($values[$idx]);
    $form_state->setValue('service_times', array_values($values));
    $count = $form_state->get('num_service_times');
    $form_state->set('num_service_times', max(1, $count - 1));
    $form_state->setRebuild();
  }

  /**
   * AJAX callback: return the service_times wrapper.
   */
  public function serviceTimesAjaxCallback(array &$form, FormStateInterface $form_state): array {
    return $form['service_times'];
  }

  /**
   * Load the current user's profile node.
   */
  private function loadProfileNode(): ?NodeInterface {
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'csn_profile')
      ->condition('uid', $this->currentUser->id())
      ->range(0, 1)
      ->execute();

    if (!empty($nids)) {
      return $this->entityTypeManager->getStorage('node')->load(reset($nids));
    }
    return NULL;
  }

  /**
   * Check if profile is approved.
   */
  private function isApproved(): bool {
    return $this->profileNode && (bool) $this->profileNode->get('field_profile_approved')->value;
  }

  /**
   * Build a text input field with defaults from the node.
   */
  private function buildTextField(string $field_name, mixed $label, bool $required, array $overrides = []): array {
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
   * Get a field value from the profile node.
   */
  private function getFieldValue(string $field_name, mixed $default = ''): mixed {
    if (!$this->profileNode || !$this->profileNode->hasField($field_name)) {
      return $default;
    }
    return $this->profileNode->get($field_name)->value ?? $default;
  }

  /**
   * Get all language options for the select widget.
   *
   * Returns term IDs keyed by tid, with Deutsch first.
   */
  private function getLanguageOptions(): array {
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
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
   * Get selected language term IDs from the profile node.
   */
  private function getSelectedLanguageIds(): array {
    if (!$this->profileNode || !$this->profileNode->hasField('field_public_languages')) {
      return [];
    }
    $ids = [];
    foreach ($this->profileNode->get('field_public_languages') as $item) {
      if ($item->target_id) {
        $ids[] = $item->target_id;
      }
    }
    return $ids;
  }

  /**
   * Count existing service time paragraphs.
   */
  private function countServiceTimes(): int {
    if (!$this->profileNode || !$this->profileNode->hasField('field_service_times')) {
      return 0;
    }
    return $this->profileNode->get('field_service_times')->count();
  }

  /**
   * Get defaults for a service time row.
   */
  private function getServiceTimeDefaults(int $index): array {
    $defaults = ['day' => '', 'time_from' => '', 'time_to' => ''];
    if (!$this->profileNode || !$this->profileNode->hasField('field_service_times')) {
      return $defaults;
    }
    $items = $this->profileNode->get('field_service_times');
    if (isset($items[$index])) {
      $paragraph = $items[$index]->entity;
      if ($paragraph) {
        $defaults['day'] = $paragraph->get('field_day')->value ?? '';
        $defaults['time_from'] = $paragraph->get('field_time_from')->value ?? '';
        $defaults['time_to'] = $paragraph->get('field_time_to')->value ?? '';
      }
    }
    return $defaults;
  }

}
