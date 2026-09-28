<?php

declare(strict_types=1);

namespace Drupal\short_url\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\short_url\ShortUrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Short URL routes.
 */
final class ShortCodeGenerator extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly ShortUrlHelper $shortUrlHelper,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('short_url.helper'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(Request $request): AjaxResponse {
    $short_url = $this->shortUrlHelper->generateUniqueShortCode();
    $element_id = $request->query->get('element', NULL);

    $response = new AjaxResponse();
    if ($element_id === NULL || mb_strlen($element_id) === 0) {
      return $response->setData($short_url);
    }
    $response->addCommand(new InvokeCommand(
      '#' . $element_id,
      'val',
      [$short_url]
    ));

    return $response;
  }

}
