<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\ByteSizeMarkup;
use Drupal\file\FileInterface;

/**
 * Hooks related to link theming.
 */
class LinkHooks implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return [
      'preRenderLink',
    ];
  }

  /**
   * Implements hook_element_info_alter().
   */
  #[Hook('element_info_alter')]
  public function alterElements(array &$info): void {
    if (isset($info['link'])) {
      array_unshift($info['link']['#pre_render'], [static::class, 'preRenderLink']);
    }
  }

  /**
   * Pre-render callback for Link render elements.
   */
  public static function preRenderLink(array $element): array {
    /** @var \Drupal\Core\Url $url */
    $url = $element['#url'] ?? NULL;
    if ($url === NULL) {
      return $element;
    }
    if (!$url->isExternal() && (!$url->isRouted() || $url->getRouteName() === '<button>')) {
      // @todo There might be other routes we do not want to alter.
      // Do not interfere with unusual setups.
      return $element;
    }

    $internal_menus = ['admin', 'devel'];
    $css_classes = array_merge([], $element['#attributes']['class'] ?? [], $url->getOption('attributes')['class'] ?? []);
    if (in_array($url->getOption('menu_name') ?? NULL, $internal_menus, TRUE)
      || in_array('toolbar-icon', $css_classes, TRUE)) {
      // Do not interfere with administrative menus.
      return $element;
    }

    $link_type = NULL;
    if ($url->isExternal()) {
      $link_type = 'external';
    }
    elseif ($url->getRouteName() === 'media_entity_download.download') {
      $link_type = 'download';
    }
    elseif ($url->getOption('entity') !== NULL || $url->getOption('set_active_class') === TRUE) {
      // It's hard to detect if this is a link that's relevant for the frontend
      // theme, as e.g. admin toolbar links should not be altered. We check:
      // 1) link fields, which probably always set the 'entity' option
      // 2) menu links via 'set_active_class' option.
      $link_type = 'internal';
    }

    if ($link_type !== NULL) {
      // Use our custom template.
      $element = [
        '#theme' => 'link__' . $link_type,
        '#link_type' => $link_type,
      ] + $element;
    }

    return $element;
  }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(array $existing, string $type, string $theme, string $path): array {
    return [
      'link' => [
        'render element' => 'element',
      ],
    ];
  }

  /**
   * Implements hook_preprocess_HOOK() for link.
   */
  #[Hook('preprocess_link')]
  public function preprocessLink(array &$variables): void {
    $properties = [
      'title',
      'url',
      'icon',
      'link_type',
      'add_on',
      'extra_classes',
    ];
    foreach ($properties as $property) {
      $variables[$property] = $variables['element']['#' . $property] ?? NULL;
    }

    if ($variables['link_type'] === 'download') {
      if ($media = $variables['element']['#url']->getOption('entity')) {
        $source_field = $media->getSource()->getConfiguration()['source_field'];
        if (($file = $media->get($source_field)->entity) instanceof FileInterface) {
          $variables['file_size'] = (string) ByteSizeMarkup::create($file->getSize());
          $variables['file_extension'] = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        }
      }
    }
  }

}
