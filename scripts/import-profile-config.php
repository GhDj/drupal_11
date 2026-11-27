<?php

/**
 * @file
 * Imports CSN profile configuration into the active site.
 *
 * Usage: ddev drush php:script scripts/import-profile-config.php
 */

use Drupal\Core\Config\FileStorage;

$config_path = DRUPAL_ROOT . '/../config/sync';
$file_storage = new FileStorage($config_path);
$config_factory = \Drupal::configFactory();

// Import config in dependency order.
$configs = [
  // Profile type.
  'profile.type.first_responder',
  // Form mode.
  'core.entity_form_mode.profile.csn_profile',
  // Field storages.
  'field.storage.profile.field_academic_title',
  'field.storage.profile.field_given_name',
  'field.storage.profile.field_family_name',
  'field.storage.profile.field_phone',
  'field.storage.profile.field_address',
  'field.storage.profile.field_deh_phone',
  'field.storage.profile.field_deh_mail',
  'field.storage.profile.field_deh_language',
  'field.storage.profile.field_availability',
  'field.storage.profile.field_confirm_data_processing',
  'field.storage.profile.field_confirm_terms_conditions',
  'field.storage.profile.field_confirm_volunteer',
  // Field instances.
  'field.field.profile.first_responder.field_academic_title',
  'field.field.profile.first_responder.field_given_name',
  'field.field.profile.first_responder.field_family_name',
  'field.field.profile.first_responder.field_phone',
  'field.field.profile.first_responder.field_address',
  'field.field.profile.first_responder.field_deh_phone',
  'field.field.profile.first_responder.field_deh_mail',
  'field.field.profile.first_responder.field_deh_language',
  'field.field.profile.first_responder.field_availability',
  'field.field.profile.first_responder.field_confirm_data_processing',
  'field.field.profile.first_responder.field_confirm_terms_conditions',
  'field.field.profile.first_responder.field_confirm_volunteer',
  // Form display.
  'core.entity_form_display.profile.first_responder.csn_profile',
];

// Entity type map: configs that must be imported via entity storage.
$entity_type_map = [
  'profile.type' => 'profile_type',
  'core.entity_form_mode' => 'entity_form_mode',
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
    // Display config — use config factory.
    echo "Importing (config): $config_name\n";
    $config = $config_factory->getEditable($config_name);
    $config->setData($data);
    $config->save();
  }
}

echo "\nDone. Profile configuration imported successfully.\n";
echo "Clear caches: ddev drush cr\n";
