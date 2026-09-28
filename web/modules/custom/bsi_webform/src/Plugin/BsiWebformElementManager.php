<?php

declare(strict_types=1);

namespace Drupal\bsi_webform\Plugin;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\webform\Plugin\WebformElementManager;

/**
 * Custom Webform element manager.
 *
 * Filters the list of available Webform element types displayed in the
 * Webform UI based on the current user's permissions and the configured
 * allowed element types.
 */
final class BsiWebformElementManager extends WebformElementManager {

  /**
   * The current user.
   */
  protected AccountProxyInterface $currentUser;

  /**
   * Sets the current user.
   */
  public function setCurrentUser(AccountProxyInterface $currentUser): void {
    $this->currentUser = $currentUser;
  }

  /**
   * Filters the available elements shown in the Webform "Add element" UI.
   */
  public function getGroupedDefinitions(?array $definitions = NULL, $label_key = 'label'): array {
    $groupedDefinitions = parent::getGroupedDefinitions($definitions);

    if ($this->currentUser->hasPermission('bypass webform element type restriction')) {
      return $groupedDefinitions;
    }

    $allowedTypes = $this->configFactory
      ->get('bsi_webform.settings')
      ->get('element_types') ?? [];

    foreach ($groupedDefinitions as $group => &$elements) {

      foreach ($elements as $pluginId => $definition) {
        if (!in_array($pluginId, $allowedTypes, TRUE)) {
          unset($elements[$pluginId]);
        }
      }

      if (empty($elements)) {
        unset($groupedDefinitions[$group]);
      }
    }

    return $groupedDefinitions;
  }

}
