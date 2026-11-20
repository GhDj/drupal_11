<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\csp\Csp;

/**
 * Hooks related to Content Security Policy handling.
 */
class CspHooks {

  /**
   * Implements hook_page_top().
   */
  #[Hook('page_top')]
  public function pageTop(array &$page_top): void {
    // @note Ensure bsi_custom weight is higher than altered modules's.
    if (($page_top['toolbar']['#type'] ?? NULL) === 'toolbar') {
      $page_top['toolbar']['#attached']['csp_nonce']['style'] = [Csp::POLICY_UNSAFE_INLINE];
    }
    if (($page_top['indicator']['#type'] ?? NULL) === 'environment_indicator') {
      // @see environment_indicator_page_top().
      $style = implode('; ', array_filter([
        $page_top['indicator']['#attributes']['style'] ?? '',
        'background-color: ' . $page_top['indicator']['#bg_color'],
        'color: ' . $page_top['indicator']['#fg_color'],
      ]));
      $hash = Csp::calculateHash($style);
      $page_top['indicator']['#attached']['csp']['style-src-attr'] = [Csp::POLICY_UNSAFE_HASHES];
      $page_top['indicator']['#attached']['csp_hash']['style-src-attr'][$hash] = [Csp::POLICY_UNSAFE_INLINE];

      // Switcher styles.
      $hash = Csp::calculateHash('border-top: 1px solid ' . $page_top['indicator']['#fg_color']);
      $page_top['indicator']['#attached']['csp_hash']['style-src-attr'][$hash] = [Csp::POLICY_UNSAFE_INLINE];
    }
  }

}
