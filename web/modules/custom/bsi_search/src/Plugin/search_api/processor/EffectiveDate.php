<?php

namespace Drupal\bsi_search\Plugin\search_api\processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Adds an effective date field to the index.
 */
#[SearchApiProcessor(
  id: "effective_date",
  label: new TranslatableMarkup("Effective date"),
  description: new TranslatableMarkup("Publication date, media created date, or node created date."),
  stages: [
    "add_properties" => 0,
  ]
)]
class EffectiveDate extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    $properties = [];

    if (!$datasource) {
      $properties['effective_date'] = new ProcessorProperty([
        'label' => $this->t('Effective date'),
        'description' => $this->t('Publication date, media created date or node created date'),
        'type' => 'date',
        'processor_id' => $this->getPluginId(),
      ]);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $entity = $item->getOriginalObject()->getValue();

    if (!$entity instanceof NodeInterface) {
      return;
    }

    $timestamp = $entity->getCreatedTime();

    if (
      $entity->hasField('field_media')
      && !$entity->get('field_media')->isEmpty()
    ) {

      $media = $entity->get('field_media')->entity;

      if ($media instanceof MediaInterface) {

        // Fallback to media created date.
        $timestamp = $media->getCreatedTime();

        // Get the language currently being indexed.
        $langcode = $item->getLanguage();

        if (
          $langcode
          && $media->hasTranslation($langcode)
        ) {
          $media = $media->getTranslation($langcode);
        }

        if (
          $media->hasField('field_publication_date')
          && !$media->get('field_publication_date')->isEmpty()
        ) {

          $date = $media->get('field_publication_date')->date;

          if ($date) {
            $timestamp = $date->getTimestamp();
          }
        }
      }
    }

    foreach ($item->getFields(FALSE) as $field) {
      if ($field->getPropertyPath() === 'effective_date') {
        $field->addValue($timestamp);
      }
    }
  }

}
