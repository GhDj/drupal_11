<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\csn_custom\FeedbackServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity form for CSN Ersthelfer:in profile (first_responder profile type).
 */
final class ProfileForm extends ContentEntityForm {

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

  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    private readonly AccountProxyInterface $currentUser,
    private readonly FeedbackServiceInterface $feedback,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('current_user'),
      $container->get('csn_custom.feedback'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $is_initial = $this->entity->isNew();

    // Apply custom theme.
    $form['#theme'] = 'csn_profile_form';
    $form['#is_initial'] = $is_initial;
    $form['#user_email'] = Html::escape(
      $this->currentUser->getEmail() ?: $this->currentUser->getAccountName() ?: ''
    );

    // Language field: add attributes for the chip widget JS.
    // options_select widget renders the <select> at widget level.
    if (isset($form['field_deh_language']['widget'])) {
      $form['field_deh_language']['widget']['#attributes']['data-csn-language-select'] = TRUE;
      $form['field_deh_language']['widget']['#attributes']['data-placeholder'] = (string) $this->t('Bitte auswählen');
    }

    // Absence fields (custom, not entity fields — derived from OH exceptions).
    $absence_defaults = $this->getAbsenceDefaults();
    $form['absence'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ich möchte eine Abwesenheit melden'),
      '#default_value' => $absence_defaults['active'] ? 1 : 0,
      '#attributes' => ['class' => ['form-boolean']],
      '#weight' => 50,
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
      '#weight' => 51,
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
      '#weight' => 52,
    ];

    // Lock fields after initial submission.
    if (!$is_initial) {
      $this->lockFields($form);
    }

    // Legal checkboxes: only required on initial.
    foreach (['field_confirm_data_processing', 'field_confirm_terms_conditions', 'field_confirm_volunteer'] as $field_name) {
      $this->setCheckboxRequired($form, $field_name, $is_initial);
    }

    // Custom submit button labels.
    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $is_initial
        ? $this->t('Freigabe des Accounts anfordern')
        : $this->t('Profil speichern');
      $form['actions']['submit']['#attributes']['class'][] = 'bsi-button';
      $form['actions']['submit']['#attributes']['class'][] = 'bsi-button--control';
    }

    // Cancel link.
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Abbrechen'),
      '#url' => Url::fromRoute('<front>'),
      '#attributes' => ['class' => ['bsi-button', 'bsi-button--secondary']],
      '#weight' => -10,
    ];

    // Hide the advanced/meta sidebar that ContentEntityForm may add.
    if (isset($form['advanced'])) {
      $form['advanced']['#access'] = FALSE;
    }

    $form['#attached']['library'][] = 'csn_custom/profile_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

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
  public function save(array $form, FormStateInterface $form_state): int {
    $items = $this->entity->get('field_availability');

    // Remove old OH exception entries (day > 6) before appending new ones.
    $regular_slots = [];
    foreach ($items as $item) {
      if (($item->day ?? 0) <= 6) {
        $regular_slots[] = $item->getValue();
      }
    }
    $items->setValue($regular_slots);

    // Append new absence entries as Office Hours exceptions.
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

    $result = parent::save($form, $form_state);

    if ($result === SAVED_NEW) {
      $form_state->setRedirectUrl($this->feedback->getFeedbackUrl(
        $this->t('Vielen Dank!'),
        [
          $this->t('Ihr Profil wurde erfolgreich erstellt.'),
          $this->t('Sie werden benachrichtigt, sobald Ihr Account freigeschaltet wurde.'),
        ],
      ));
    }
    else {
      $this->messenger()->addStatus($this->t('Ihr Profil wurde gespeichert.'));
      $form_state->setRedirect('csn_custom.profile');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  protected function showRevisionUi(): bool {
    return FALSE;
  }

  /**
   * Disable locked fields by walking into the widget render tree.
   */
  private function lockFields(array &$form): void {
    foreach (self::LOCK_AFTER_INITIAL as $field_name) {
      if (!isset($form[$field_name])) {
        continue;
      }
      // Mark the entire field wrapper as disabled — works for most widgets.
      $form[$field_name]['#disabled'] = TRUE;
      // Also add CSS class for visual feedback.
      $form[$field_name]['#attributes']['class'][] = 'csn-profile-field--locked';
    }
  }

  /**
   * Set required state on a boolean_checkbox widget.
   */
  private function setCheckboxRequired(array &$form, string $field_name, bool $required): void {
    if (!isset($form[$field_name]['widget'])) {
      return;
    }
    // boolean_checkbox: widget → value.
    if (isset($form[$field_name]['widget']['value'])) {
      $form[$field_name]['widget']['value']['#required'] = $required;
    }
  }

  /**
   * Get absence date range from Office Hours exception entries.
   */
  private function getAbsenceDefaults(): array {
    $defaults = ['active' => FALSE, 'start' => '', 'end' => ''];
    if ($this->entity->isNew() || !$this->entity->hasField('field_availability')) {
      return $defaults;
    }
    $exception_dates = [];
    foreach ($this->entity->get('field_availability') as $item) {
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
