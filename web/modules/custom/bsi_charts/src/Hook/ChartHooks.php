<?php

declare(strict_types=1);

namespace Drupal\bsi_charts\Hook;

use Drupal\bsi_charts\Element\ChartsSettingsAlter;
use Drupal\Core\Hook\Attribute\Hook;

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

    // Ensure stacking is applied vertically, not horizontally.
    unset($definition['options']['indexAxis']);

    if ($definition['type'] === 'radar') {
      // Ensure radial axis starts at 0.
      $definition['options']['scales']['r']['min'] = 0;
    }

    // If a trend dataset is present, ensure it's a line.
    $index = array_find_key($definition['data']['datasets'], static fn (array $dataset)
      => $dataset['label'] === static::LABEL_TRENDLINE);
    if ($index === NULL) {
      return;
    }

    $dataset = &$definition['data']['datasets'][$index];
    $dataset['type'] = 'line';
    $dataset['order'] = -1;
    $dataset['borderDash'] = [static::STYLE_DOT_SIZE, static::STYLE_DOT_SIZE];
  }

  /**
   * Implements hook_chart_alter().
   */
  #[Hook('chart_alter')]
  public function alterChart(array &$element, $chart_id): void {
    $element['#accessible_table_button_class'] = 'bsi-button bsi-button--primary';
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
