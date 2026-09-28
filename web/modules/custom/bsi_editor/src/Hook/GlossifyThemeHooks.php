<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks related to the Glossify module.
 */
class GlossifyThemeHooks {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_theme_registry_alter().
   */
  #[Hook('theme_registry_alter')]
  public function alterThemeRegistry(array &$theme_registry): void {
    if (isset($theme_registry['glossify_tooltip'])) {
      $theme_registry['glossify_tooltip']['variables']['term'] = NULL;
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for glossify_tooltip.
   */
  #[Hook('theme_suggestions_glossify_tooltip_alter')]
  public function alterThemeSuggestions(array &$suggestions, array &$variables, string $hook): void {
    if (!isset($variables['term']?->id)) {
      return;
    }

    $term = $this->entityTypeManager->getStorage('taxonomy_term')
      ->load($variables['term']->id);
    $suggestions[] = $hook . '__' . $term->bundle();
  }

  /**
   * Implements hook_preprocess_HOOK() for glossify_tooltip.
   */
  #[Hook('preprocess_glossify_tooltip')]
  public function preprocessGlossifyTooltip(array &$variables): void {
    if (!isset($variables['term']?->id)) {
      return;
    }

    $term = $this->entityTypeManager->getStorage('taxonomy_term')
      ->load($variables['term']->id);
    $variables['entity'] = $term;
  }

}
