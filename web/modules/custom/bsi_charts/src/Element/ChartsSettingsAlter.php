<?php

declare(strict_types=1);

namespace Drupal\bsi_charts\Element;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Callback handlers for charts_settings element.
 */
class ChartsSettingsAlter implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRender', 'process'];
  }

  /**
   * Process callback handler for the charts widget form.
   */
  public static function process(array $element): array {
    if ($element['#used_in'] !== 'basic_form') {
      return $element;
    }

    $element['type']['#type'] = 'select';
    $element['xaxis']['#type'] = 'details';
    $element['yaxis']['#type'] = 'details';
    $element['display']['#type'] = 'details';

    return $element;
  }

  /**
   * Pre-render callback handler for the charts widget form.
   */
  public static function preRender(array $element): array {
    if ($element['#used_in'] !== 'basic_form') {
      return $element;
    }

    $currentUser = \Drupal::currentUser();

    if ($element['library']['#type'] ?? NULL === 'select') {
      $element['library']['#access'] = $currentUser->hasPermission('bsi_chart customize chart library');
    }

    // Make option more visible, and independent of customization access.
    $element['stacking'] = &$element['display']['stacking'];
    unset($element['display']['stacking']);
    $element['title'] = &$element['display']['title'];
    unset($element['display']['title']);

    $element['series']['import']['#access'] = $currentUser->hasPermission('bsi_chart use import csv');
    $element['series']['#weight'] = 10;

    $element['xaxis']['#access'] = $currentUser->hasPermission('bsi_chart customize chart axis');
    $element['xaxis']['#weight'] = 20;
    $element['yaxis']['#access'] = $currentUser->hasPermission('bsi_chart customize chart axis');
    $element['yaxis']['#weight'] = 20;

    $element['display']['#access'] = $currentUser->hasPermission('bsi_chart customize chart display');
    $element['display']['#weight'] = 99;

    return $element;
  }

}
