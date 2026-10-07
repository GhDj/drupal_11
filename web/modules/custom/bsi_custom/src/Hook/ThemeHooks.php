<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Hooks related to frontend theming and general preprocessing.
 */
class ThemeHooks {

  public function __construct(
    protected readonly ThemeManagerInterface $themeManager,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for menu.html.twig.
   */
  #[Hook('preprocess_menu')]
  public function preprocessMenu(array &$variables): void {
    $menu_name = $variables['menu_name'];
    foreach ($variables['items'] as &$item) {
      $item['url']->setOption('menu_name', $menu_name);
    }
  }

  /**
   * Implements hook_theme_registry_alter().
   */
  #[Hook('theme_registry_alter')]
  public function themeRegistryAlter(array &$theme_registry): void {
    if (isset($theme_registry['menu'])) {
      $theme_registry['menu']['variables']['region'] = NULL;
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for region.html.twig.
   */
  #[Hook('preprocess_region')]
  public function preprocessRegion(array &$variables): void {
    if (isset($variables['elements']['bsi_bund_page_title'])) {
      $has_opener = isset($variables['elements']['bsi_bund_opener'])
        || isset($variables['elements']['bsi_bund_media_opener']);
      if ($has_opener) {
        unset($variables['elements']['bsi_bund_page_title']);
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for block.html.twig.
   */
  #[Hook('preprocess_block')]
  public function preprocessMenuBlock(array &$variables): void {
    $block_id = $variables['elements']['#id'] ?? NULL;
    if ($block_id == NULL || ($variables['base_plugin_id'] ?? '') !== 'system_menu_block') {
      return;
    }

    /** @var \Drupal\block\BlockInterface|null $block */
    $block = \Drupal::entityTypeManager()->getStorage('block')
      ->load($block_id);
    if ($block !== NULL) {
      // Restore default theme and suggestion handling to avoid duplicates.
      // @see MenuLinkTree::build.
      $variables['content']['#theme'] = 'menu';
      $variables['content']['#region'] = $block->getRegion();
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for menu.html.twig.
   */
  #[Hook('theme_suggestions_menu_alter')]
  public function alterMenuThemeSuggestions(&$suggestions, array $variables, string $hook): void {
    if (!isset($variables['region'])) {
      return;
    }
    $suggestions[] = $hook . '__' . $variables['region'];
    $suggestions[] = $hook . '__' . $variables['menu_name'];
    $suggestions[] = $hook . '__' . $variables['menu_name'] . '__' . $variables['region'];
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for block.html.twig.
   */
  #[Hook('theme_suggestions_block_alter')]
  public function alterViewsBlockThemeSuggestions(&$suggestions, array $variables, string $hook): void {
    $plugin_id = $variables['elements']['#base_plugin_id'] ?? $variables['elements']['#plugin_id'];
    if ($plugin_id !== 'views_block') {
      return;
    }
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $variables['elements']['content']['#view'] ?? NULL;

    if (isset($view)) {
      $suggestion = array_pop($suggestions);
      $suggestions[] = $hook . '__' . $plugin_id . '__' . $view->id();
      $suggestions[] = $suggestion;
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for views_view.html.twig.
   */
  #[Hook('theme_suggestions_views_view_alter')]
  public function alterViewsThemeSuggestions(&$suggestions, array $variables, string $hook): void {
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $variables['view'];

    array_pop($suggestions);
    $suggestions[] = $hook . '__' . $view->id();
    $suggestions[] = $hook . '__' . $view->id() . '__' . $view->current_display;
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter() for menu-link-content.html.twig.
   */
  #[Hook('theme_suggestions_menu_link_content_alter')]
  public function alterMenuLinkContentThemeSuggestions(&$suggestions, array $variables, string $hook): void {
    $view_mode = $variables['elements']['#view_mode'];
    $suggestions[] = $hook . '__' . $view_mode;
  }

  /**
   * Add version query param to files in dist folder.
   */
  #[Hook('file_url_alter')]
  public function alterThemeAssetUrls(string &$uri): void {
    $theme_path = $this->themeManager->getActiveTheme()->getPath();
    if (str_starts_with(ltrim($uri, '/'), $theme_path . '/dist/') && file_exists($uri)) {
      $uri .= '?v=' . filemtime($uri);
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for field--paragraph--field-items.html.twig.
   */
  #[Hook('preprocess_field__paragraph__field_items')]
  public function preprocessSequenceItems(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['element']['#object'];
    if ($paragraph->bundle() !== 'sequence' || !$paragraph->hasField('field_display_variant')) {
      return;
    }

    /** @var \Drupal\Core\Field\FieldItemListInterface $items */
    $items = $variables['element']['#items'];
    $year = '';
    $month = '';
    foreach ($items as $delta => $field_item) {
      $entity = $field_item->entity;
      $step = $entity->get('field_step_label')?->value;
      if (
        $step
        && $entity->get('field_section_title')->isEmpty()
        && $entity->get('field_section_text')->isEmpty()
        && $entity->get('field_highlight')->isEmpty()
      ) {
        $variables['items'][$delta]['#title'] = $step;
        $year = $step;
      }
      else {
        // Pre-grouped items for easier rendering.
        if ($step) {
          $month = $step;
          $label = $entity->get('field_step_label')->view($variables['element']['#view_mode']);
          $variables['years'][$year][$month]['title'] = $label;
        }
        $variables['years'][$year][$month][] = &$variables['items'][$delta];
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for field-group-html-element.html.twig.
   */
  #[Hook('preprocess_field_group_html_element')]
  public function preprocessFieldGroup(array &$variables): void {
    $entity_type_id = $variables['element']['#entity_type'] ?? NULL;
    $bundle = $variables['element']['#bundle'] ?? NULL;
    if ($entity_type_id !== 'node' || $bundle !== 'certificate') {
      return;
    }

    $language_manager = \Drupal::languageManager();
    $config_factory = \Drupal::configFactory();

    $langcodes = array_map(static fn (LanguageInterface $language) => $language->getId(), $language_manager->getLanguages());
    foreach (Element::children($variables['element']) as $key) {
      $element = &$variables['element'][$key];
      if (!($element['#items'] ?? NULL)) {
        continue;
      }

      $config_name = $element['#items']->getFieldDefinition()->getConfigDependencyName();
      $config = $config_factory->getEditable($config_name);
      foreach ($langcodes as $langcode) {
        $element['#title_' . $langcode] = $language_manager
          ->getLanguageConfigOverride($langcode, $config_name)
          ->get('label') ?? $config->get('label');
      }
    }
  }

}
