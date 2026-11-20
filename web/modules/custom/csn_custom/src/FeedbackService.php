<?php

declare(strict_types=1);

namespace Drupal\csn_custom;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\TempStore\PrivateTempStore;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * {@inheritDoc}
 */
final class FeedbackService implements FeedbackServiceInterface {

  /**
   * The temp store collection holding feedback data.
   */
  private PrivateTempStore $tempStore;

  /**
   * Constructs a FeedbackService object.
   */
  public function __construct(
    PrivateTempStoreFactory $tempStoreFactory,
  ) {
    $this->tempStore = $tempStoreFactory->get('csn_feedback');
  }

  /**
   * {@inheritDoc}
   */
  public function getFeedback(string $token, bool $delete = TRUE): array {
    if (!$data = $this->tempStore->get($token)) {
      throw new NotFoundHttpException();
    }
    if ($delete) {
      $this->tempStore->delete($token);
    }

    return $data;
  }

  /**
   * {@inheritDoc}
   */
  public function setFeedback(array $data): string {
    $token = Crypt::randomBytesBase64(64);
    $this->tempStore->set($token, $data);

    return $token;
  }

}
