<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to build Chart lightbox.
 */
final class LightboxController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    protected $languageManager,
    protected $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('language_manager'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(ParagraphInterface $paragraph): AjaxResponse {
    $title = $paragraph->get('field_section_title')->value ?? '';
    $body = [];

    $langcode = $this->languageManager
      ->getCurrentLanguage()
      ->getId();

    $media = $paragraph->hasField('field_media')
      ? $paragraph->get('field_media')->entity
      : NULL;
    if ($media && $media->hasTranslation($langcode)) {
      $media = $media->getTranslation($langcode);
    }

    $media_view = $media ? $this->entityTypeManager->getViewBuilder('media')
      ->view($media, 'lightbox') : NULL;

    if ($paragraph->hasTranslation($langcode)) {
      $paragraph = $paragraph->getTranslation($langcode);
    }
    if (
      $paragraph->hasField('field_body') &&
      !$paragraph->get('field_body')->isEmpty()
    ) {
      $body = $paragraph->get('field_body')->view([
        'label' => 'hidden',
      ]);
    }

    $build = [
      '#theme' => 'bsi_chart_lightbox',
      '#media_view' => $media_view,
      '#body' => $body,
    ];

    $response = new AjaxResponse();
    $response->addCommand(
      new OpenModalDialogCommand(
        $title,
        $build,
        [
          'width' => '90%',
          'dialogClass' => 'bsi-chart-dialog',
        ]
      )
    );
    return $response;

  }

}
