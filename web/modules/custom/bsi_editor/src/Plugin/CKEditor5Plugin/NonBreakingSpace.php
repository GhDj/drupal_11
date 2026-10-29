<?php

namespace Drupal\bsi_editor\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 Plugin: geschütztes Leerzeichen (&nbsp;).
 */
#[\Drupal\ckeditor5\Attribute\CKEditor5Plugin(
  id: 'bsi_editor_nbsp',
  ckeditor5: [
    'plugins' => ['bsiEditorNbsp.NonBreakingSpaceEditing'],
  ],
  drupal: [
    'label' => new TranslatableMarkup('Non-breaking space'),
    'library' => 'bsi_editor/ckeditor5.nbsp',
    'admin_library' => 'bsi_editor/ckeditor5.nbsp.admin',
    'toolbar_items' => [
      'nbsp' => [
        'label' => new TranslatableMarkup('Insert non-breaking space'),
      ],
    ],
    'elements' => FALSE,
  ],
)]
class NonBreakingSpace extends CKEditor5PluginDefault {

  /**
   * {@inheritdoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    return $static_plugin_config;
  }

}
