<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Hook;

use Drupal\bsi_csn\CsnContextChecker;
use Drupal\block\BlockInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Block-related hooks for the CSN module.
 */
class BlockHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly CsnContextChecker $csnContextChecker,
  ) {}

  /**
   * Hides regular header/footer blocks on CSN pages.
   */
  #[Hook('block_access')]
  public function blockAccess(BlockInterface $block, string $operation, $account): AccessResult {
    if ($operation !== 'view') {
      return AccessResult::neutral();
    }

    // Blocks to hide on CSN pages (replaced by CSN header/footer).
    $hidden_plugins = [
      // Header: replaced by CSN branding.
      'system_branding_block',
      // Footer/Legal: replaced by CSN footer block.
      'config_pages_field',
      'system_menu_block:main',
      'menu_item_fields:social-media',
    ];

    $plugin_id = $block->getPluginId();
    $region = $block->getRegion();

    // Only hide config_pages_field blocks in footer/legal regions.
    if ($plugin_id === 'config_pages_field' && !in_array($region, ['footer', 'legal'])) {
      return AccessResult::neutral();
    }

    // Only hide system_menu_block:main in footer region (quick navigation).
    if ($plugin_id === 'system_menu_block:main' && $region !== 'footer') {
      return AccessResult::neutral();
    }

    if (in_array($plugin_id, $hidden_plugins)) {
      if ($this->csnContextChecker->isCsnPage()) {
        return AccessResult::forbidden('CSN pages use CSN-specific header and footer.')
          ->addCacheContexts(['route']);
      }
    }

    return AccessResult::neutral();
  }

  /**
   * Adds an info note to CSN block forms about code-managed visibility.
   */
  #[Hook('form_block_form_alter')]
  public function formBlockFormAlter(array &$form, FormStateInterface $form_state): void {
    $block = $form_state->getFormObject()->getEntity();
    if (in_array($block->getPluginId(), ['bsi_csn_branding', 'bsi_csn_footer'])) {
      $form['visibility_note'] = [
        '#type' => 'item',
        '#title' => $this->t('Note'),
        '#markup' => $this->t('This block is automatically shown only on CSN group content pages. Visibility is managed in code via blockAccess().'),
        '#weight' => 80,
      ];
    }
  }

}
