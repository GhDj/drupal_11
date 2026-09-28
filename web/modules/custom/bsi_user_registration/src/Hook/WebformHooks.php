<?php

namespace Drupal\bsi_user_registration\Hook;

use Drupal\bsi_user_registration\WebformHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Webform hooks for the BSI User Registration module.
 */
final class WebformHooks {

  use StringTranslationTrait;

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(
    array &$form,
    FormStateInterface $form_state,
    string $form_id,
  ): void {

    if ($form_id !== 'webform_submission_user_registration_edit_form') {
      return;
    }

    $submission = $form_state->getFormObject()->getEntity();
    $data = $submission->getData();

    $form['user_creation'] = [
      '#type' => 'select',
      '#title' => $this->t('User creation'),
      '#options' => [
        'approve' => $this->t('Approve'),
        'reject' => $this->t('Reject'),
      ],
      '#default_value' => $data['user_creation'] ?? NULL,
      '#empty_option' => $this->t('- Select a decision -'),
      '#disabled' => ($data['user_creation'] ?? NULL) === 'approve',
    ];

    $form['#entity_builders'][] = [WebformHelper::class, 'buildSubmissionData'];

    $form['actions']['submit']['#submit'][] = [
      WebformHelper::class,
      'processDecision',
    ];
  }

}
