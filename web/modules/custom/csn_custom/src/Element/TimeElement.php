<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Attribute\FormElement;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Element\FormElementBase;

/**
 * Provides an HTML5 time input element.
 *
 * Drupal's textfield forces type="text" in its pre_render, which breaks
 * type="time" value handling during AJAX rebuilds. This element properly
 * supports type="time" through the full form API lifecycle.
 *
 * Usage:
 * @code
 * $form['start_time'] = [
 *   '#type' => 'bsi_time',
 *   '#title' => t('Start time'),
 *   '#default_value' => '09:00',
 * ];
 * @endcode
 */
#[FormElement('bsi_time')]
class TimeElement extends FormElementBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    $class = static::class;
    return [
      '#input' => TRUE,
      '#size' => 10,
      '#pre_render' => [[$class, 'preRenderTime']],
      '#theme' => 'input__textfield',
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state): ?string {
    if ($input !== FALSE && $input !== NULL) {
      return (string) $input;
    }
    return NULL;
  }

  /**
   * Pre-render callback: set input type to "time".
   */
  public static function preRenderTime(array $element): array {
    $element['#attributes']['type'] = 'time';
    Element::setAttributes($element, ['id', 'name', 'value', 'size']);
    return $element;
  }

}
