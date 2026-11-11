<?php

declare(strict_types=1);

namespace Drupal\bsi_charts\Hook;

use Drupal\bsi_charts\Element\ChartsSettingsAlter;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Element;

/**
 * Hooks related to chart media.
 */
class ChartHooks {

  /**
   * Expected label for the trend line data set.
   */
  const string LABEL_TRENDLINE = 'Trend';

  /**
   * Size in pixels for the dash length and distance.
   */
  const int STYLE_DOT_SIZE = 5;

  /**
   * Implements hook_chart_definition_alter().
   */
  #[Hook('chart_definition_alter')]
  public function alterChartDefinition(array &$definition, array &$element, $chart_id): void {
    if ($element['#chart_library'] !== 'chartjs') {
      return;
    }
    $element['#attached']['library'][] = 'bsi_charts/chart.js';

    if ($definition['type'] === 'radar') {
      // Ensure radial axis starts at 0.
      $definition['options']['scales']['r']['min'] = 0;
    }

    foreach (['prefix', 'suffix', 'decimal_count'] as $property) {
      if (strlen((string) $element['yaxis']['#' . $property]) > 0) {
        $element['#attached']['drupalSettings']['bsi_charts'][$element['#id']][$property] = $element['yaxis']['#' . $property];
      }
    }

    // Apply prefix/suffix only to tooltip, not Y axis.
    unset(
      $definition['options']['scales']['y']['ticks']['prefix'],
      $definition['options']['scales']['y']['ticks']['suffix'],
    );

    // Use the entity's language to more easily spot missing translations.
    $definition['options']['locale'] = $element['#entity']->language()->getId();

    // If a trend dataset is present, ensure it's a line.
    $index = array_find_key($definition['data']['datasets'], static fn (array $dataset)
      => $dataset['label'] === static::LABEL_TRENDLINE);
    if ($index !== NULL) {
      $dataset = &$definition['data']['datasets'][$index];
      $dataset['type'] = 'line';
      $dataset['order'] = -1;
      $dataset['borderDash'] = [static::STYLE_DOT_SIZE, static::STYLE_DOT_SIZE];
    }
  }

  /**
   * Implements hook_chart_alter().
   */
  #[Hook('chart_alter')]
  public function alterChart(array &$element, $chart_id): void {
    $element['#accessible_table_button_class'] = 'bsi-button bsi-button--primary';

    if ($element['#chart_library'] !== 'chartjs' || $element['#chart_type'] !== 'scatter') {
      return;
    }

    // Extend scatter plot handling to support singe column data sets.
    // @see \Drupal\charts_chartjs\Plugin\chart\Library\Chartjs::populateDatasets
    foreach (Element::children($element) as $key) {
      if ($element[$key]['#type'] !== 'chart_data') {
        continue;
      }
      $numeric_labels = array_all($element['xaxis']['#labels'],
        static fn (mixed $value, int|string $key) => is_numeric($value)
      );
      foreach ($element[$key]['#data'] as $series) {
        foreach ($series as $index => $record) {
          $element[$key]['#data'][$index] = [
            $numeric_labels ? $element['xaxis']['#labels'][$index] : $index,
            $record,
          ];
        }
      }
    }
  }

  /**
   * Implements hook_element_info_alter().
   */
  #[Hook('element_info_alter')]
  public function alterElementInfo(array &$info): void {
    if (isset($info['charts_settings'])) {
      $info['charts_settings']['#pre_render'][] = [ChartsSettingsAlter::class, 'preRender'];
      $info['charts_settings']['#process'][] = [ChartsSettingsAlter::class, 'process'];
    }
    if (isset($info['chart_data_collector_table'])) {
      $info['chart_data_collector_table']['#process'][] = [ChartsSettingsAlter::class, 'processSeriesData'];
    }
  }

}
