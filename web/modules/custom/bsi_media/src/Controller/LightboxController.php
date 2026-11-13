<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Controller;

use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Cache\CacheableAjaxResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
   * Open a dialog that displays the provided paragraph.
   */
  public function __invoke(Request $request, ParagraphInterface $paragraph): Response|array {
    if (!$request->isXmlHttpRequest()) {
      throw new AccessDeniedHttpException('Invalid request.');
    }
    if (!$paragraph->access('view')) {
      throw new AccessDeniedHttpException('Invalid paragraph.');
    }

    $title = $paragraph->get('field_section_title')->value ?? '';
    $build = [
      '#theme' => 'bsi_chart_lightbox',
      '#media_view' => NULL,
      '#body' => NULL,
    ];

    $cache = CacheableMetadata::createFromObject($paragraph);
    $langcode = $this->languageManager
      ->getCurrentLanguage()
      ->getId();

    // Lightbox might display media, e.g. image or diagram.
    $media = $paragraph->hasField('field_media')
      ? $paragraph->get('field_media')->entity
      : NULL;
    $cache->addCacheableDependency($media);
    if ($media?->hasTranslation($langcode)) {
      $media = $media->getTranslation($langcode);
    }
    if ($media && $media->access('view')) {
      $build['#media_view'] = $this->entityTypeManager->getViewBuilder('media')
        ->view($media, 'lightbox');
    }

    // Add paragraph content to lightbox.
    if ($paragraph->hasTranslation($langcode)) {
      $paragraph = $paragraph->getTranslation($langcode);
    }
    if (
      $paragraph->hasField('field_body') &&
      !$paragraph->get('field_body')->isEmpty()
    ) {
      $build['#body'] = $paragraph->get('field_body')->view([
        'label' => 'hidden',
      ]);
    }

    CacheableMetadata::createFromRenderArray($build)
      ->addCacheContexts(['user.permissions'])
      ->merge($cache)
      ->applyTo($build);

    $response = new CacheableAjaxResponse();
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
