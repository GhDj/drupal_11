<?php

// phpcs:ignoreFile

include __DIR__ . '/../settings.common.php';

if (file_exists(DRUPAL_ROOT . '/' . $site_path . '/settings.local.php')) {
  include DRUPAL_ROOT . '/' . $site_path . '/settings.local.php';
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = __DIR__ . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}
