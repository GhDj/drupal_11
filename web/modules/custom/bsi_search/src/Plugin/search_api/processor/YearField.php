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

    if ($entity instanceof MediaInterface) {

      // Fallback.
      $timestamp = $entity->getCreatedTime();

      $translated_media = $entity;

      $langcode = $item->getLanguage();

      if ($langcode && $entity->hasTranslation($langcode)) {
        $translated_media = $entity->getTranslation($langcode);
      }

      if (
        $translated_media->hasField('field_publication_date')
        && !$translated_media->get('field_publication_date')->isEmpty()
      ) {
        $timestamp = $translated_media
          ->get('field_publication_date')
          ->date
          ->getTimestamp();
      }
    }
    elseif ($entity instanceof NodeInterface) {
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
