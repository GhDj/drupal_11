<?php

namespace Drupal\bsi_editor\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5Plugin\Language;
use Drupal\editor\EditorInterface;

/**
 * CKEditor 5 language plugin override that restricts available options.
 */
class LanguageSelect extends Language {

  /**
   * List of langcodes we want to offer in the editor.
   */
  private static array $desiredLangcodes = ['en', 'fr', 'de', 'es', 'ja'];

  /**
   * {@inheritDoc}
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {
    $config = parent::getDynamicPluginConfig($static_plugin_config, $editor);
    $desiredLangcodes = static::$desiredLangcodes;

    // @todo Language names are not translated.
    $languages = &$config['language']['textPartLanguage'];
    $languages = array_values(array_filter(
      $config['language']['textPartLanguage'],
      function (array $language) use ($desiredLangcodes) {
        return in_array($language['languageCode'], $desiredLangcodes);
      }
    ));

    return $config;
  }

}
