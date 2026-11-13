<?php

declare(strict_types=1);

namespace Drupal\bsi_charts\Hook;

use Drupal\bsi_charts\Element\ChartsSettingsAlter;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\Element;

/**
 * Hooks related to chart media.
 */
class ChartHooks {

  /**
   * Number of characters to keep in one line.
   */
  const int LABEL_MAX_LINE_LENGTH = 20;

  /**
   * Expected label for the trend line data set.
   */
  const string LABEL_TRENDLINE = 'Trend';

  /**
   * Size in pixels for the dash length and distance.
   */
  const int STYLE_DOT_SIZE = 5;

  public function __construct(
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Implements hook_chart_definition_alter().
   */
  #[Hook('chart_definition_alter')]
  public function alterChartDefinition(array &$definition, array &$element, $chart_id): void {
    if ($element['#chart_library'] !== 'chartjs') {
      return;
    }
    $element['#attached']['library'][] = 'bsi_charts/chart.js';

    if ($definition['type'] === 'pie') {
      $definition['options']['maintainAspectRatio'] = FALSE;
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

    // Apply chart limits to index axis (= y) when using horizontal alignment.
    if (($definition['options']['indexAxis'] ?? NULL) === 'y') {
      $definition['options']['scales']['x']['min'] = $definition['options']['scales']['y']['min'] ?? NULL;
      $definition['options']['scales']['x']['max'] = $definition['options']['scales']['y']['max'] ?? NULL;
    }

    if ($definition['type'] === 'radar') {
      // Ensure radial axis starts at 0.
      $definition['options']['scales']['r']['min'] = 0;
    }

    foreach ($definition['options']['scales'] ?? [] as $key => $scale) {
      if (($scale['ticks']['rotation'] ?? NULL) === 90) {
        // Avoid font face mis-rendering by not going a full 90°.
        $definition['options']['scales'][$key]['ticks']['rotation'] = 89.9;
        $definition['options']['scales'][$key]['ticks']['minRotation'] = min(89.9, $scale['ticks']['minRotation']);
        $definition['options']['scales'][$key]['ticks']['maxRotation'] = max(89.9, $scale['ticks']['maxRotation']);
      }
    }

    $lengths = array_map('mb_strlen', $definition['data']['labels'] ?? []);
    sort($lengths);
    $median = min(static::LABEL_MAX_LINE_LENGTH, $lengths[(int) floor(count($lengths) / 2)]);
    foreach ($definition['data']['labels'] ?? [] as $index => $label) {
      $definition['data']['labels'][$index] = $this->balanceLabel($label, $median);
    }

    // Use the entity's language to more easily spot missing translations.
    // May not be set when previewing a new chart.
    $definition['options']['locale'] = ($element['#entity'] ?? NULL)?->language()->getId()
      ?? $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

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

  /**
   * Helper method to add line breaks to long labels.
   */
  private function balanceLabel(string $label, int $median): string|array {
    if (mb_strlen($label) <= $median) {
      return $label;
    }

    // Split on whitespace, except for &nbsp;.
    $tokens = preg_split('/[ \t\r\n\f\v]+/u', trim($label));
    // Special characters should not stand alone.
    for ($i = 0; $i < count($tokens); $i++) {
      if (isset($tokens[$i + 1]) && in_array($tokens[$i], ['(', '[', '{'], TRUE)) {
        // Attach opening brackets to their successor.
        $tokens[$i + 1] = $tokens[$i] . $tokens[$i + 1];
        unset($tokens[$i]);
      }
      elseif ($i > 0 && in_array($tokens[$i], [')', ']', '}'], TRUE)) {
        // Attach closing brackets to their predecessor.
        $tokens[$i - 1] .= $tokens[$i];
        unset($tokens[$i]);
      }
    }
    $tokens = array_values($tokens);

    $lines = [];
    $line = '';
    foreach ($tokens as $token) {
      $candidate = $line === '' ? $token : "$line $token";
      if ($line !== '' && mb_strlen($candidate) > $median) {
        $lines[] = $line;
        $line = $token;
      }
      else {
        $line = $candidate;
      }
    }
    if ($line !== '') {
      $lines[] = $line;
    }

    return $lines;
  }

}
