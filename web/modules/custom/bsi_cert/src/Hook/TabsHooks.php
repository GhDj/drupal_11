<?php

declare(strict_types=1);

namespace Drupal\bsi_cert\Hook;

use Drupal\bsi_cert\Service\BsiCertFeedClient;
use Drupal\bsi_cert\Service\BsiCertTeaserBuilder;
use Drupal\bsi_custom\Hook\TabsHooks as CustomTabsHooks;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\Plugin\views\cache\CachePluginBase;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Hooks related to the frontend tabs.
 */
class TabsHooks {

  use StringTranslationTrait;

  public function __construct(
    #[Autowire(service: 'bsi_cert.teaser_builder')]
    private readonly BsiCertTeaserBuilder $certBuilder,
  ) {}

  /**
   * Transform view result into a tabbed element.
   */
  #[Hook('views_post_render')]
  public function addCertFrontTabs(ViewExecutable $view, array &$output, CachePluginBase $cache): void {
    if ($view->id() !== 'security' || $view->current_display !== 'block_front') {
      return;
    }

    $output['#children']['cert_federal'] = CustomTabsHooks::createTab(
      $this->certBuilder->build(BsiCertFeedClient::TYPE_FEDERAL),
      $this->certBuilder->getSourceLabel(BsiCertFeedClient::TYPE_FEDERAL),
    );
    $output['#children']['cert_citizen'] = CustomTabsHooks::createTab(
      $this->certBuilder->build(BsiCertFeedClient::TYPE_CITIZEN),
      $this->certBuilder->getSourceLabel(BsiCertFeedClient::TYPE_CITIZEN),
    );
  }

}
