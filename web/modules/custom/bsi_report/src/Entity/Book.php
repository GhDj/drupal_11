<?php

namespace Drupal\bsi_report\Entity;

use Drupal\book\Entity\Node\Book as ContribBook;
use Drupal\Core\Url;

/**
 * Defines a custom Node Type class with an additional custom property.
 */
class Book extends ContribBook {

  /**
   * {@inheritDoc}
   */
  public function toUrl($rel = NULL, array $options = []): Url {
    if (($rel ?? 'canonical') === 'canonical' && ($link_uri = $this->getBook()['link_uri'] ?? NULL)) {
      return Url::fromUri($link_uri, $options)
        ->setRouteParameter($this->getEntityTypeId(), $this->id());
    }
    return parent::toUrl($rel, $options);
  }

}
