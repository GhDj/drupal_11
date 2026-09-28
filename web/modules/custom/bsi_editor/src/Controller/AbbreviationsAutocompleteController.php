<?php

namespace Drupal\bsi_editor\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides autocomplete results for abbreviations terms.
 */
class AbbreviationsAutocompleteController extends ControllerBase {

  const int MAX_RESULT_COUNT = 20;

  /**
   * Builds the response.
   *
   * If no search input is provided, returns duplicate abbreviations.
   */
  public function __invoke(?string $input = NULL): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('taxonomy_term');

    $query = $storage->getQuery()
      ->condition('vid', 'abbreviations')
      ->range(0, static::MAX_RESULT_COUNT)
      ->sort('name', 'ASC')
      ->accessCheck(TRUE);
    if ($input !== NULL && strlen($input) > 0) {
      // Match abbreviation (term name) and definition (description).
      // @todo Also add for partial matches
      //   e.g. return "<A>rtificial <I>ntelligence" on input "AI"?
      $query->condition($query->orConditionGroup()
        ->condition('name', $input, '=')
        ->condition('name', '%' . $input . '%', 'LIKE')
        ->condition('description', '%' . $input . '%', 'LIKE')
      );
    }

    $terms = $storage->loadMultiple($query->execute());

    $results = [];
    foreach ($terms as $term) {
      /** @var \Drupal\taxonomy\TermInterface $term */
      $results[$term->label()][] = [
        'abbreviation' => $term->label(),
        'title' => $term->getDescription(),
        'url' => $term->toUrl('canonical')->toString(),
        'uuid' => $term->uuid(),
      ];
    }
    if ($input === NULL) {
      // Filter to only return duplicate abbreviations.
      $results = array_filter($results, static fn (array $result) => count($result) > 1);
    }
    else {
      // Sort: exact matches first, then starts-with, then alphabetically.
      usort($results, static function (array $a, array $b) use ($input): int {
        $aCompare = mb_strtolower($a['abbreviation']);
        $bCompare = mb_strtolower($b['abbreviation']);
        $inputCompare = mb_strtolower($input);
        if ($inputCompare === $aCompare || $inputCompare === $bCompare) {
          return $aCompare === $inputCompare ? -1 : 1;
        }
        if (str_starts_with($aCompare, $inputCompare) !== str_starts_with($bCompare, $inputCompare)) {
          return str_starts_with($aCompare, $inputCompare) ? -1 : 1;
        }
        if ($a['abbreviation'] !== $b['abbreviation']) {
          return $a['abbreviation'] <=> $b['abbreviation'];
        }
        return $a['title'] <=> $b['title'];
      });
    }

    $response = new CacheableJsonResponse($results);
    foreach ($terms as $term) {
      $response->addCacheableDependency($term);
    }
    $response->getCacheableMetadata()
      ->addCacheContexts(['languages:language_interface'])
      ->addCacheTags(['taxonomy_term_list:abbreviations']);

    return $response;
  }

}
