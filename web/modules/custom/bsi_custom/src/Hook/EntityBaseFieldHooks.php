<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks related to entity base fields.
 */
class EntityBaseFieldHooks {

  /**
   * Implements hook_entity_type_build().
   *
   * @see https://www.drupal.org/node/2925634
   */
  #[Hook('entity_type_build')]
  public function skipNodeBaseFieldPreprocessing(array &$entity_types): void {
    $entity_types['node']->set('enable_base_field_custom_preprocess_skipping', TRUE);
    $entity_types['taxonomy_term']->set('enable_base_field_custom_preprocess_skipping', TRUE);
  }

  /**
   * Implements hook_entity_base_field_info_alter().
   */
  #[Hook('entity_base_field_info_alter')]
  public function allowLabelPlacement(array &$fields, EntityTypeInterface $entity_type): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $labelKey = $entity_type->getKey('label');
    if ($labelKey !== FALSE && !$fields[$labelKey]->isDisplayConfigurable('view')) {
      $fields[$labelKey]
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayOptions('view', [
          'region' => 'hidden',
        ]);
    }
  }

  /**
   * Implements hook_entity_base_field_info_alter().
   */
  #[Hook('entity_base_field_info_alter')]
  public function allowMetadataPlacement(array &$fields, EntityTypeInterface $entity_type): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    foreach (['created', 'changed', 'updated', 'uid'] as $fieldName) {
      if (!isset($fields[$fieldName])) {
        continue;
      }

      $fields[$fieldName]
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE)
        ->setDisplayOptions('view', [
          'region' => 'hidden',
        ]);
    }
  }

}
