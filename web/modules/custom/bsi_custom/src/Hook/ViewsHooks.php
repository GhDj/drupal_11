<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;

/**
 * Hooks related to views rendering and behavior.
 */
class ViewsHooks {

  /**
   * Implements template_preprocess_HOOK() for views-view-field.html.twig.
   */
  #[Hook('preprocess_views_view_field')]
  public function preprocessViewsField(array &$variables): void {
    $entity = $variables['row']->_entity ?? NULL;
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'product_approval') {
      return;
    }

    // @todo Simplify! There are three ways to render a search_api field:
    //   1. Fallback rendering (fallback_options.link_to_item)
    //   2. Entity field rendering (field_rendering + settings.link_to_entity)
    //   3. Entity field rendering + field template (not using this preprocess)
    if ($variables['field']->field === 'title'
      && ($variables['field']->options['settings']['link_to_entity'] ?? NULL) === TRUE
      && ($media = $entity->get('field_media')->entity)
      && $media->access('view')
    ) {
      // Rewrite output to link to media download.
      $variables['output'] = Link::createFromRoute(
        $entity->label(),
        'media_entity_download.download',
        [
          'media' => $media->id(),
        ],
      );
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK() for views-view-field.html.twig.
   */
  #[Hook('theme_suggestions_views_view_field')]
  public function viewsFieldSuggestions(array $variables): array {
    $options = $variables['field']->options;
    $hook = 'views_view_field';

    $field_name = $options['field'];
    $entity_type = $options['entity_type'] ?? NULL;
    $renderer = $options['plugin_id'];
    if (isset($options['field_rendering'])) {
      $renderer = $options['field_rendering'] === TRUE ? 'field' : 'fallback';
    }
    $render_type = $renderer === 'field' ? $options['type'] : $options['fallback_handler'] ?? NULL;

    $suggestions = [];
    if ($entity_type !== NULL) {
      $suggestions[] = implode('__', [$hook, $entity_type]);
      $suggestions[] = implode('__', [$hook, $field_name]);
      $suggestions[] = implode('__', [$hook, $entity_type, $field_name]);
    }
    else {
      $suggestions[] = implode('__', [$hook, $field_name]);
    }
    if ($renderer) {
      $suggestions[] = implode('__', [$hook, $renderer]);
      if ($render_type !== NULL) {
        $suggestions[] = implode('__', [$hook, $renderer, $render_type]);
      }
      if ($entity_type !== NULL) {
        $suggestions[] = implode('__', [$hook, $renderer, $entity_type]);
        $suggestions[] = implode('__', [$hook, $renderer, $entity_type, $field_name]);
      }
    }

    return $suggestions;
  }

}
