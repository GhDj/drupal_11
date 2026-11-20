<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Controller;

use Drupal\csn_custom\FeedbackServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides feedback responses to various actions in the Cyber Security Network.
 */
final class FeedbackPageController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    #[Autowire(service: 'csn_custom.feedback')]
    private readonly FeedbackServiceInterface $feedback,
  ) {}

  /**
   * Builds the response.
   */
  public function __invoke(string $token): array {
    if (!$data = $this->feedback->getFeedback($token)) {
      throw new NotFoundHttpException();
    }

    return [
      '#theme' => 'csn_feedback',
      '#title' => $data['title'],
      '#message' => $data['message'],
      '#content' => $data['content'] ?? NULL,
    ];
  }

}
