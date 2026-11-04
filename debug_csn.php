<?php

/**
 * @file
 * Debug script to verify CSN group relationships and block visibility.
 *
 * Usage: drush php:script debug_csn.php
 */

echo "=== CSN Debug ===\n\n";

// 1. Check if bsi_csn module is enabled.
$module_handler = \Drupal::moduleHandler();
echo "1. Module bsi_csn enabled: " . ($module_handler->moduleExists('bsi_csn') ? 'YES' : 'NO') . "\n";

// 2. Find CSN group.
$groups = \Drupal::entityTypeManager()
  ->getStorage('group')
  ->loadByProperties(['type' => 'csn']);

if (empty($groups)) {
  echo "2. CSN group: NOT FOUND\n";
  return;
}

$group = reset($groups);
echo "2. CSN group: {$group->label()} (id: {$group->id()})\n";

// 3. List all nodes in the CSN group.
/** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $rel_storage */
$rel_storage = \Drupal::entityTypeManager()->getStorage('group_relationship');
$relationships = $rel_storage->loadByGroup($group);

echo "3. Group relationships: " . count($relationships) . "\n";
foreach ($relationships as $rel) {
  $entity = $rel->getEntity();
  $type = $rel->getRelationshipType()->id();
  echo "   - [{$type}] {$entity->label()} (entity id: {$entity->id()})\n";
}

// 4. Check block placement.
$blocks = \Drupal::entityTypeManager()
  ->getStorage('block')
  ->loadByProperties(['plugin' => 'bsi_csn_branding']);

echo "4. CSN Branding block placements: " . count($blocks) . "\n";
foreach ($blocks as $block) {
  echo "   - Block: {$block->id()}, region: {$block->getRegion()}, status: " . ($block->status() ? 'enabled' : 'disabled') . "\n";
  $visibility = $block->getVisibility();
  echo "   - Visibility conditions: " . (empty($visibility) ? 'NONE' : implode(', ', array_keys($visibility))) . "\n";
  if (isset($visibility['group_content_visibility'])) {
    $conf = $visibility['group_content_visibility'];
    echo "   - Group types: " . print_r($conf['group_types'] ?? [], TRUE);
    echo "   - Negate: " . ($conf['negate'] ?? 'false') . "\n";
  }
}

// 5. Check CSN settings config.
$config = \Drupal::config('bsi_csn.settings');
echo "5. CSN config:\n";
echo "   - bsi_url: " . ($config->get('bsi_url') ?: '(empty)') . "\n";
echo "   - csn_dashboard_url: " . ($config->get('csn_dashboard_url') ?: '(empty)') . "\n";
echo "   - logout_redirect_url: " . ($config->get('logout_redirect_url') ?: '(empty)') . "\n";

echo "\n=== Done ===\n";
