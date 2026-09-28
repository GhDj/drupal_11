<?php

namespace Drupal\bsi_report;

use Drupal\book\BookManagerInterface;
use Drupal\book\BookOutline;

/**
 * Decorates the book.outline service.
 */
class BookOutlineDecorator extends BookOutline {

  protected const int ADJACENT_PREVIOUS = -1;
  protected const int ADJACENT_NEXT = 1;

  /**
   * Constructor.
   */
  public function __construct(
    protected readonly BookOutline $inner,
    protected BookManagerInterface $bookManager,
  ) {
    parent::__construct($bookManager);
  }

  /**
   * Pass non-decorated method calls along to original service.
   */
  public function __call(string $method, array $arguments) {
    return $this->inner->$method(...$arguments);
  }

  /**
   * {@inheritDoc}
   */
  public function prevLink(array $book_link): ?array {
    $link = $this->getAdjacentLink($book_link, static::ADJACENT_PREVIOUS);
    if ($link !== NULL && $link['nid'] === $book_link['bid']) {
      // Exclude the book cover page.
      return NULL;
    }
    return $link;
  }

  /**
   * {@inheritDoc}
   */
  public function nextLink(array $book_link): ?array {
    return $this->getAdjacentLink($book_link, static::ADJACENT_NEXT);
  }

  /**
   * Get the adjacent link in a given direction, if any.
   *
   * @param array $book_link
   *   The book link record.
   * @param int $offset
   *   Offset from the provided link, see ADJACENT_* constants.
   */
  protected function getAdjacentLink(array $book_link, int $offset): ?array {
    // If the parent is zero, we are at the start of a book.
    if ($book_link['pid'] == 0) {
      return NULL;
    }

    $link = $this->getBookTreeAdjacentLink($book_link, $offset);
    if ($link !== NULL) {
      return $this->bookManager->bookLinkTranslate($link);
    }
    return NULL;
  }

  /**
   * Get the adjacent link based on the flattened book tree.
   */
  protected function getBookTreeAdjacentLink(array $book_link, int $offset): ?array {
    $flat = array_values($this->getFilteredFlatBookTree($book_link));
    $index = array_find_key($flat, static fn(array $link) => $link['nid'] === $book_link['nid']);
    $slice = array_slice($flat, $index + $offset, 1);
    return $slice ? reset($slice) : NULL;
  }

  /**
   * Get list of book links that are actual links.
   *
   * @return list<array>
   *   The flat book tree excluding unlinked elements.
   *
   * @todo This could probably use some performance optimization.
   */
  protected function getFilteredFlatBookTree($book_link): array {
    // Get the full tree, as desired item may be in another hierarchy branch.
    $flat = $this->bookManager->bookTreeGetFlat([
      'nid' => 0,
      'depth' => 9,
    ] + $book_link);
    return array_filter(
      $flat,
      static fn(array $link) => $link['link_uri'] !== 'route:<nolink>',
    );
  }

}
