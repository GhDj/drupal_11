<?php

declare(strict_types=1);

namespace Drupal\csn_custom;

/**
 * Stores feedback for the user.
 */
interface FeedbackServiceInterface {

  /**
   * Return feedback information for the given token.
   */
  public function getFeedback(string $token, bool $delete = TRUE): array;

  /**
   * Set data to display on the user's feedback page.
   *
   * @param array{'title': string|\Drupal\Component\Render\MarkupInterface, 'message': string|\Drupal\Component\Render\MarkupInterface, 'content': string|\Drupal\Component\Render\MarkupInterface|array } $data
   *   The data to display on the feedback page.
   *
   * @return string
   *   The token for data lookup.
   */
  public function setFeedback(array $data): string;

}
