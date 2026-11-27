<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Hook;

use Drupal\comment\CommentInterface;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;

/**
 * Hooks related to CSN comments on incident reports.
 */
class CommentHooks {

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly ModerationInformationInterface $moderationInformation,
  ) {}

  /**
   * Implements hook_form_FORM_ID_alter() for comment_csn_comment_form.
   */
  #[Hook('form_comment_csn_comment_form_alter')]
  public function alterCommentForm(array &$form, FormStateInterface $formState, string $formId): void {
    // Hide the subject field — not needed for correction comments.
    if (isset($form['subject'])) {
      $form['subject']['#access'] = FALSE;
    }

    // Set submit button label.
    if (isset($form['actions']['submit'])) {
      $form['actions']['submit']['#value'] = t('Kommentar senden');
    }

    // Enforce 1000 character limit on the comment body.
    if (isset($form['field_comment_body']['widget'][0]['value'])) {
      $form['field_comment_body']['widget'][0]['value']['#maxlength'] = 1000;
      $form['field_comment_body']['widget'][0]['value']['#attributes']['maxlength'] = 1000;
    }
    // For text_textarea widget, the key is #value vs nested format.
    if (isset($form['field_comment_body']['widget'][0]['#type']) && $form['field_comment_body']['widget'][0]['#type'] === 'text_format') {
      $form['field_comment_body']['widget'][0]['#attributes']['maxlength'] = 1000;
    }

    // Add server-side validation for character limit.
    $form['#validate'][] = [static::class, 'validateCommentLength'];
  }

  /**
   * Validation callback: enforce 1000 character limit on comment body.
   */
  public static function validateCommentLength(array &$form, FormStateInterface $formState): void {
    $value = $formState->getValue('field_comment_body');
    $text = '';
    if (is_array($value) && isset($value[0]['value'])) {
      $text = $value[0]['value'];
    }
    if (mb_strlen($text) > 1000) {
      $formState->setErrorByName('field_comment_body', t('The comment may not be longer than 1000 characters.'));
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_insert() for comment.
   *
   * When a CSN admin posts a comment on an incident report that is in
   * "review_requested" or "revision_requested" state, transition it to
   * "revision_requested" (= "Bitte um Korrektur" / "In Korrektur").
   * Comments by first responders do not change the status.
   */
  #[Hook('comment_insert')]
  public function commentInsert(EntityInterface $entity): void {
    if (!$entity instanceof CommentInterface) {
      return;
    }
    if ($entity->bundle() !== 'csn_comment') {
      return;
    }

    $node = $entity->getCommentedEntity();
    if (!$node instanceof NodeInterface || $node->bundle() !== 'incident_report') {
      return;
    }

    // Only CSN admins trigger a status transition.
    if (!$this->currentUser->hasRole('csn_admin')) {
      return;
    }

    // Check current moderation state.
    if (!$this->moderationInformation->isModeratedEntity($node)) {
      return;
    }
    $currentState = $node->get('moderation_state')->value;
    if (!in_array($currentState, ['review_requested', 'revision_requested'], TRUE)) {
      return;
    }

    // Transition to revision_requested.
    $node->set('moderation_state', 'revision_requested');
    $node->save();
  }

}
