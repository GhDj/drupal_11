<?php

namespace Drupal\bsi_search\Plugin\search_api\processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds a computed "year" field to the Search API index.
 */
#[SearchApiProcessor(
  id: "year_field",
  label: new TranslatableMarkup("Year field"),
  description: new TranslatableMarkup("Extracts the year from entity created date (Node and Media)."),
  stages: [
    "add_properties" => 0,
    "pre_index_save" => 0,
  ]
)]
class YearField extends ProcessorPluginBase {

  /**
   * Cutoff threshold.
   *
   * We start aggregating all year options into a single option
   * if the current year is before this threshold.
   *
   * Calculation: option year < (current_year - cutoff_threshold).
   */
  const CUTOFF_YEAR = 6;

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL) {
    $properties = [];

    $definition = new ProcessorProperty([
      'label' => $this->t('Year (Node and Media only)'),
      'description' => $this->t('Year extracted from entity created time'),
      'type' => 'string',
      'processor_id' => $this->getPluginId(),
    ]);

    $properties['field_year'] = $definition;

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $entity = $item->getOriginalObject()->getEntity();

    if (!$entity) {
      return;
    }

    $timestamp = NULL;

    // Get created time for the entity.
    // Both Node and Media entities have getCreatedTime() method defined.
    // If we need to expand this to other entities we might need to revisit
    // this.
    if ($entity instanceof NodeInterface || ($entity instanceof MediaInterface)) {
      $timestamp = $entity->getCreatedTime();
    }

    if (!$timestamp) {
      return;
    }

    $year = (int) date('Y', $timestamp);
    $current_year = (int) date('Y');
    $cutoff_year = $current_year - self::CUTOFF_YEAR;

    $year_value_to_index = ($year < $cutoff_year) ? "$cutoff_year and older" : $year;

    foreach ($item->getFields() as $field) {
      if ($field->getPropertyPath() === 'field_year') {
        $field->addValue($year_value_to_index);
      }
    }
  }

}
