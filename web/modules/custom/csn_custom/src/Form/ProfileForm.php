<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\csn_custom\FeedbackServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity form for the CSN first responder profile.
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
    private readonly FeedbackServiceInterface $feedback,
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('csn_custom.feedback'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id): EntityInterface {
    $storage = $this->entityTypeManager->getStorage('profile');
    $profiles = $storage->loadByProperties([
      'type' => 'first_responder',
      'uid' => $this->currentUser()->id(),
    ]);

    if ($profiles) {
      return reset($profiles);
    }

    return $storage->create([
      'type' => 'first_responder',
      'uid' => $this->currentUser()->id(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    $is_initial = $this->entity->isNew();

    // Absence fields (custom, not entity fields — derived from OH exceptions).
    $absence_defaults = $this->getAbsenceDefaults();
    $form['absence'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('I would like to report an absence'),
      '#default_value' => $absence_defaults['active'] ? 1 : 0,
      '#weight' => 50,
    ];
    $form['absence_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Start'),
      '#default_value' => $absence_defaults['start'],
      '#states' => [
        'visible' => [':input[name="absence"]' => ['checked' => TRUE]],
        'required' => [':input[name="absence"]' => ['checked' => TRUE]],
      ],
      '#weight' => 51,
    ];
    $form['absence_end'] = [
      '#type' => 'date',
      '#title' => $this->t('End'),
      '#default_value' => $absence_defaults['end'],
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

    // Legal checkboxes: only required on initial submission.
    foreach (['field_confirm_data_processing', 'field_confirm_terms_conditions', 'field_confirm_volunteer'] as $field_name) {
      $this->setCheckboxRequired($form, $field_name, $is_initial);
    }

    // Custom submit button labels.
    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = $is_initial
        ? $this->t('Request account activation')
        : $this->t('Save profile');
    }

    // Cancel link.
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('<front>'),
      '#weight' => -10,
    ];

    // Hide the advanced/meta sidebar that ContentEntityForm may add.
    if (isset($form['advanced'])) {
      $form['advanced']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    if ($form_state->getValue('absence')) {
      if (empty($form_state->getValue('absence_start'))) {
        $form_state->setErrorByName('absence_start', $this->t('Please enter a start date for the absence.'));
      }
      if (empty($form_state->getValue('absence_end'))) {
        $form_state->setErrorByName('absence_end', $this->t('Please enter an end date for the absence.'));
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
        $this->t('Thank you!'),
        [
          $this->t('Your profile has been created successfully.'),
          $this->t('You will be notified once your account has been activated.'),
        ],
      ));
    }
    else {
      $this->messenger()->addStatus($this->t('Your profile has been saved.'));
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
      $form[$field_name]['#disabled'] = TRUE;
    }
  }

  /**
   * Set required state on a boolean_checkbox widget.
   */
  private function setCheckboxRequired(array &$form, string $field_name, bool $required): void {
    if (!isset($form[$field_name]['widget'])) {
      return;
    }
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
