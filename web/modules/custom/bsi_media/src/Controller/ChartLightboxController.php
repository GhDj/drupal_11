<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to build Chart lightbox.
 */
final class ChartLightboxController extends ControllerBase {

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
  public function __invoke(ParagraphInterface $paragraph, MediaInterface $media): AjaxResponse {
    $langcode = $this->languageManager
      ->getCurrentLanguage()
      ->getId();

    if ($media->hasTranslation($langcode)) {
      $media = $media->getTranslation($langcode);
    }

    if ($paragraph->hasTranslation($langcode)) {
      $paragraph = $paragraph->getTranslation($langcode);
    }

    $media_view = $this->entityTypeManager
      ->getViewBuilder('media')
      ->view($media, 'lightbox');

    $body = [];

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
      '#section_title' => $paragraph->get('field_section_title')->value ?? '',
      '#media_view' => $media_view,
      '#body' => $body,
    ];

    $response = new AjaxResponse();
    $response->addCommand(
      new OpenModalDialogCommand(
        $paragraph->label(),
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
