<?php

namespace Drupal\webform_entity_cart\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\webform_entity_cart\WebformEntityCartInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Render cart block.
 *
 * Only renders block if cart has items.
 *
 * @Block(
 *   id = "webform_entity_cart_basket_block",
 *   admin_label = @Translation("Basket")
 * )
 */
class BasketBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    protected WebformEntityCartInterface $cart,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritDoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('webform_entity_cart.cookie')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function build(): array {
    $count = 0;

    foreach ($this->cart->getCart() as $items) {
      $count += array_sum($items);
    }

    return [
      '#type' => 'container',
      '#count' => $count,
      '#url' => Url::fromUserInput('/cart'),
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags(): array {
    return [
      'webform_entity_cart',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts(): array {
    return [
      'session',
    ];
  }

}
