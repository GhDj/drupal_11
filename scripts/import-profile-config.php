<?php

/**
 * @file
 * Import CSN Ersthelfer:in profile config objects individually.
 *
 * Prerequisites:
 *   ddev composer require drupal/profile
 *   ddev composer install
 *
 * Usage: ddev drush php:script scripts/import-profile-config.php
 */

// Step 0: Check and enable required modules.
$module_installer = \Drupal::service('module_installer');
$module_handler = \Drupal::service('module_handler');

$required_modules = ['profile', 'address', 'office_hours', 'telephone', 'csn_custom'];
foreach ($required_modules as $module) {
  if (!$module_handler->moduleExists($module)) {
    echo "==> Enabling $module...\n";
    try {
      $module_installer->install([$module]);
      echo "    $module enabled.\n";
    }
    catch (\Exception $e) {
      echo "    ERROR enabling $module: " . $e->getMessage() . "\n";
      echo "    Run: ddev composer require drupal/$module\n";
      return;
    }
  }
  else {
    echo "==> $module already enabled.\n";
  }
}

$config_path = DRUPAL_ROOT . '/../config/sync';
if (!is_dir($config_path)) {
  echo "ERROR: Config sync directory not found at $config_path\n";
  return;
}

$source = new \Drupal\Core\Config\FileStorage($config_path);
$config_storage = \Drupal::service('config.storage');
$entity_type_manager = \Drupal::entityTypeManager();

// Configs in dependency order.
$configs = [
  // 1. Taxonomy vocabulary.
  'taxonomy.vocabulary.csn_languages',

  // 2. Profile type.
  'profile.type.first_responder',

  // 3. Field storages.
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

  // 4. Field instances.
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

  // 5. Form mode + form display.
  'core.entity_form_mode.profile.csn_profile',
  'core.entity_form_display.profile.first_responder.csn_profile',

  // 6. CSN branding block.
  'block.block.csn_branding',
];

$entity_type_map = [
  'taxonomy.vocabulary' => 'taxonomy_vocabulary',
  'profile.type' => 'profile_type',
  'field.storage' => 'field_storage_config',
  'field.field' => 'field_config',
  'core.entity_form_mode' => 'entity_form_mode',
  'core.entity_form_display' => 'entity_form_display',
  'core.entity_view_display' => 'entity_view_display',
  'block.block' => 'block',
];

$success = 0;
$errors = 0;

foreach ($configs as $config_name) {
  $data = $source->read($config_name);
  if ($data === FALSE) {
    echo "[MISSING] $config_name\n";
    $errors++;
    continue;
  }

  $entity_type_id = NULL;
  foreach ($entity_type_map as $prefix => $type_id) {
    if (str_starts_with($config_name, $prefix)) {
      $entity_type_id = $type_id;
      break;
    }
  }

  try {
    if ($entity_type_id) {
      $storage = $entity_type_manager->getStorage($entity_type_id);
      $entity_type_def = $entity_type_manager->getDefinition($entity_type_id);
      $id_key = $entity_type_def->getKey('id');
      $id = $data[$id_key] ?? $data['id'] ?? NULL;

      if ($id && $storage->load($id)) {
        echo "[EXISTS] $config_name\n";
      }
      else {
        unset($data['uuid']);
        $entity = $storage->createFromStorageRecord($data);
        $entity->save();
        echo "[CREATED] $config_name\n";
      }
    }
    else {
      \Drupal::configFactory()->getEditable($config_name)->setData($data)->save();
      echo "[SAVED] $config_name\n";
    }
    $success++;
  }
  catch (\Exception $e) {
    echo "[ERROR] $config_name — " . $e->getMessage() . "\n";
    $errors++;
  }
}

echo "\n=== Done: $success imported, $errors errors ===\n";

// Seed language terms.
echo "\n==> Seeding CSN language terms...\n";
$module_path = \Drupal::service('extension.list.module')->getPath('csn_custom');
$install_file = $module_path . '/csn_custom.install';
if (file_exists($install_file)) {
  require_once $install_file;
}
if (function_exists('_csn_custom_seed_languages')) {
  _csn_custom_seed_languages();
  echo "    Language terms seeded.\n";
}

drupal_flush_all_caches();
echo "Caches cleared.\n";
