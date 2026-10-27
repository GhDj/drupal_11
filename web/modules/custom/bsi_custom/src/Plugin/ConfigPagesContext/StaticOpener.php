<?php

namespace Drupal\bsi_custom\Plugin\ConfigPagesContext;

use Drupal\config_pages\ConfigPagesContextBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a static opener config pages context.
 *
 * @ConfigPagesContext(
 *   id = "static_opener",
 *   label = @Translation("Static opener"),
 * )
 */
class StaticOpener extends ConfigPagesContextBase {

  use StringTranslationTrait;

  public const string QUERY_PARAM = 'static_opener';

  /**
   * List of bundles that will not be offered as context configuration options.
   */
  protected static array $excludedBundles = [
    // Handling based on field_article_type.
    'article',
    // @see OpenerHooks::VARIANT_HERO.
    'entry_page',
    // @see OpenerHooks::VARIANT_IMAGE.
    'page',
    'report_page',
  ];

  const string PAGE_FORM = 'form';
  const string PAGE_SEARCH = 'search';
  const string PAGE_MEDIA = 'media';

  /**
   * List of views ids that will be scanned for block_search* displays.
   */
  public static array $searchViews = [
    'search',
    'search_pages',
    'search_lists',
    'search_media',
  ];

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly EntityFieldManagerInterface $entityFieldManager,
    protected readonly CurrentRouteMatch $currentRouteMatch,
    protected readonly RequestStack $requestStack,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('current_route_match'),
      $container->get('request_stack'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): string {
    foreach ($this->getLinks() as $link) {
      if ($link['selected'] === TRUE) {
        return $link['title'];
      }
    }
    return $this->t('None');
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(): ?string {
    $default = static::PAGE_SEARCH;
    if ($this->currentRouteMatch->getRouteName() === NULL) {
      // Is this even possible?
      return $default;
    }

    $entity = $this->currentRouteMatch->getParameter('node')
      ?? $this->currentRouteMatch->getParameter('media');
    if (
      !$entity instanceof EntityInterface
      && ($query_param = $this->requestStack->getCurrentRequest()->query->get(static::QUERY_PARAM))
    ) {
      return $query_param;
    }

    if (!$entity) {
      return $default;
    }

    if ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'article') {
      return $entity->get('field_article_type')->value;
    }
    elseif ($entity->getEntityTypeId() === 'node' && $entity->bundle() === 'entry_page') {
      return $this->getLayoutBuilderPageType($entity);
    }

    return $entity->bundle();
  }

  /**
   * {@inheritdoc}
   */
  public function getLinks(): array {
    $links = [];

    $links = array_merge($links, $this->getValueLinks([
      static::PAGE_SEARCH => $this->t('Search page'),
      static::PAGE_MEDIA => $this->t('Media library'),
      static::PAGE_FORM => $this->t('Form'),
    ]));

    /** @var array<string, \Drupal\node\Entity\NodeType> $node_bundles */
    $node_bundles = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    $links = array_merge($links, $this->getBundleLinks($node_bundles));

    if (isset($node_bundles['article'])) {
      $type_field = $this->entityFieldManager->getFieldDefinitions('node', 'article')['field_article_type'];
      $allowed_values = $type_field->getFieldStorageDefinition()->getSetting('allowed_values');
      $links = array_merge($links, $this->getValueLinks($allowed_values));
    }

    /** @var array<string, \Drupal\media\Entity\MediaType> $media_bundles */
    $media_bundles = $this->entityTypeManager->getStorage('media_type')->loadMultiple();
    $links = array_merge($links, $this->getBundleLinks($media_bundles));

    // Mark currently active element.
    $value = $this->getValue();
    if (isset($links[$value])) {
      $links[$value]['selected'] = TRUE;
    }

    return array_values($links);
  }

  /**
   * Get links for the provided bundle types.
   *
   * @param array<\Drupal\Core\Config\Entity\ConfigEntityTypeInterface&ConfigEntityInterface> $bundles
   *   Map of bundle entity types keyed by bundle name.
   *
   * @return array<string, array{'title': string, 'href': \Drupal\Core\Url, 'selected': bool, 'value': mixed}>
   *   Links for use in ::getLinks.
   */
  protected function getBundleLinks(array $bundles): array {
    $bundles = array_filter($bundles, fn(EntityInterface $bundle) => $this->hasCanonicalPage($bundle));
    return $this->getValueLinks(array_map(
      static fn(EntityInterface $bundle) => $bundle->label(),
      $bundles,
    ));
  }

  /**
   * Get links from the provided values.
   *
   * @param array<string, string|\Drupal\Core\StringTranslation\TranslatableMarkup> $values
   *   The key-value map to transform into links.
   *
   * @return array<string, array{'title': string, 'href': \Drupal\Core\Url, 'selected': bool, 'value': mixed}>
   *   Links for use in ::getLinks.
   */
  protected function getValueLinks(array $values): array {
    $links = [];
    foreach ($values as $value => $label) {
      if (in_array($value, static::$excludedBundles, TRUE)) {
        continue;
      }
      $links[$value] = [
        'title' => $label,
        'href' => Url::fromRoute('<current>', $this->currentRouteMatch->getParameters()->all(), [
          'query' => [static::QUERY_PARAM => $value] + $this->requestStack->getCurrentRequest()->query->all(),
        ]),
        'selected' => FALSE,
        'value' => $value,
      ];
    }
    return $links;
  }

  /**
   * Whether the given bundle entity may have a canonical page.
   */
  protected function hasCanonicalPage(ConfigEntityInterface $bundleType): bool {
    /** @var \Drupal\rabbit_hole\BehaviorSettingsManagerInterface $rabbit_hole */
    $rabbit_hole = \Drupal::service('rabbit_hole.behavior_settings_manager');

    $rh_settings = $rabbit_hole->loadBehaviorSettingsAsConfig(
      $bundleType->getEntityTypeId(),
      $bundleType->id(),
    );

    return $rh_settings->isNew()
      || $rh_settings->get('action') === 'display_page'
      || $rh_settings->get('allow_override');
  }

  /**
   * Get the page type of the given entity, if using layout_builder.
   */
  public static function getLayoutBuilderPageType(ContentEntityInterface $entity): ?string {
    if (!$entity->hasField('layout_builder__layout')) {
      return NULL;
    }

    /** @var \Drupal\layout_builder\SectionListInterface $field_list */
    $field_list = $entity->get('layout_builder__layout');
    foreach ($field_list->getSections() as $section) {
      foreach ($section->getComponents() as $component) {
        $plugin = $component->getPlugin();
        if (!$plugin instanceof BlockPluginInterface) {
          continue;
        }
        if ($plugin->getBaseId() === 'webform_block') {
          return static::PAGE_FORM;
        }
        elseif ($plugin->getBaseId() === 'views_block') {
          [$view_id, $display_id] = explode('-', $plugin->getDerivativeId());

          if (
            in_array($view_id, static::$searchViews, TRUE)
            && ($display_id === 'block_search' || str_starts_with($display_id, 'block_search'))
          ) {
            return static::PAGE_SEARCH;
          }
          elseif ($view_id === 'search_media' && $display_id === 'block_media') {
            return static::PAGE_MEDIA;
          }
        }
      }
    }
    return NULL;
  }

}
