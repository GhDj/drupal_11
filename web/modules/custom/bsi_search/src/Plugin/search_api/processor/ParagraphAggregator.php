<?php

namespace Drupal\bsi_search\Plugin\search_api\processor;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Paragraph field aggregator.
 */
#[SearchApiProcessor(
  id: "paragraph_aggregator",
  label: new TranslatableMarkup("Paragraph Aggregator"),
  description: new TranslatableMarkup("Aggregates all paragraph fields marked as full-text separated by boost value."),
  stages: [
    "add_properties" => 0,
    "pre_index_save" => 0,
  ]
)]
class ParagraphAggregator extends ProcessorPluginBase {

  /**
   * Type of fields to be aggregated.
   */
  protected const MAPPED_FIELD_TYPES = [
    'text',
    'text_long',
    'string',
    'solr_text_unstemmed',
  ];

  /**
   * Dynamic property definitions.
   */
  protected array $dynamicProperties;

  /**
   * Build dynamic property definitions based on index boosts.
   */
  protected function buildDynamicProperties(): void {
    if (!empty($this->dynamicProperties)) {
      return;
    }

    $index = $this->index;
    $boost_groups = [];

    foreach ($index->getFields() as $field) {

      // Only include paragraph fields.
      if ($field->getDatasourceId() !== 'entity:paragraph') {
        continue;
      }

      if (in_array($field->getType(), ['text', 'text_long', 'string', 'solr_text_unstemmed'])) {

        $path = $field->getPropertyPath();
        $parts = explode('.', $path);
        $field_name = end($parts);

        $boost = (string) $field->getBoost() ?? 1;

        $boost_groups[$boost][] = $field_name;
      }
    }

    // Create dynamic fields.
    foreach ($boost_groups as $boost => $fields) {
      $id = "aggregated_paragraph_text_boost_$boost";

      $this->dynamicProperties[$id] = [
        'boost' => $boost,
        'fields' => $fields,
        'definition' => new ProcessorProperty([
          'label' => $this->t('Aggregated paragraph text (boost @boost)', ['@boost' => $boost]),
          'description' => $this->t('Paragraph text aggregated for fields with boost @boost.', ['@boost' => $boost]),
          'type' => 'string',
          'processor_id' => $this->getPluginId(),
        ]),
      ];
    }
  }

  /**
   * Declare dynamic fields.
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    if ($datasource) {
      return [];
    }

    $this->buildDynamicProperties();

    $properties = [];
    foreach ($this->dynamicProperties as $id => $info) {
      $properties[$id] = $info['definition'];
    }

    return $properties;
  }

  /**
   * Aggregate paragraph text into dynamic boost fields.
   */
  public function addFieldValues(ItemInterface $item): void {
    $entity = $item->getOriginalObject()->getValue();
    if (!$entity) {
      return;
    }

    $this->buildDynamicProperties();

    $output = [];
    foreach ($this->dynamicProperties as $id => $info) {
      $output[$id] = [];
    }

    // Loop through paragraph reference fields.
    foreach ($entity->getFieldDefinitions() as $field_name => $definition) {

      if ($definition->getSetting('target_type') !== 'paragraph') {
        continue;
      }

      foreach ($entity->get($field_name) as $item_ref) {
        $paragraph = $item_ref->entity;
        if (!$paragraph) {
          continue;
        }

        $visited = [];
        $this->collectParagraphValues($paragraph, $output, $visited);
      }

    }

    // Save aggregated values.
    foreach ($output as $id => $values) {
      $item->getField($id)?->addValue(implode("\n\n", $values));
    }
  }

  /**
   * Recursively collect paragraph values based on dynamic properties.
   */
  protected function collectParagraphValues(Paragraph $paragraph, array &$output, array &$visited = []): void {
    // Prevent infinite loops.
    if (in_array($paragraph->id(), $visited)) {
      return;
    }

    $visited[] = $paragraph->id();

    foreach ($paragraph->getFieldDefinitions() as $paragraph_field_name => $paragraph_definition) {

      if ($paragraph->get($paragraph_field_name)->isEmpty()) {
        continue;
      }

      $field_type = $paragraph_definition->getType();

      // If this is a nested paragraph field.
      if ($field_type === 'entity_reference_revisions' && $paragraph_definition->getSetting('target_type') === 'paragraph') {
        foreach ($paragraph->get($paragraph_field_name)->referencedEntities() as $nested) {
          if ($nested instanceof Paragraph) {
            $this->collectParagraphValues($nested, $output, $visited);
          }
        }
      }

      // Match against boost-configured fields.
      foreach ($this->dynamicProperties as $id => $info) {
        if (in_array($paragraph_field_name, $info['fields'])) {
          $value = $paragraph->get($paragraph_field_name)->getString();
          if (!empty($value)) {
            $output[$id][] = $value;
          }
        }
      }

    }
  }

}
