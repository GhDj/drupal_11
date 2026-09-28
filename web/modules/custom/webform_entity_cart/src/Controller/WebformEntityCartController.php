<?php

declare(strict_types=1);

namespace Drupal\webform_entity_cart\Controller;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\webform_entity_cart\WebformEntityCartInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Webform Entity Cart routes.
 */
final class WebformEntityCartController extends ControllerBase {

  /**
   * Basket block ID.
   */
  const BASKET_BLOCK_ID = 'bsi_bund_basket';

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly WebformEntityCartInterface $cart,
    private readonly BlockManagerInterface $blockManager,
  ) {}

  /**
   * Builds the response.
   */
  public function __invoke(Request $request, string $operation, string $entity_type_id, mixed $entity_id, ?int $count = NULL): JsonResponse {
    try {
      $entity = $this->entityTypeManager()->getStorage($entity_type_id)
        ->load($entity_id);
    }
    catch (PluginException $e) {
      return (new AjaxResponse([
        'status' => 'error',
        'data' => ['error' => $e->getMessage()],
      ]))
        ->addCommand(new MessageCommand($this->t('Failed to update cart.'), NULL, [
          'type' => 'error',
        ]));
    }

    if ($request->getMethod() === 'POST') {
      $count = (int) $request->request->get(
        'count',
        WebformEntityCartInterface::COUNT_ANY,
      );
    }

    $success = match ($operation) {
      'add' => $this->cart->add($entity, $count),
      'remove' => $this->cart->remove($entity, $count),
      'update' => $this->cart->set($entity, $count),
    };

    Cache::invalidateTags([
      'webform_entity_cart',
    ]);

    $response = (new AjaxResponse());
    if ($success === TRUE) {
      $response->addCommand(new MessageCommand($this->t('Cart was updated.')));
      $response->addCommand(
        new ReplaceCommand(
          '#block-bsi-bund-basket',
          $this->buildCartBlock(),
        )
      );

      return $response;
    }
    return $response;
  }

  /**
   * Help method to rebuild cart.
   */
  protected function buildCartBlock(): array {

    $block = $this->entityTypeManager->getStorage('block')->load(self::BASKET_BLOCK_ID);

    return $this->entityTypeManager
      ->getViewBuilder('block')
      ->view($block);
  }

}
