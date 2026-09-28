<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\bsi_custom\Plugin\ConfigPagesContext\StaticOpener;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Hooks related to page openers.
 */
class OpenerHooks {

  use StringTranslationTrait;

  const string VARIANT_HERO = 'hero';
  const string VARIANT_IMAGE = 'image';
  const string VARIANT_STATIC = 'static';

  const string PAGE_FRONT = 'front';

  public function __construct(
    protected readonly PathMatcherInterface $pathMatcher,
    #[Autowire(service: 'config_pages.loader')]
    protected readonly ConfigPagesLoaderServiceInterface $configPagesLoader,
  ) {}

  /**
   * Implements hook_preprocess_HOOK for node--opener.html.twig.
   */
  #[Hook('preprocess_node__opener')]
  public function alterNodeOpener(array &$variables, string $hook, array $context): void {
    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $variables['elements']['#node'];
    $has_image = $entity->hasField('field_media') && !$entity->get('field_media')->isEmpty();

    $variant = static::VARIANT_STATIC;
    $sub_variant = NULL;
    if ($entity->bundle() === 'entry_page') {
      $page_type = StaticOpener::getLayoutBuilderPageType($entity);
      if ($this->pathMatcher->isFrontPage()) {
        $page_type = static::PAGE_FRONT;
      }

      $variant = static::VARIANT_HERO;
      if ($page_type === static::PAGE_FRONT) {
        $variables['content']['title']['#attributes']['class'][] = 'visually-hidden';
        unset($variables['content']['body'], $variables['content']['field_media']);

        $sub_variant = 'front';
      }
      elseif ($entity->hasField('field_stage_items') && !$entity->get('field_stage_items')->isEmpty()) {
        unset($variables['content']['body'], $variables['content']['field_media']);
        unset($variables['content']['field_stage_extra']);

        $sub_variant = 'stage';
      }
      elseif ($page_type !== NULL) {
        $variant = static::VARIANT_STATIC;
        $sub_variant = $page_type;
      }
      elseif ($has_image) {
        $sub_variant = 'image';
      }
    }
    elseif ($entity->bundle() === 'article') {
      $variant = static::VARIANT_STATIC;
      $sub_variant = $entity->get('field_article_type')->value ?? NULL;
    }
    elseif ($entity->bundle() === 'report_page' && $has_image) {
      $variant = static::VARIANT_HERO;
    }
    elseif ($has_image) {
      $variant = static::VARIANT_IMAGE;
      $sub_variant = $entity->bundle();
    }

    if ($variant === static::VARIANT_STATIC) {
      // Pass static image to template.
      $variables['content']['field_media'] = $this->configPagesLoader
        ->getFieldView('page_opener', 'field_media');

      CacheableMetadata::createFromRenderArray($variables)
        ->merge(CacheableMetadata::createFromRenderArray($variables['content']['field_media']))
        ->applyTo($variables);
    }

    $variables['variant'] = $variant;
    $variables['sub_variant'] = $sub_variant;
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for node.
   */
  #[Hook('theme_suggestions_node_alter')]
  public function alterNodeThemeSuggestions(array &$suggestions, array &$variables, string $hook): void {
    if ($variables['elements']['#view_mode'] !== 'opener') {
      return;
    }

    $dummy_variables = [] + $variables;
    $this->alterNodeOpener($dummy_variables, $hook, []);
    $variant = $dummy_variables['variant'] ?: self::VARIANT_STATIC;
    $sub_variant = $dummy_variables['sub_variant'] ?: NULL;

    $suggestions[] = implode('__', [
      $hook,
      $variables['elements']['#view_mode'],
      $variant,
    ]);
    if ($sub_variant !== NULL) {
      $suggestions[] = implode('__', [
        $hook,
        $variables['elements']['#view_mode'],
        $variant,
        $sub_variant,
      ]);
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for field.
   */
  #[Hook('theme_suggestions_field_alter')]
  public function alterFieldThemeSuggestions(array &$suggestions, array $variables): void {
    $element = $variables['element'];

    if (($element['#entity_type'] ?? NULL) !== 'node'
      || ($element['#bundle'] ?? NULL) !== 'entry_page'
      || ($element['#view_mode'] ?? NULL) !== 'opener'
      || !in_array($element['#field_name'] ?? NULL, ['field_stage_items', 'field_stage_extra'], TRUE)
    ) {
      return;
    }

    $pageContext = $this->pathMatcher->isFrontPage() ? 'front' : 'default';
    foreach ([] + $suggestions as $suggestion) {
      $suggestions[] = $suggestion . '__' . $pageContext;
    }
  }

  /**
   * Implements hook_preprocess_HOOK for media--opener.html.twig.
   */
  #[Hook('preprocess_media__opener')]
  public function alterMediaOpener(array &$variables, string $hook, array $context): void {
    /** @var \Drupal\media\MediaInterface $entity */
    $entity = $variables['elements']['#media'];

    $variables['variant'] = static::VARIANT_STATIC;
    $variables['sub_variant'] = $entity->bundle();

    // Pass static image to template.
    $variables['content']['field_media'] = $this->configPagesLoader
      ->getFieldView('page_opener', 'field_media');

    CacheableMetadata::createFromRenderArray($variables)
      ->merge(CacheableMetadata::createFromRenderArray($variables['content']['field_media']))
      ->applyTo($variables);
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for media.
   */
  #[Hook('theme_suggestions_media_alter')]
  public function alterMediaThemeSuggestions(array &$suggestions, array &$variables, string $hook): void {
    if ($variables['elements']['#view_mode'] !== 'opener') {
      return;
    }

    $dummy_variables = [] + $variables;
    $this->alterMediaOpener($dummy_variables, $hook, []);
    $variant = $dummy_variables['variant'] ?: self::VARIANT_STATIC;
    $sub_variant = $dummy_variables['sub_variant'] ?: NULL;

    $suggestions[] = implode('__', [
      $hook,
      $variables['elements']['#view_mode'],
      $variant,
    ]);
    if ($sub_variant !== NULL) {
      $suggestions[] = implode('__', [
        $hook,
        $variables['elements']['#view_mode'],
        $variant,
        $sub_variant,
      ]);
    }
  }

}
