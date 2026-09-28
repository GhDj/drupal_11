<?php

declare(strict_types=1);

namespace Drupal\short_url;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\redirect\Entity\Redirect;
use Drupal\redirect\RedirectRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * Helper service for handling short URLs.
 */
final class ShortUrlHelper {

  use StringTranslationTrait;

  /**
   * Characters available for use in short codes, avoids ambiguous characters.
   *
   * @todo Move these properties to configuration.
   */
  private const string CODE_ALPHABET = '123456789abcdefghjkmnpqrstuvwxyz';
  private const int CODE_LENGTH = 5;

  /**
   * The module configuration.
   */
  private ImmutableConfig $config;

  /**
   * Constructs a ShortUrlHelper object.
   */
  public function __construct(
    private readonly RedirectRepository $redirectRepository,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RequestContext $requestContext,
    ConfigFactoryInterface $configFactory,
  ) {
    $this->config = $configFactory->get('short_url.settings');
  }

  /**
   * Generate a unique short code suitable for a prefixed short url.
   */
  public function generateUniqueShortCode(): string {
    do {
      $code = $this->generateShortCode();
    } while (!$this->isAvailable($code));

    return $code;
  }

  /**
   * Determine if the given code is available for use or already taken.
   */
  public function isAvailable(string $code, ?EntityInterface $entity = NULL): bool {
    $short_url = $this->getShortUrlPrefix($entity) . $code;
    /** @var array<\Drupal\redirect\Entity\Redirect> $redirects */
    $redirects = $this->redirectRepository->findBySourcePath($short_url);
    if (count($redirects) === 1) {
      if (!$entity->hasLinkTemplate('canonical')) {
        // This should not happen.
        return FALSE;
      }
      $redirect_url = reset($redirects)->getRedirectUrl();
      $entity_url = $entity->toUrl('canonical');
      return $redirect_url->getRouteName() === $redirect_url->getRouteName()
        && $entity_url->getRouteParameters() == $entity_url->getRouteParameters();
    }

    return $redirects === [];
  }

  /**
   * Generate a random short code with the configured length and alphabet.
   */
  protected function generateShortCode(): string {
    $code = '';
    for ($i = 0; $i < ($length ?? static::CODE_LENGTH); $i++) {
      $code .= static::CODE_ALPHABET[random_int(0, strlen(static::CODE_ALPHABET) - 1)];
    }
    return $code;
  }

  /**
   * Helper to get the short URL prefix.
   *
   * @todo Extend to allow for per-bundle overrides.
   */
  private function getShortUrlPrefix(?EntityInterface $entity = NULL): string {
    return ltrim($this->config->get('prefix'), '/');
  }

  /**
   * Retrieve the entity's short URL, if any.
   */
  public function getEntityShortUrl(EntityInterface $entity): ?Url {
    $info = $this->getShortUrlInfo($entity);
    if ($info['short_url'] === NULL) {
      return NULL;
    }

    return Url::fromUri($info['base_url'] . '/' . $info['prefix'] . $info['code'])
      ->setOption('absolute', TRUE)
      // Provide additional information to consumers.
      ->setOption('short_url_base', $info['base_url'])
      ->setOption('short_url_prefix', $info['prefix'])
      ->setOption('short_url_code', $info['code'])
      ->setOption('short_url', $info['short_url']);
  }

  /**
   * Get global short URL information or for the given entity.
   *
   * @return array<{"base_url": string, "prefix": string, "code": ?string, "short_url": ?string}>
   *   Global information about short urls, or for this entity.
   */
  public function getShortUrlInfo(?EntityInterface $entity = NULL): array {
    $redirect = $entity ? $this->getRedirect($entity) : NULL;
    $prefix = $this->getShortUrlPrefix($entity);
    $code = $redirect ? mb_substr($redirect->getSource()['path'], mb_strlen($prefix)) : NULL;

    // Short URLs are language independent, ensure rendered URL contains the
    // base url without language prefixes.
    $base_url = $this->requestContext->getCompleteBaseUrl();

    return [
      'base_url' => $base_url,
      'prefix' => $prefix,
      'code' => $code,
      'short_url' => $redirect?->getSourceUrl(),
    ];
  }

  /**
   * Get an entity's redirect with a given prefix.
   *
   * @return \Drupal\redirect\Entity\Redirect|null
   *   The newest redirect with the given prefix, if any.
   */
  protected function getRedirect(EntityInterface $entity): ?Redirect {
    if ($entity->isNew()) {
      return NULL;
    }
    $uri = 'internal:/' . $entity->toUrl()->getInternalPath();
    $prefix = $this->config->get('prefix');

    $storage = $this->entityTypeManager->getStorage('redirect');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('redirect_source.path', ltrim($prefix, '/') . '%', 'LIKE')
      ->condition('redirect_redirect.uri', $uri)
      ->condition('enabled', 1)
      ->sort('created', 'DESC')
      ->range(0, 1);
    $entities = $storage->loadMultiple($query->execute());
    return reset($entities) ?: NULL;
  }

  /**
   * Updates an entity's short url.
   */
  public function updateShortUrl(EntityInterface $entity, string $short_code): void {
    $prefix = $this->config->get('prefix');

    $storage = $this->entityTypeManager->getStorage('redirect');
    // Create a dummy redirect entity to utilize Redirect's sanitization.
    /** @var Redirect $new_redirect */
    $new_redirect = $storage->create(['status_code' => Response::HTTP_MOVED_PERMANENTLY]);
    $new_redirect->setRedirect($entity->toUrl('canonical')->getInternalPath());
    $new_redirect->setSource($prefix);

    // @todo Load and update instead of re-creating.
    $query = $storage->getQuery()->accessCheck(TRUE)
      ->condition('redirect_redirect.uri', $new_redirect->getRedirect()['uri'])
      ->condition('redirect_source.path', $new_redirect->getSource()['path'] . '%', 'LIKE')
      ->condition('enabled', 1);
    foreach ($storage->loadMultiple($query->execute()) as $redirect) {
      // @todo Delete, disable or do something else entirely? Also affects
      //   \Drupal\short_url\Form\SettingsForm::submitForm.
      $redirect->delete();
    }

    if (!empty($short_code)) {
      // Set the actual source and finally save.
      $new_redirect->setSource($prefix . $short_code);
      $new_redirect->save();
    }
  }

}
