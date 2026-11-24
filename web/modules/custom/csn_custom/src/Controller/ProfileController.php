<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\profile\Entity\ProfileInterface;

/**
 * Controller for the CSN profile page.
 *
 * Loads or creates the first_responder profile entity, then renders
 * the entity form using the 'csn_profile' form mode.
 */
final class ProfileController extends ControllerBase {

  /**
   * Renders the profile form.
   */
  public function form(): array {
    $profile = $this->loadOrCreateProfile();
    return $this->entityFormBuilder()->getForm($profile, 'csn_profile');
  }

  /**
   * Loads the current user's first_responder profile or creates a new one.
   */
  private function loadOrCreateProfile(): ProfileInterface {
    $storage = $this->entityTypeManager()->getStorage('profile');
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

}
