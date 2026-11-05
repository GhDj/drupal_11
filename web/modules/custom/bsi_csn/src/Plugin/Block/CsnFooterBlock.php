<?php

declare(strict_types=1);

namespace Drupal\bsi_csn\Plugin\Block;

use Drupal\bsi_csn\CsnContextChecker;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the CSN footer block.
 */
#[Block(
  id: 'bsi_csn_footer',
  admin_label: new TranslatableMarkup('CSN Footer'),
  category: new TranslatableMarkup('BSI'),
)]
final class CsnFooterBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly MenuLinkTreeInterface $menuLinkTree,
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
      $container->get('menu.link_tree'),
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

    // Load the footer menu (utility links).
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(1);
    $tree = $this->menuLinkTree->load('footer', $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $this->menuLinkTree->transform($tree, $manipulators);

    $utility_links = [];
    foreach ($tree as $element) {
      if ($element->access->isAllowed()) {
        $link = $element->link;
        $utility_links[] = [
          'title' => $link->getTitle(),
          'url' => $link->getUrlObject(),
        ];
      }
    }

    return [
      '#theme' => 'csn_footer',
      '#copyright_text' => $config->get('copyright_text') ?: '',
      '#utility_links' => $utility_links,
      '#back_to_top_url' => Url::fromUri('route:<none>', ['fragment' => 'main-content']),
      '#back_to_top_title' => $this->t('To top'),
      '#cache' => [
        'tags' => array_merge(
          $config->getCacheTags(),
          ['config:system.menu.footer'],
        ),
      ],
    ];
  }

}
