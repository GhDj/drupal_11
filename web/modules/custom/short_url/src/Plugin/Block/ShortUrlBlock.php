<?php

declare(strict_types=1);

namespace Drupal\short_url\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\short_url\Plugin\Derivative\ShortUrlBlockDeriver;
use Drupal\short_url\ShortUrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a short url block.
 */
#[Block(
  id: 'short_url',
  admin_label: new TranslatableMarkup('Short URL'),
  category: new TranslatableMarkup('Content'),
  deriver: ShortUrlBlockDeriver::class
)]
final class ShortUrlBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ShortUrlHelper $shortUrlHelper,
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
      $container->get('short_url.helper'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $entity = $this->getContextValue('entity');
    if (!$entity || !($url = $this->shortUrlHelper->getEntityShortUrl($entity))) {
      return [];
    }
    return [
      'short_url' => [
        '#theme' => 'link__short_url',
        '#url' => $url,
      ],
    ];
  }

}
