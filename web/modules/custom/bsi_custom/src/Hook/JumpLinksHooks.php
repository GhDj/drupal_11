<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks related to the field widgets.
 */
class JumpLinksHooks {

  /**
   * Implements hook_preprocess_HOOK() for field--paragraph--field-section-title.html.twig and field--paragraph--field-section-title--key-facts.html.twig.
   *
   * @todo Replace by using the title_attributes of ::processParagraph.
   */
  #[Hook('preprocess_field__paragraph__field_section_title')]
  #[Hook('preprocess_field__paragraph__field_section_title__key_facts')]
  public function preprocessTitle(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $entity */
    $entity = $variables['element']['#object'];
    if (!$entity->hasField('field_jumplink') || $entity->get('field_jumplink')->isEmpty()) {
      return;
    }

    $variables['attributes']['id'] = 'anchor-' . Html::getId($entity->get('field_jumplink')->value);
  }

  /**
   * Implements hook_preprocess_HOOK for paragraph.html.twig.
   *
   * @see \Drupal\bsi_custom\Hook\ParagraphHooks::preprocessParagraph
   */
  public static function processParagraph(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $entity */
    $entity = $variables['elements']['#paragraph'];

    if (!$entity->hasField('field_jumplink') || $entity->get('field_jumplink')->isEmpty()) {
      return;
    }
    $variables['title_attributes']['id'] = 'anchor-' . Html::getId($entity->get('field_jumplink')->value);
  }

}
