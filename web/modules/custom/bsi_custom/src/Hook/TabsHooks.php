<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;
use Drupal\views\Plugin\views\cache\CachePluginBase;
use Drupal\views\ViewExecutable;

/**
 * Hooks related to the frontend tabs.
 */
class TabsHooks {

  /**
   * Transform view result into a tabbed element.
   */
  #[Hook('views_post_render')]
  public function addSecurityFrontTabs(ViewExecutable $view, array &$output, CachePluginBase $cache): void {
    if ($view->id() !== 'security' || $view->current_display !== 'block_front') {
      return;
    }

    $tab = $output;
    $label = $view->display_handler->getOption('title')
      ?: $this->t('BSI IT Security Announcements');

    $output = $this->createHorizontalTabs();
    $output['#children']['bits'] = $this->createTab($tab, $label);
  }

  /**
   * Implements hook_preprocess_HOOK for paragraph.html.twig.
   *
   * Replace field_items with tabs render element.
   */
  #[Hook('preprocess_paragraph')]
  public function processTabsParagraph(array &$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['elements']['#paragraph'];
    if ($paragraph->bundle() !== 'tabs') {
      return;
    }
    $variables['content']['field_items'] = static::createHorizontalTabs();
    foreach (Element::getVisibleChildren($variables['elements']['field_items']) as $key) {
      /** @var \Drupal\paragraphs\Entity\Paragraph $tab */
      $tab = $variables['elements']['field_items'][$key]['#paragraph'];

      $label = $tab->get('field_section_title')->value;
      $more_link = [];
      if (!$tab->get('field_link')->isEmpty()) {
        $more_link = ['#type' => 'more_link'] + $tab->get('field_link')->view('default')[0];
      }

      // Do not render these fields twice.
      $tab
        ->set('field_section_title', NULL)
        ->set('field_link', NULL);
      $variables['content']['field_items']['#children'][$key] = static::createTab(
        [
          'content' => $variables['elements']['field_items'][$key],
          'more' => $more_link,
        ],
        $label,
      );
    }
  }

  /**
   * Get a render array for the tabs container.
   */
  public static function createHorizontalTabs(): array {
    return [
      '#theme' => 'horizontal_tabs',
      '#children' => [],
      '#attached' => [
        'library' => [
          'field_group/element.horizontal_tabs',
        ],
      ],
    ];
  }

  /**
   * Get a render array for a single tab.
   *
   * @param array $build
   *   The tab content.
   * @param mixed $label
   *   The tab title.
   *
   * @return array
   *   The render array for the tab, suitable for $tabs['#children'][].
   */
  public static function createTab(array $build, mixed $label): array {
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $transformed = \Drupal::transliteration()->transliterate($label, $langcode);

    return [
      '#type' => 'details',
      '#title' => $label,
      '#attributes' => [
        'id' => Html::getUniqueId($transformed),
      ],
      'elements' => $build,
    ];
  }

}
