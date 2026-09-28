<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a to-top link in a block.
 */
#[Block(
  id: 'bsi_custom_to_top',
  admin_label: new TranslatableMarkup('To-Top'),
  category: new TranslatableMarkup('BSI'),
)]
final class ToTopBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['link'] = [
      '#type' => 'link',
      '#url' => Url::fromUri('route:<none>', [
        'fragment' => 'main-content',
      ]),
      '#title' => $this->t('To top'),
    ];

    return $build;
  }

}
