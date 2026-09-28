<?php

declare(strict_types=1);

namespace Drupal\bsi_social\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a mastodon integration block.
 */
#[Block(
  id: 'bsi_social_mastodon',
  admin_label: new TranslatableMarkup('Social Media: @service', ['@service' => 'Mastodon']),
  category: new TranslatableMarkup('BSI'),
)]
final class MastodonIntegrationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The bsi_social.settings configuration.
   */
  private ImmutableConfig $config;

  /**
   * Total number of posts to get.
   */
  private const LIMIT = 3;

  /**
   * Cache lifetime in seconds.
   */
  private const CACHE_EXPIRE = 300;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected ClientInterface $httpClient,
    protected ConfigFactoryInterface $config_factory,
    protected CacheBackendInterface $cache,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $config_factory->get('bsi_social.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('config.factory'),
      $container->get('cache.default'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    $endpoint = $this->config->get('mastodon_endpoint');
    $cache_expire = $this->config->get('cache_expire');

    $cid = sprintf(
      'bsi_social:mastodon:%s:%d',
      md5($endpoint),
      self::LIMIT,
    );

    if ($cache = $this->cache->get($cid)) {
      $items = $cache->data;
    }
    else {
      $response = $this->httpClient->request('GET', $endpoint . '?limit=' . self::LIMIT, []);

      if ($response->getStatusCode() !== 200) {
        return [];
      }

      $body = (string) $response->getBody();
      $items = json_decode($body, TRUE);

      $this->cache->set(
        $cid,
        $items,
        time() + $cache_expire,
      );
    }

    return [
      'posts' => [
        '#theme' => 'mastodon_posts',
        '#items' => $items,
      ],
      '#cache' => [
        'tags' => ['config:bsi_social.settings'],
        'max-age' => $cache_expire,
      ],
    ];

  }

}
