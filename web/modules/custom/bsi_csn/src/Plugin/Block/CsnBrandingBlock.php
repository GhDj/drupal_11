<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Plugin\Block;

use Drupal\bsi_csn\CsnContextChecker;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the CSN branding block (CSN logo only).
 *
 * The regular BSI logo is retained via the main branding block.
 * This block adds the CSN logo on the right side of the header.
 */
#[Block(
  id: 'bsi_csn_branding',
  admin_label: new TranslatableMarkup('CSN Branding'),
  category: new TranslatableMarkup('BSI'),
)]
final class CsnBrandingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    mixed $plugin_definition,
    private readonly ImmutableConfig $config,
    private readonly CsnContextChecker $csnContextChecker,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')->get('bsi_csn.settings'),
      $container->get('bsi_csn.context_checker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf($this->csnContextChecker->isCsnPage())
      ->addCacheContexts(['route']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return array_merge(parent::getCacheContexts(), ['route']);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'csn_branding',
      '#csn_dashboard_url' => $this->config->get('csn_dashboard_url') ?: '',
      '#cache' => [
        'tags' => $this->config->getCacheTags(),
      ],
    ];
  }

}
