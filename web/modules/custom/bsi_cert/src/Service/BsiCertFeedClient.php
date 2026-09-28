<?php

namespace Drupal\bsi_cert\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\State\StateInterface;
use GuzzleHttp\ClientInterface;

/**
 * Fetches, parses and stores CERT advisories per source.
 *
 * Fetching (refresh()) happens exclusively from cron. The frontend reads the
 * stored result (getStored()) and never performs a network request, so a slow
 * or unreachable feed can no longer block a page render.
 */
class BsiCertFeedClient {

  public const string TYPE_CITIZEN = 'citizen';
  public const string TYPE_FEDERAL = 'federal';

  const string TAG_PREFIX = 'bsi_cert:feed:';
  const string STATE_PREFIX = 'bsi_cert.items.';
  const string STATE_TIME_PREFIX = 'bsi_cert.fetched.';

  /**
   * The bsi_cert.settings configuration.
   */
  private ImmutableConfig $config;

  /**
   * The bsi_cert logger.
   */
  private LoggerChannelInterface $logger;

  public function __construct(
    protected ClientInterface $httpClient,
    protected StateInterface $state,
    protected TimeInterface $time,
    LoggerChannelFactoryInterface $loggerFactory,
    ConfigFactoryInterface $config_factory,
  ) {
    $this->config = $config_factory->get('bsi_cert.settings');
    $this->logger = $loggerFactory->get('bsi_cert');
  }

  /**
   * Returns the cache tag for a given source.
   */
  public static function cacheTag(string $source): string {
    return self::TAG_PREFIX . $source;
  }

  /**
   * Fetches a source from the network and persists the result.
   *
   * This is the only method that performs a HTTP request. It is meant to be
   * called from cron. On failure the previously stored data is left untouched,
   * so the frontend keeps showing the last successful result.
   *
   * @param string $source
   *   Source machine name (one of the TYPE_* constants).
   *
   * @return bool
   *   TRUE if the feed was fetched and stored, FALSE on error or missing URL.
   */
  public function refresh(string $source): bool {
    $url = $this->config->get('sources.' . $source . '.source_url');
    if (!$url) {
      return FALSE;
    }

    try {
      $response = $this->httpClient->request('GET', $url, [
        'timeout' => (int) $this->config->get('request_timeout'),
        'headers' => ['Accept' => '*/*'],
      ]);
      $body = (string) $response->getBody();

      $items = $this->config->get('sources.' . $source . '.source_format') === 'json'
        ? $this->parseJson($body)
        : $this->parseRss($body);

      // Store the full parsed list; slicing to teaser_count happens at read
      // time so a changed count takes effect without a re-fetch.
      $previous = $this->state->get(self::STATE_PREFIX . $source, []);
      $this->state->set(self::STATE_PREFIX . $source, $items);
      $this->state->set(self::STATE_TIME_PREFIX . $source, (int) $this->time->getRequestTime());

      // Only rebuild the rendered block when the data actually changed.
      if ($items !== $previous) {
        Cache::invalidateTags([self::cacheTag($source)]);
      }

      return TRUE;
    }
    catch (\Throwable $e) {
      $this->logger->error('Could not load CERT feed "@source": @msg', [
        '@source' => $source,
        '@msg' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

  /**
   * Returns the stored teaser items for a source. Never hits the network.
   *
   * @param string $source
   *   Source machine name (one of the TYPE_* constants).
   *
   * @return array[]
   *   List of ['date_ts' => ?int, 'title' => string, 'link' => string],
   *   sliced to the configured teaser_count.
   */
  public function getStored(string $source): array {
    $items = $this->state->get(self::STATE_PREFIX . $source, []);
    $count = (int) ($this->config->get('sources.' . $source . '.teaser_count') ?: 9);
    return array_slice($items, 0, $count);
  }

  /**
   * Backwards-compatible alias.
   *
   * Kept so existing callers keep working; it now returns stored data only and
   * never triggers a fetch. Extra arguments (e.g. the former $refresh flag) are
   * ignored.
   */
  public function getTeasers(string $source): array {
    return $this->getStored($source);
  }

  /**
   * Timestamp of the last successful fetch for a source, if any.
   */
  public function getLastUpdated(string $source): ?int {
    $ts = $this->state->get(self::STATE_TIME_PREFIX . $source);
    return $ts !== NULL ? (int) $ts : NULL;
  }

  /**
   * Parses an RSS 2.0 / Atom feed.
   */
  protected function parseRss(string $xml): array {
    $items = [];
    $previous = libxml_use_internal_errors(TRUE);
    $doc = simplexml_load_string($xml);
    libxml_use_internal_errors($previous);
    if ($doc === FALSE) {
      return [];
    }

    $nodes = $doc->channel->item ?? $doc->entry ?? [];
    foreach ($nodes as $node) {
      $title = trim((string) $node->title);
      // RSS 2.0 carries the URL as element text; Atom uses the href attribute.
      $link = (string) ($node->link['href'] ?? $node->link);
      $rawDate = (string) ($node->pubDate ?? $node->published ?? $node->updated ?? '');
      $timestamp = $rawDate !== '' ? strtotime($rawDate) : NULL;
      if ($title === '' || $link === '') {
        continue;
      }
      $items[] = ['date_ts' => $timestamp ?: NULL, 'title' => $title, 'link' => $link];
    }

    // Newest first.
    usort($items, fn($a, $b) => ($b['date_ts'] ?? 0) <=> ($a['date_ts'] ?? 0));
    return $items;
  }

  /**
   * Parses a JSON feed. Adjust the field names to the actual API if used.
   */
  protected function parseJson(string $json): array {
    $data = json_decode($json, TRUE);
    if (!is_array($data)) {
      return [];
    }

    $rows = $data['items'] ?? $data['data'] ?? $data;
    $items = [];
    foreach ($rows as $row) {
      if (!is_array($row)) {
        continue;
      }
      $title = (string) ($row['title'] ?? $row['name'] ?? '');
      $link = (string) ($row['link'] ?? $row['url'] ?? '');
      $rawDate = (string) ($row['published'] ?? $row['date'] ?? $row['pubDate'] ?? '');
      $timestamp = $rawDate !== '' ? strtotime($rawDate) : NULL;
      if ($title === '' || $link === '') {
        continue;
      }
      $items[] = ['date_ts' => $timestamp ?: NULL, 'title' => $title, 'link' => $link];
    }

    usort($items, fn($a, $b) => ($b['date_ts'] ?? 0) <=> ($a['date_ts'] ?? 0));
    return $items;
  }

}
