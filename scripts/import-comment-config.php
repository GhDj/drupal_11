<?php

/**
 * @file
 * Imports CSN comment configuration into the active site.
 *
 * Usage: ddev drush php:script scripts/import-comment-config.php
 */

use Drupal\Core\Config\FileStorage;

$config_path = DRUPAL_ROOT . '/../config/sync';
$file_storage = new FileStorage($config_path);
$config_factory = \Drupal::configFactory();
$module_installer = \Drupal::service('module_installer');

// 1. Enable the comment module if not already enabled.
$module_handler = \Drupal::moduleHandler();
if (!$module_handler->moduleExists('comment')) {
  echo "Enabling comment module...\n";
  $module_installer->install(['comment']);
  echo "Comment module enabled.\n";
}

// 2. Import config in dependency order.
$configs = [
  // Comment type.
  'comment.type.csn_comment',
  // Field storage for comment body.
  'field.storage.comment.field_comment_body',
  // Field instance for comment body.
  'field.field.comment.csn_comment.field_comment_body',
  // Field storage for node comments field.
  'field.storage.node.field_comments',
  // Field instance on incident_report.
  'field.field.node.incident_report.field_comments',
  // Comment form display.
  'core.entity_form_display.comment.csn_comment.default',
  // Comment view display.
  'core.entity_view_display.comment.csn_comment.default',
  // Updated node view displays.
  'core.entity_view_display.node.incident_report.full',
  'core.entity_view_display.node.incident_report.default',
  'core.entity_view_display.node.incident_report.explorer',
  'core.entity_view_display.node.incident_report.opener',
  'core.entity_view_display.node.incident_report.teaser',
  'core.entity_view_display.node.incident_report.teaser_highlight',
  'core.entity_view_display.node.incident_report.teaser_triplet',
  'core.entity_view_display.node.incident_report.search_result',
  // Updated node form display.
  'core.entity_form_display.node.incident_report.default',
];

// Entity type map: configs that must be imported via entity storage.
$entity_type_map = [
  'comment.type' => 'comment_type',
  'field.storage' => 'field_storage_config',
  'field.field' => 'field_config',
];

foreach ($configs as $config_name) {
  $data = $file_storage->read($config_name);
  if (!$data) {
    echo "WARNING: Config not found in sync dir: $config_name\n";
    continue;
  }

  // Determine if this is a config entity that needs entity storage.
  $use_entity_storage = FALSE;
  foreach ($entity_type_map as $prefix => $entity_type_id) {
    if (str_starts_with($config_name, $prefix . '.')) {
      $use_entity_storage = TRUE;
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type_id);
      $existing = $storage->load($data['id']);
      if ($existing) {
        echo "Updating (entity): $config_name\n";
        foreach ($data as $key => $value) {
          if ($key === 'uuid') {
            continue;
          }
          $existing->set($key, $value);
        }
        $existing->save();
      }
      else {
        echo "Creating (entity): $config_name\n";
        $entity = $storage->create($data);
        $entity->save();
      }
      break;
    }
  }

  if (!$use_entity_storage) {
    // Simple config or display config — use config factory.
    echo "Importing (config): $config_name\n";
    $config = $config_factory->getEditable($config_name);
    $config->setData($data);
    $config->save();
  }
}

echo "\nDone. Comment configuration imported successfully.\n";
echo "Clear caches: ddev drush cr\n";
