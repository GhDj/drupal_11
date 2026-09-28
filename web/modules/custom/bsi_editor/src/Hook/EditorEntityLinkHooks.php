<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\rabbit_hole\BehaviorSettingsManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Hook implementations for ckeditor5_test.
 *
 * @note This only applies when using "Entity links" text filter.
 */
class EditorEntityLinkHooks {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    #[Autowire(service: 'rabbit_hole.behavior_settings_manager')]
    protected ?BehaviorSettingsManager $rabbitHole,
  ) {}

  /**
   * Implements hook_entity_bundle_info_alter().
   *
   * Allow selecting additional entity types/bundles when linking.
   */
  #[Hook('entity_bundle_info_alter')]
  public function entityBundleInfoAlter(array &$bundles): void {
    $this->enableLinkToCanonical($bundles, 'taxonomy_term');
    $this->enableLinkToCanonical($bundles, 'media');
  }

  /**
   * Activates CKEditor link suggestions for additional entity types/bundles.
   *
   * @see \Drupal\ckeditor5\Plugin\CKEditor5Plugin\EntityLinkSuggestions
   */
  private function enableLinkToCanonical(array &$bundles, string $entityTypeId): void {
    if (!isset($bundles[$entityTypeId])) {
      return;
    }
    $entityType = $this->entityTypeManager->getDefinition($entityTypeId);
    $entity_type_id = $entityType->getBundleEntityType() ?? $entityTypeId;
    foreach (array_keys($bundles[$entityTypeId]) as $bundle) {
      $bundles[$entityTypeId][$bundle]['ckeditor5_link_suggestions'] = $this->hasCanonicalPage($entity_type_id, $bundle);
    }
  }

  /**
   * Helper method to determine if an entity bundle may have a canonical page.
   */
  private function hasCanonicalPage(string $entityTypeId, string $bundle): bool {
    $config = $this->rabbitHole?->loadBehaviorSettingsAsConfig($entityTypeId, $bundle);
    return $config->isNew()
      || $config->get('action') === 'display_page'
      || $config->get('allow_override') === 1;
  }

}
