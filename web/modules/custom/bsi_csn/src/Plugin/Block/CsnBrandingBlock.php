<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Plugin\Block;

use Drupal\bsi_csn\CsnContextChecker;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the CSN branding block.
 */
#[Block(
  id: 'bsi_csn_branding',
  admin_label: new TranslatableMarkup('CSN Branding'),
  category: new TranslatableMarkup('BSI'),
)]
final class CsnBrandingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ThemeHandlerInterface $themeHandler,
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
      $container->get('config.factory'),
      $container->get('theme_handler'),
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
  public function build(): array {
    $config = $this->configFactory->get('bsi_csn.settings');
    $theme_path = $this->themeHandler->getTheme('bsi_bund')?->getPath() ?? '';

    return [
      '#theme' => 'csn_branding',
      '#bsi_logo' => '/' . $theme_path . '/logo.svg',
      '#bsi_logo_mobile' => '/' . $theme_path . '/logo_mobile.svg',
      '#bsi_url' => $config->get('bsi_url') ?: '',
      '#csn_dashboard_url' => $config->get('csn_dashboard_url') ?: '',
      '#cache' => [
        'tags' => $config->getCacheTags(),
      ],
    ];
  }

}
