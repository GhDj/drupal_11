<?php

/**
 * @file
 * Import CSN Incident Report config objects individually.
 *
 * Prerequisites:
 *   ddev composer require drupal/duration_field
 *
 * Usage: ddev drush php:script scripts/import-incident-report-config.php
 */

// Step 0: Check and enable required modules.
$module_installer = \Drupal::service('module_installer');
$module_handler = \Drupal::service('module_handler');

// Required: duration_field must be installed.
$required_modules = ['duration_field'];
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

// Optional: csn_custom (depends on group module). Enable if possible.
if (!$module_handler->moduleExists('csn_custom')) {
  echo "==> Enabling csn_custom...\n";
  try {
    $module_installer->install(['csn_custom']);
    echo "    csn_custom enabled.\n";
  }
  catch (\Exception $e) {
    echo "    WARNING: Could not enable csn_custom: " . $e->getMessage() . "\n";
    echo "    The CsnTaxonomySubtreeWidget won't be available — incident type fields will use default widget.\n";
    echo "    Continuing with config import...\n";
  }
}
else {
  echo "==> csn_custom already enabled.\n";
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
  // 1. Taxonomy vocabularies.
  'taxonomy.vocabulary.incident_types',
  'taxonomy.vocabulary.attack_vectors',

  // 2. Content type.
  'node.type.incident_report',

  // 3. Field storages.
  'field.storage.node.field_incident_types',
  'field.storage.node.field_incident_types_abuse',
  'field.storage.node.field_incident_types_abuse_other',
  'field.storage.node.field_incident_types_other',
  'field.storage.node.field_time_duration',

  // 4. Field instances.
  'field.field.node.incident_report.body',
  'field.field.node.incident_report.field_incident_types',
  'field.field.node.incident_report.field_incident_types_abuse',
  'field.field.node.incident_report.field_incident_types_abuse_other',
  'field.field.node.incident_report.field_incident_types_other',
  'field.field.node.incident_report.field_start_date',
  'field.field.node.incident_report.field_time_duration',

  // 5. Base field overrides.
  'core.base_field_override.node.incident_report.alias_keeper',
  'core.base_field_override.node.incident_report.node_keeper',
  'core.base_field_override.taxonomy_term.attack_vectors.description',
  'core.base_field_override.taxonomy_term.incident_types.description',

  // 6. Form display (depends on csn_custom for widget — imported separately below).

  // 7. View displays.
  'core.entity_view_display.node.incident_report.default',
  'core.entity_view_display.node.incident_report.explorer',
  'core.entity_view_display.node.incident_report.full',
  'core.entity_view_display.node.incident_report.opener',
  'core.entity_view_display.node.incident_report.search_result',
  'core.entity_view_display.node.incident_report.teaser',
  'core.entity_view_display.node.incident_report.teaser_highlight',
  'core.entity_view_display.node.incident_report.teaser_triplet',

  // 8. Workflow.
  'workflows.workflow.incident_report',

  // 9. Language settings.
  'language.content_settings.node.incident_report',
  'language.content_settings.taxonomy_term.attack_vectors',
  'language.content_settings.taxonomy_term.incident_types',

  // 10. Rabbit Hole (block taxonomy term pages).
  'rabbit_hole.behavior_settings.taxonomy_vocabulary_attack_vectors',
  'rabbit_hole.behavior_settings.taxonomy_vocabulary_incident_types',

  // 11. Other settings.
  'save_edit.settings',
  'user.role.csn_deh',
];

$entity_type_map = [
  'taxonomy.vocabulary' => 'taxonomy_vocabulary',
  'node.type' => 'node_type',
  'field.storage' => 'field_storage_config',
  'field.field' => 'field_config',
  'core.base_field_override' => 'base_field_override',
  'core.entity_form_display' => 'entity_form_display',
  'core.entity_view_display' => 'entity_view_display',
  'workflows.workflow' => 'workflow',
  'user.role' => 'user_role',
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
        // For settings-like entities (save_edit, user.role), update in place.
        if (in_array($config_name, ['save_edit.settings', 'user.role.csn_deh'])) {
          \Drupal::configFactory()->getEditable($config_name)->setData($data)->save();
          echo "[UPDATED] $config_name\n";
        }
        else {
          echo "[EXISTS] $config_name\n";
        }
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

// Import form display (may depend on csn_custom for the widget).
echo "\n==> Importing form display...\n";
$form_display_name = 'core.entity_form_display.node.incident_report.default';
$fd_data = $source->read($form_display_name);
if ($fd_data) {
  // If csn_custom is not enabled, remove its dependency and use default widget.
  if (!$module_handler->moduleExists('csn_custom')) {
    if (isset($fd_data['dependencies']['module'])) {
      $fd_data['dependencies']['module'] = array_values(
        array_diff($fd_data['dependencies']['module'], ['csn_custom'])
      );
    }
    // Switch csn_taxonomy_subtree widgets to default options_buttons.
    foreach ($fd_data['content'] ?? [] as $field_name => &$field_config) {
      if (isset($field_config['type']) && $field_config['type'] === 'csn_taxonomy_subtree') {
        $field_config['type'] = 'options_buttons';
        $field_config['settings'] = [];
        echo "    Switched $field_name widget to options_buttons (csn_custom not available)\n";
      }
    }
    unset($field_config);
  }
  try {
    $storage = $entity_type_manager->getStorage('entity_form_display');
    $existing = $storage->load($fd_data['id']);
    if ($existing) {
      echo "[EXISTS] $form_display_name\n";
    }
    else {
      unset($fd_data['uuid']);
      $entity = $storage->createFromStorageRecord($fd_data);
      $entity->save();
      echo "[CREATED] $form_display_name\n";
    }
  }
  catch (\Exception $e) {
    echo "[ERROR] $form_display_name — " . $e->getMessage() . "\n";
  }
}

// Import German translations.
echo "\n==> Importing German translations...\n";
$de_config_path = $config_path . '/language/de';
$de_source = new \Drupal\Core\Config\FileStorage($de_config_path);

$de_configs = [
  'node.type.incident_report',
  'taxonomy.vocabulary.attack_vectors',
  'taxonomy.vocabulary.incident_types',
  'workflows.workflow.incident_report',
  'core.base_field_override.taxonomy_term.attack_vectors.description',
  'core.base_field_override.taxonomy_term.incident_types.description',
  'core.entity_form_display.node.incident_report.default',
  'field.field.node.incident_report.body',
  'field.field.node.incident_report.field_incident_types',
  'field.field.node.incident_report.field_incident_types_abuse',
  'field.field.node.incident_report.field_incident_types_abuse_other',
  'field.field.node.incident_report.field_incident_types_other',
  'field.field.node.incident_report.field_start_date',
  'field.field.node.incident_report.field_time_duration',
];

$language_manager = \Drupal::languageManager();
foreach ($de_configs as $config_name) {
  $data = $de_source->read($config_name);
  if ($data === FALSE) {
    echo "[DE MISSING] $config_name\n";
    continue;
  }
  try {
    $language_manager->getLanguageConfigOverride('de', $config_name)->setData($data)->save();
    echo "[DE SAVED] $config_name\n";
  }
  catch (\Exception $e) {
    echo "[DE ERROR] $config_name — " . $e->getMessage() . "\n";
  }
}

echo "\n=== Done: $success imported, $errors errors ===\n";

drupal_flush_all_caches();
echo "Caches cleared.\n";

echo "\n==> Next steps:\n";
echo "    1. Import taxonomy terms via default_content or manually\n";
echo "    2. Visit /node/add/incident_report to test\n";
