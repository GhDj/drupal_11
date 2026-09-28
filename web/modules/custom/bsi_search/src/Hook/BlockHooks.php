<?php

namespace Drupal\bsi_search\Hook;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Block hooks for the BSI Search module.
 */
class BlockHooks {

  /**
   * Block display IDs for yearly reports.
   */
  const array SEARCH_YEARLY_REPORTS_VIEW_BLOCKS = [
    'views_block:search_yearly_report-block_search_yearly_reports',
    'views_exposed_filter_block:search_yearly_report-block_search_yearly_reports',
  ];

  /**
   * Add conditional logic.
   *
   * The yearly results view blocks should only be added to
   * nodes that are linked to a book.
   */
  #[Hook('plugin_filter_block__layout_builder_alter')]
  public function pluginFilterlayoutBuilderAlter(array &$definitions, array $extra): void {
    if (!isset($extra['section_storage']) || !($extra['section_storage'] instanceof SectionStorageInterface)) {
      return;
    }

    $entity = $extra['section_storage']->getContextValue('entity');
    if (!isset($entity) || !($entity instanceof EntityInterface)) {
      return;
    }

    /** @var \Drupal\book\BookManagerInterface $book_manager */
    $book_manager = \Drupal::service('book.manager');
    $book_data = $book_manager->loadBookLink($entity->id());

    if (empty($book_data)) {
      foreach (self::SEARCH_YEARLY_REPORTS_VIEW_BLOCKS as $block_id) {
        if (isset($definitions[$block_id])) {
          unset($definitions[$block_id]);
        }
      }
    }
  }

  /**
   * Implements block_view_BASE_ID_alter() for block_search_topic.
   */
  #[Hook('block_view_block_search_topic_alter')]
  public function alterSearchTopicBlock(array &$build, BlockPluginInterface &$plugin): void {
    /** @var \Drupal\block\BlockInterface $block */
    $block = $build['#block'];
    if ($block->id() === 'bsi_bund_site_search') {
      $build['#pre_render'][] = [$plugin, 'preRenderHeaderSearchBlock'];
    }
    else {
      $build['#pre_render'][] = [$plugin, 'preRenderCustomSearchBlock'];
    }
  }

}
