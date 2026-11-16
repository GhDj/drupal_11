<?php

/**
 * @file
 * Import CSN Profile config objects individually.
 *
 * Usage: ddev drush php:script scripts/import-profile-config.php
 *
 * This bypasses full config:import which may fail due to missing modules
 * (encrypt, key, real_aes, tfa). It imports only the BSIR-568 configs.
 */

use Drupal\Core\Config\FileStorage;
use Drupal\Core\Config\InstallStorage;

$config_path = \Drupal::service('extension.list.profile')->getPath('standard') ? '' : '';
$config_path = DRUPAL_ROOT . '/../config/sync';

if (!is_dir($config_path)) {
  echo "ERROR: Config sync directory not found at $config_path\n";
  return;
}

$source = new FileStorage($config_path);
$config_storage = \Drupal::service('config.storage');
$entity_type_manager = \Drupal::entityTypeManager();

// Order matters: dependencies must be imported first.
$configs = [
  // 1. Taxonomy vocabulary.
  'taxonomy.vocabulary.csn_languages',

  // 2. Node type.
  'node.type.csn_profile',

  // 3. Paragraph type.
  'paragraphs.paragraphs_type.service_time',

  // 4. Field storages (node).
  'field.storage.node.field_csn_team_phone',
  'field.storage.node.field_csn_team_email',
  'field.storage.node.field_csn_team_street',
  'field.storage.node.field_csn_team_house_number',
  'field.storage.node.field_csn_team_zip',
  'field.storage.node.field_csn_team_city',
  'field.storage.node.field_csn_team_country',
  'field.storage.node.field_public_title',
  'field.storage.node.field_public_first_name',
  'field.storage.node.field_public_last_name',
  'field.storage.node.field_public_phone',
  'field.storage.node.field_public_email',
  'field.storage.node.field_public_languages',
  'field.storage.node.field_service_times',
  'field.storage.node.field_absence',
  'field.storage.node.field_absence_start',
  'field.storage.node.field_absence_end',
  'field.storage.node.field_legal_consent',
  'field.storage.node.field_profile_approved',

  // 5. Field storages (paragraph).
  'field.storage.paragraph.field_day',
  'field.storage.paragraph.field_time_from',
  'field.storage.paragraph.field_time_to',

  // 6. Field instances (node).
  'field.field.node.csn_profile.field_csn_team_phone',
  'field.field.node.csn_profile.field_csn_team_email',
  'field.field.node.csn_profile.field_csn_team_street',
  'field.field.node.csn_profile.field_csn_team_house_number',
  'field.field.node.csn_profile.field_csn_team_zip',
  'field.field.node.csn_profile.field_csn_team_city',
  'field.field.node.csn_profile.field_csn_team_country',
  'field.field.node.csn_profile.field_public_title',
  'field.field.node.csn_profile.field_public_first_name',
  'field.field.node.csn_profile.field_public_last_name',
  'field.field.node.csn_profile.field_public_phone',
  'field.field.node.csn_profile.field_public_email',
  'field.field.node.csn_profile.field_public_languages',
  'field.field.node.csn_profile.field_service_times',
  'field.field.node.csn_profile.field_absence',
  'field.field.node.csn_profile.field_absence_start',
  'field.field.node.csn_profile.field_absence_end',
  'field.field.node.csn_profile.field_legal_consent',
  'field.field.node.csn_profile.field_profile_approved',

  // 7. Field instances (paragraph).
  'field.field.paragraph.service_time.field_day',
  'field.field.paragraph.service_time.field_time_from',
  'field.field.paragraph.service_time.field_time_to',

  // 8. Form displays.
  'core.entity_form_display.node.csn_profile.default',
  'core.entity_form_display.paragraph.service_time.default',

  // 9. View displays.
  'core.entity_view_display.node.csn_profile.default',
  'core.entity_view_display.paragraph.service_time.default',
];

$success = 0;
$skipped = 0;
$errors = 0;

foreach ($configs as $config_name) {
  $data = $source->read($config_name);
  if ($data === FALSE) {
    echo "[MISSING] $config_name — not found in config/sync\n";
    $errors++;
    continue;
  }

  // Determine if this is a config entity or simple config.
  $parts = explode('.', $config_name);
  $entity_type_id = NULL;

  // Map config prefixes to entity types.
  $entity_type_map = [
    'node.type' => 'node_type',
    'taxonomy.vocabulary' => 'taxonomy_vocabulary',
    'paragraphs.paragraphs_type' => 'paragraphs_type',
    'field.storage' => 'field_storage_config',
    'field.field' => 'field_config',
    'core.entity_form_display' => 'entity_form_display',
    'core.entity_view_display' => 'entity_view_display',
  ];

  foreach ($entity_type_map as $prefix => $type_id) {
    if (str_starts_with($config_name, $prefix)) {
      $entity_type_id = $type_id;
      break;
    }
  }

  try {
    if ($entity_type_id) {
      $entity_type = $entity_type_manager->getDefinition($entity_type_id);
      $storage = $entity_type_manager->getStorage($entity_type_id);
      $id_key = $entity_type->getKey('id');

      // Check if entity already exists.
      $id = $data[$id_key] ?? $data['id'] ?? NULL;
      if ($id && $storage->load($id)) {
        echo "[EXISTS] $config_name — updating\n";
        $entity = $storage->load($id);
        foreach ($data as $key => $value) {
          if ($key !== 'uuid' && $key !== '_core') {
            $entity->set($key, $value);
          }
        }
        $entity->save();
      }
      else {
        // Remove uuid so Drupal generates a new one to avoid collisions.
        unset($data['uuid']);
        $entity = $storage->createFromStorageRecord($data);
        $entity->save();
        echo "[CREATED] $config_name\n";
      }
    }
    else {
      // Simple config object.
      $config = \Drupal::configFactory()->getEditable($config_name);
      $config->setData($data)->save();
      echo "[SAVED] $config_name\n";
    }
    $success++;
  }
  catch (\Exception $e) {
    echo "[ERROR] $config_name — " . $e->getMessage() . "\n";
    $errors++;
  }
}

echo "\n=== Done: $success imported, $skipped skipped, $errors errors ===\n";

// Seed language terms.
echo "\n==> Seeding CSN language terms...\n";
if (function_exists('_bsi_csn_seed_languages')) {
  _bsi_csn_seed_languages();
  echo "Language terms seeded.\n";
}
else {
  echo "Note: _bsi_csn_seed_languages() not available. Run 'drush updb' or enable bsi_csn module first.\n";
}

// Clear caches.
drupal_flush_all_caches();
echo "Caches cleared.\n";
