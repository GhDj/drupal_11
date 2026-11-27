<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Plugin\Block;

use Drupal\csn_custom\CsnContextChecker;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the CSN branding block (CSN logo only).
 *
 * The regular BSI logo is retained via the main branding block.
 * This block adds the CSN logo on the right side of the header.
 */
#[Block(
  id: 'csn_branding',
  admin_label: new TranslatableMarkup('CSN Branding'),
  category: new TranslatableMarkup('CSN'),
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
      $container->get('config.factory')->get('csn_custom.settings'),
      $container->get('Drupal\csn_custom\CsnContextChecker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf($this->csnContextChecker->isCsnPage())
      ->addCacheableDependency($this->csnContextChecker);
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
    $dashboard_url = NULL;
    if ($dashboard = $this->config->get('dashboard')) {
      $dashboard_url = Url::fromUserInput($dashboard);
    }
    return [
      '#theme' => 'csn_branding',
      '#dashboard_url' => $dashboard_url,
      '#attached' => ['library' => ['csn_custom/csn_branding']],
    ];
  }

}
