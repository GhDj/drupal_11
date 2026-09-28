<?php

namespace Drupal\bsi_media\ContextProvider;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\media\Entity\Media;

/**
 * Sets the current media as a context on media routes.
 *
 * Functional copy of \Drupal\node\ContextProvider\NodeRouteContext.
 */
class MediaRouteContext implements ContextProviderInterface {

  use StringTranslationTrait;

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids): array {
    $result = [];
    $context_definition = EntityContextDefinition::create('media')->setRequired(FALSE);
    $value = NULL;
    if (($route_object = $this->routeMatch->getRouteObject())) {
      $route_contexts = $route_object->getOption('parameters');
      // Check for a media revision parameter first.
      if (isset($route_contexts['media_revision']) && $revision = $this->routeMatch->getParameter('media_revision')) {
        $value = $revision;
      }
      elseif (isset($route_contexts['media']) && $media = $this->routeMatch->getParameter('media')) {
        $value = $media;
      }
      elseif (isset($route_contexts['media_preview']) && $media = $this->routeMatch->getParameter('media_preview')) {
        $value = $media;
      }
      elseif ($this->routeMatch->getRouteName() == 'media.add') {
        $media_type = $this->routeMatch->getParameter('media_type');
        $value = Media::create(['type' => $media_type->id()]);
      }
    }

    $cacheability = new CacheableMetadata();
    $cacheability->setCacheContexts(['route']);

    $context = new Context($context_definition, $value);
    $context->addCacheableDependency($cacheability);
    $result['media'] = $context;

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts(): array {
    $context = EntityContext::fromEntityTypeId('media', $this->t('Media from URL'));
    return ['media' => $context];
  }

}
