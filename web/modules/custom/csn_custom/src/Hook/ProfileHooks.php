<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\csn_custom\Form\ProfileForm;

/**
 * Hooks related to the CSN Ersthelfer:in profile.
 */
class ProfileHooks {

  /**
   * Implements hook_entity_type_build().
   *
   * Registers the 'csn_profile' form mode on the profile entity type.
   */
  #[Hook('entity_type_build')]
  public function buildEntityType(array &$entity_types): void {
    if (isset($entity_types['profile'])) {
      $entity_types['profile']->setFormClass(
        'csn_profile',
        ProfileForm::class,
      );
    }
  }

}
