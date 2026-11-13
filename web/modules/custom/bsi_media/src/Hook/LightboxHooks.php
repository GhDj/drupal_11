<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Hook;

use Drupal\bsi_media\Controller\LightboxController;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Hooks for Chart lightbox logic.
 */
final class LightboxHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'bsi_chart_lightbox' => [
        'variables' => [
          'section_title' => NULL,
          'media_view' => NULL,
          'body' => NULL,
        ],
        'template' => 'bsi-chart-lightbox',
      ],
    ];
  }

  /**
   * Implements hook_preprocess_HOOK() for paragraph.html.twig.
   */
  #[Hook('preprocess_paragraph')]
  public function preprocessParagraph(array &$variables): void {
    $paragraph = $variables['paragraph'];
    if (!$paragraph instanceof ParagraphInterface || $paragraph->bundle() !== 'tile_item') {
      return;
    }

    $settings = [
      'callback' => LightboxController::class,
      'event' => 'click',
      'url' => Url::fromRoute('bsi_media.lightbox', [
        'paragraph' => $paragraph->id(),
      ])->toString(TRUE)->getGeneratedUrl(),
      'selector' => '[data-lightbox="' . $paragraph->id() . '"]',
    ];

    $variables['#attached']['library'][] = 'core/drupal.ajax';
    $variables['#attached']['library'][] = 'core/drupal.dialog.ajax';
    // Mark the URL as trusted, see ajax.js.
    $variables['#attached']['drupalSettings']['ajaxTrustedUrl'][$settings['url']] = TRUE;
    $variables['#attached']['drupalSettings']['ajax']['open-lightbox-' . $paragraph->id()] = $settings;

    $variables['lightbox_attributes'] = new Attribute([
      'href' => Url::fromRoute('<none>')
        ->toString(TRUE)
        ->getGeneratedUrl(),
      'class' => ['use-ajax'],
      'data-lightbox' => $paragraph->id(),
      'data-dialog-type' => 'modal',
    ]);

    $media = $paragraph->get('field_media')->entity;
    if (isset($media)) {
      $media->parent_paragraph = $paragraph;
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for media.html.twig.
   */
  #[Hook('preprocess_media')]
  public function preprocessMedia(array &$variables): void {
    $media = $variables['media'];

    if (!$media instanceof MediaInterface) {
      return;
    }

    if (!in_array($media->bundle(), ['chart', 'image'], TRUE)) {
      return;
    }

    $paragraph = $media->parent_paragraph ?? NULL;
    if (!$paragraph instanceof ParagraphInterface) {
      return;
    }

    $variables['lightbox_url'] = Url::fromRoute(
      'bsi_media.lightbox',
      [
        'paragraph' => $paragraph->id(),
      ]
    )->toString();
  }

}
