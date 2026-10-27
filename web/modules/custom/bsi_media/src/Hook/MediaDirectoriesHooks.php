<?php

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Entity hooks related to media_directories integration.
 */
final class MediaDirectoriesHooks {

  /**
   * Implements hook_ckeditor5_plugin_info_alter().
   */
  #[Hook('ckeditor5_plugin_info_alter')]
  public function disableImageOptionsPlugin(array &$plugin_definitions): void {
    unset($plugin_definitions['media_directories_browser_image_options']);
  }

}
