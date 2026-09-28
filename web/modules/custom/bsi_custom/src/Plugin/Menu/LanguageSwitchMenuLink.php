<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Menu;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a menu link to switch to the current page in another language.
 *
 * @todo Add language deriver instead of manual entry in .links.menu.yml.
 */
class LanguageSwitchMenuLink extends MenuLinkDefault implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    StaticMenuLinkOverridesInterface $static_override,
    protected readonly LanguageManagerInterface $languageManager,
    protected readonly RouteMatchInterface $routeMatch,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu_link.static.overrides'),
      $container->get('language_manager'),
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlObject($title_attribute = TRUE): Url {
    $url = Url::fromRouteMatch($this->routeMatch);
    $url = $this->getTranslatedUrl($url);

    $options = $url->getOptions() + $this->getOptions();
    if ($title_attribute && $description = $this->getDescription()) {
      $options['attributes']['title'] = $description;
    }
    $url->setOptions($options);

    return $url;
  }

  /**
   * Get the url object for the translated page. Fallback to <front>.
   */
  protected function getTranslatedUrl(Url $current_url): Url {
    $links = $this->languageManager->getLanguageSwitchLinks(LanguageInterface::TYPE_INTERFACE, $current_url);
    $langcode = $this->getDerivativeId();

    $entities = array_filter(
      $this->routeMatch->getParameters()->all(),
      static fn (mixed $parameter) => $parameter instanceof ContentEntityInterface
    );

    $url = Url::fromRoute('<front>');
    if ($link = ($links->links[$langcode] ?? NULL)) {
      $is_translated = array_all($entities, fn (ContentEntityInterface $entity)
        => $entity->isTranslatable() && $entity->hasTranslation($langcode)
      );
      if ($is_translated) {
        $url = $link['url'];
      }
    }

    $url->setOption('language', $this->languageManager->getNativeLanguages()[$langcode]);
    $url->setOption('_link', $link);
    $url->setOption('_entities', $entities);

    return $url;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(): mixed {
    /** @var \Drupal\language\Entity\ConfigurableLanguage $language */
    $language = $this->getUrlObject()->getOption('language');

    return $language?->getName() ?? parent::getTitle();
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(): bool {
    return $this->getDerivativeId() !== $this->languageManager->getCurrentLanguage()->getId()
      && $this->getUrlObject()->access();
  }

  /**
   * Gather all cache metadata for this link.
   */
  protected function getCacheableMetadata(): CacheableMetadata {
    $url = $this->getUrlObject();
    $access_result = $url->access(NULL, TRUE);

    $cache_metadata = CacheableMetadata::createFromObject($access_result);
    $cache_metadata->addCacheContexts([
      'url.path',
      'url.query_args',
      'url.site',
      'languages:' . LanguageInterface::TYPE_INTERFACE,
    ]);

    // The url that was initially provided as the language switch link.
    $link = $url->getOption('_link');
    if ($link && $url !== $link['url']) {
      // Rewritten link still relies on original link target.
      $cache_metadata->addCacheableDependency($link['url']);
    }
    foreach ($url->getOption('_entities') ?? [] as $entity) {
      $cache_metadata->addCacheableDependency($entity);
    }

    return $cache_metadata;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return $this->getCacheableMetadata()->getCacheContexts();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return $this->getCacheableMetadata()->getCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return $this->getCacheableMetadata()->getCacheMaxAge();
  }

}
