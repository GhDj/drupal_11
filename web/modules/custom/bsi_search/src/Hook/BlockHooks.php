<?php

namespace Drupal\bsi_search\Hook;

use Drupal\book\BookInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Block hooks for the BSI Search module.
 */
class BlockHooks {

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
