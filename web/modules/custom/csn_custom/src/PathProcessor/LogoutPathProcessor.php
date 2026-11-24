<?php

declare(strict_types=1);

namespace Drupal\csn_custom\PathProcessor;

use Drupal\csn_custom\CsnContextChecker;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Path processor to change CSN logout target.
 */
final class LogoutPathProcessor implements OutboundPathProcessorInterface {

  /**
   * The csn_custom.settings configuration.
   */
  private ImmutableConfig $config;

  /**
   * Constructs a LogoutPathProcessor object.
   */
  public function __construct(
    private readonly CsnContextChecker $contextChecker,
    ConfigFactoryInterface $configFactory,
  ) {
    $this->config = $configFactory->get('csn_custom.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], ?Request $request = NULL, ?BubbleableMetadata $bubbleable_metadata = NULL): string {
    if (($options['route_name'] ?? NULL) !== 'user.logout' || isset($options['query']['destination'])) {
      return $path;
    }

    $bubbleable_metadata->addCacheableDependency($this->contextChecker);
    if ($this->contextChecker->isCsnPage()) {
      $bubbleable_metadata->addCacheableDependency($this->config);

      $logout_redirect = $this->config->get('logout_redirect');
      if (!empty($logout_redirect)) {
        $redirect_url = Url::fromUserInput($logout_redirect);
        $options['query']['destination'] = '/' . $redirect_url->getInternalPath();
      }
    }
    return $path;
  }

}
