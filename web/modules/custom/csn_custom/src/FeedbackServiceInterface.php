<?php

declare(strict_types=1);

namespace Drupal\csn_custom;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Url;

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

  /**
   * Get the URL for displaying the provided feedback.
   *
   * @param string|\Drupal\Component\Render\MarkupInterface $title
   *   The page title of the feedback page.
   * @param list<string|\Drupal\Component\Render\MarkupInterface>|null $message
   *   The optional message(s) to display.
   * @param array|null $content
   *   Additional render elements to display.
   *
   * @return \Drupal\Core\Url
   *   The url of the feedback page.
   */
  public function getFeedbackUrl(string|MarkupInterface $title, ?array $message = [], ?array $content = []): Url;

}
