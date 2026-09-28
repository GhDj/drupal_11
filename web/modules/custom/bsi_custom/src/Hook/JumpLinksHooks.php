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
   * Implements hook_preprocess_HOOK() for field--paragraph--field-section-title.html.twig.
   */
  #[Hook('preprocess_field__paragraph__field_section_title')]
  public function preprocessTitle(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $entity */
    $entity = $variables['element']['#object'];
    if (!$entity->hasField('field_jumplink') || $entity->get('field_jumplink')->isEmpty()) {
      return;
    }

    $variables['attributes']['id'] = 'anchor-' . Html::getId($entity->get('field_jumplink')->value);
  }

}
