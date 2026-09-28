<?php

namespace Drupal\bsi_user_registration;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Webform helper methods for the BSI User Registration module.
 */
class WebformHelper {

  /**
   * Saves the user_creation field value.
   */
  public static function buildSubmissionData(string $entity_type_id, WebformSubmissionInterface $submission, array &$form, FormStateInterface $form_state): void {

    $decision = $form_state->getValue('user_creation');

    if (!$decision) {
      return;
    }

    $data = $submission->getData();
    $data['user_creation'] = $decision;
    $submission->setData($data);
  }

  /**
   * User creation logic depending on decision.
   */
  public static function processDecision(array &$form, FormStateInterface $form_state): void {

    $submission = $form_state->getFormObject()->getEntity();
    $data = $submission->getData();

    $decision = $form_state->getValue('user_creation');

    if ($decision === 'reject') {
      $submission->delete();
      return;
    }

    if ($decision !== 'approve') {
      return;
    }

    $email = $data['email'] ?? NULL;
    $last_name = $data['last_name'] ?? NULL;
    $first_name = $data['first_name'] ?? NULL;

    if (!$email) {
      return;
    }

    $user_storage = \Drupal::entityTypeManager()->getStorage('user');

    $existing = $user_storage->loadByProperties([
      'mail' => $email,
    ]);

    if ($existing) {
      return;
    }

    $name = (isset($last_name) && isset($first_name)) ? t("@first @last", [
      '@first' => $first_name,
      '@last' => $last_name,
    ]) : $email;

    $user = $user_storage->create([
      'name' => $name,
      'mail' => $email,
      'status' => TRUE,
    ]);

    $user->save();

    // @todo assign group role to the user.
  }

}
