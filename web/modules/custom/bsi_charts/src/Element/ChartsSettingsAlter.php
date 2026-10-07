<?php

declare(strict_types=1);

namespace Drupal\bsi_charts\Element;

use Drupal\Core\Render\Element;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Callback handlers for charts_settings element.
 */
class ChartsSettingsAlter implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRender', 'process', 'processSeriesData'];
  }

  /**
   * Process callback handler for the charts_settings form element.
   */
  public static function process(array $element): array {
    if ($element['#used_in'] !== 'basic_form') {
      return $element;
    }

    // Add context to chart types to let translations make sense.
    $element['type']['#type'] = 'select';
    foreach ($element['type']['#options'] as $option => $label) {
      if (!$label instanceof TranslatableMarkup) {
        continue;
      }
      $element['type']['#options'][$option] = new TranslatableMarkup(
        // phpcs:ignore Drupal.Semantics.FunctionT.NotLiteralString
        $label->getUntranslatedString(),
        $label->getArguments(),
        $label->getOptions() + ['context' => 'Chart type'],
      );
    }
    uasort($element['type']['#options'], static fn($a, $b) => (string) $a <=> (string) $b);

    // Element order for easier placement of children via preRender.
    $element['series']['#weight'] = 10;
    $element['plot_lines']['#type'] = 'details';
    $element['plot_lines']['#weight'] = 15;
    $element['xaxis']['#type'] = 'details';
    $element['xaxis']['#weight'] = 20;
    $element['yaxis']['#type'] = 'details';
    $element['yaxis']['#weight'] = 20;
    $element['display']['#type'] = 'details';
    $element['display']['#weight'] = 99;

    $element['display']['polar']['#description'] = new TranslatableMarkup('Enable to display the chart data based on a circle. Transforms categories into angles and values into distance from the center point.');

    // Restrict certain elements based on user permissions.
    $currentUser = \Drupal::currentUser();
    $element['series']['#import_csv'] = $currentUser
      ->hasPermission('bsi_chart use import csv');

    if (!$currentUser->hasPermission('bsi_chart customize chart display')) {
      ChartsSettingsAlter::restrictElement($element['plot_lines']);
      ChartsSettingsAlter::restrictElement($element['display'], ['polar', 'title', 'stacking']);
    }
    if (!$currentUser->hasPermission('bsi_chart customize chart axis')) {
      ChartsSettingsAlter::restrictElement($element['xaxis']);
      ChartsSettingsAlter::restrictElement($element['yaxis']);
    }

    $element['#cache']['contexts'][] = 'user.permissions';

    return $element;
  }

  /**
   * Process callback handler for the charts_data_collector_table form element.
   */
  public static function processSeriesData(array $element): array {
    if (!\Drupal::currentUser()->hasPermission('bsi_chart customize chart display')) {
      $heading = &$element['data_collector_table'][0];
      foreach (Element::children($heading) as $key) {
        ChartsSettingsAlter::restrictElement($heading[$key]['color']);
        ChartsSettingsAlter::restrictElement($heading[$key]['chart_type']);
      }
    }

    return $element;
  }

  /**
   * Pre-render callback handler for the charts widget form.
   */
  public static function preRender(array $element): array {
    if ($element['#used_in'] !== 'basic_form') {
      return $element;
    }

    // Make options more visible and independent of customization access.
    $element['stacking'] = &$element['display']['stacking'];
    unset($element['display']['stacking']);
    $element['title'] = &$element['display']['title'];
    unset($element['display']['title']);
    $element['polar'] = &$element['display']['polar'];
    unset($element['display']['polar']);

    return $element;
  }

  /**
   * Recursive helper function to hide advanced elements.
   *
   * Chart element's buildElement method need all values present, so we cannot
   * simply set `#access`. Using `#type` value also does not work.
   *
   * @see \Drupal\charts\Element\Chart::buildElement
   */
  public static function restrictElement(?array &$element, ?array $except = []): void {
    if ($element === NULL || $element === []) {
      return;
    }
    if (isset($element['#value']) || isset($element['#default_value'])) {
      $element = [
        '#type' => 'hidden',
        '#value' => $element['#value'] ?? $element['#default_value'],
      ];
    }
    elseif (($element['#input'] ?? FALSE) === FALSE || ($element['#tree'] ?? FALSE) === TRUE) {
      foreach (Element::getVisibleChildren($element) as $child) {
        if (in_array($child, $except, TRUE)) {
          continue;
        }
        ChartsSettingsAlter::restrictElement($element[$child], $except[$child] ?? []);
      }
      $element['#type'] = 'container';
      $element['#attributes']['class'][] = 'hidden';
    }
  }

}
