<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Hooks for Chart lightbox logic.
 */
final class ChartHooks {

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
   * Implements hook_preprocess_paragraph().
   */
  #[Hook('preprocess_paragraph')]
  public function preprocessParagraph(array &$variables): void {
    $paragraph = $variables['paragraph'];

    if (!$paragraph instanceof ParagraphInterface) {
      return;
    }

    if ($paragraph->bundle() !== 'tile_item') {
      return;
    }

    $media = $paragraph->get('field_media')->entity;

    if (isset($media)) {
      $media->parent_paragraph = $paragraph;
    }

  }

  /**
   * Implements hook_preprocess_media().
   */
  #[Hook('preprocess_media')]
  public function preprocessMedia(array &$variables): void {
    $media = $variables['media'];

    if (!$media instanceof MediaInterface) {
      return;
    }

    if ($media->bundle() !== 'chart') {
      return;
    }

    if (
      !$media->hasField('field_media_chart') ||
      $media->get('field_media_chart')->isEmpty()
    ) {
      return;
    }

    if ($media->parent_paragraph) {
      $paragraph = $media->parent_paragraph;
      if ($paragraph instanceof ParagraphInterface) {
        $paragraph_id = $paragraph->id();

        $variables['lightbox_url'] = Url::fromRoute(
          'bsi_media.chart_lightbox',
          [
            'paragraph' => $paragraph_id,
            'media' => $media->id(),
          ]
        )->toString();
      }
    }

  }

}
