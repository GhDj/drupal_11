<?php

// phpcs:ignoreFile

/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */


$config['system.logging']['error_level'] = 'verbose';

// $config['user.role.authenticated']['permissions'][] = 'access kint';
// $config['user.role.anonymous']['permissions'][] = 'access kint';

$settings['skip_permissions_hardening'] = TRUE;

// Make the site accessible from all hosts.
$settings['trusted_host_patterns'] = ['.*'];


// ini_set('memory_limit', '1024M');
// ini_set('max_execution_time', 0);

$local_dev = TRUE;
$local_dev = FALSE;

if ($local_dev) {

  if (file_exists(DRUPAL_ROOT . '/' . $site_path . '/services.development.yml')) {
    // Enable local development services.
    $settings['container_yamls'][] = DRUPAL_ROOT . '/' . $site_path . '/services.development.yml';

    // Disable caches.
    $settings['cache']['bins']['render'] = 'cache.backend.null';
    $settings['cache']['bins']['page'] = 'cache.backend.null';
    $settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
  }

  // Disable CSS and JS aggregation.
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['css']['gzip'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;
  $config['system.performance']['js']['gzip'] = FALSE;
  $config['system.performance']['cache']['page']['max_age'] = 0;

}
