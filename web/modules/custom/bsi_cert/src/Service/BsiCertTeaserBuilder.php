<?php

namespace Drupal\bsi_cert\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Builds the render array for a CERT teaser list.
 *
 * Shared by the block plugin and by the tab integration in bsi_custom, so the
 * teaser markup is produced in exactly one place. Data comes exclusively from
 * the stored feed items (populated by cron); no network request is made here.
 */
class BsiCertTeaserBuilder {

  use StringTranslationTrait;

  /**
   * The module configuration.
   */
  protected ImmutableConfig $config;

  public function __construct(
    #[Autowire(service: 'bsi_cert.feed_client')]
    protected readonly BsiCertFeedClient $feedClient,
    protected readonly DateFormatterInterface $dateFormatter,
    ConfigFactoryInterface $config_factory,
  ) {
    $this->config = $config_factory->get('bsi_cert.settings');
  }

  /**
   * Retrieve the configured label for the provided source.
   */
  public function getSourceLabel(string $source): string|TranslatableMarkup {
    return $this->config->get('sources.' . $source . '.label') ?? $source;
  }

  /**
   * Builds the teaser list render array for a source.
   *
   * @param string $source
   *   Source machine name (one of BsiCertFeedClient::TYPE_*).
   *
   * @return array
   *   A render array using the 'bsi_cert_teaserlist' theme hook.
   */
  public function build(string $source): array {
    $items = [];
    foreach ($this->feedClient->getStored($source) as $item) {
      $parsed = $this->parseTitle($item['title']);

      $items[] = [
        'title' => $parsed['title'],
        'tag' => $parsed['tag'],
        'severity' => $parsed['severity'],
        // @todo If urls are relative, add base url.
        'url' => Url::fromUri($item['link']),
        'attributes' => [
          'target' => '_blank',
          'rel' => 'noopener noreferrer',
        ],
        'timestamp' => $item['date_ts'] ?? NULL,
      ];
    }

    if (
      ($link_text = $this->config->get('sources.' . $source . '.cta_text'))
      && ($link_uri = $this->config->get('sources.' . $source . '.cta_url'))
    ) {
      $more_link = Link::fromTextAndUrl($link_text, Url::fromUri($link_uri))->toRenderable();
      $more_link['#type'] = 'more_link';
    }

    return [
      '#theme' => 'bsi_cert_teaserlist',
      '#label' => $this->getSourceLabel($source),
      '#items' => $items,

      '#source' => $source,
      '#more_link' => $more_link ?? NULL,
      '#cache' => [
        // The feed cache tag bubbles up to whatever embeds this render array
        // (block, tabs view). Cron invalidates it when new data arrives, which
        // is the only thing that rebuilds this output. Until then it is cached
        // permanently, so the block never re-evaluates on a page request.
        'tags' => [BsiCertFeedClient::cacheTag($source)],
        'max-age' => Cache::PERMANENT,
      ],
    ];
  }

  /**
   * Splits a raw feed title into tag, severity and the plain title.
   *
   * @param string $raw_title
   *   The unmodified title from the feed item.
   *
   * @return array
   *   Associative array with 'tag', 'severity' and 'title' keys. 'tag' and
   *   'severity' are NULL if the title didn't match the expected pattern.
   */
  protected function parseTitle(string $raw_title): array {
    $pattern = '/^\[(NEU|UPDATE)]\s*\[(niedrig|mittel|hoch|kritisch)]\s*(.+)$/ui';

    if (preg_match($pattern, $raw_title, $matches)) {
      return [
        'tag' => mb_strtoupper($matches[1]),
        'severity' => mb_ucfirst($matches[2]),
        'title' => trim($matches[3]),
      ];
    }

    return [
      'tag' => NULL,
      'severity' => NULL,
      'title' => trim($raw_title),
    ];
  }

}
