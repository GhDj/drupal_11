<?php

declare(strict_types=1);

namespace Drupal\markup_decorator\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Allow adding classes to elements detected using a CSS-like query.
 */
#[Filter(
  id: 'decorate_markup',
  title: new TranslatableMarkup('Markup decorator'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Decorate specific tags and elements with custom CSS classes.'),
  settings: [
    'rules' => '',
  ],
)]
class MarkupDecorator extends FilterBase {

  /**
   * {@inheritDoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['rules'] = [
      '#type' => 'textarea',
      '#title' => new TranslatableMarkup('Rules'),
      '#description' => new TranslatableMarkup('Add one item per line in the format %format. Example: <code>@example</code>', [
        '%format' => $this->t('selector|value for the class attribute'),
        '@example' => 'p.foo#bar|some-class-name another-class',
      ]),
      '#default_value' => $this->settings['rules'],
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $dom = Html::load($text);

    $rules = explode("\n", $this->settings['rules']);
    foreach ($rules as $rule) {
      [$selector, $classes] = explode('|', $rule);
      $classes_to_apply = explode(' ', $classes);
      $classes_to_apply = array_map('trim', $classes_to_apply);
      if (!$classes_to_apply) {
        continue;
      }

      $this->processRule($dom, $selector, $classes_to_apply);
    }

    return new FilterProcessResult(Html::serialize($dom));
  }

  /**
   * Process a single rule.
   *
   * @param \DOMDocument $dom
   *   The DOM on which to work.
   * @param string $selector
   *   The selector of the rule.
   * @param array $classes_to_apply
   *   The classes to apply for the rule.
   */
  protected function processRule(\DOMDocument $dom, string $selector, array $classes_to_apply): void {
    [$tag, $classes, $id] = $this->parseSelector($selector);
    if (!$tag) {
      return;
    }

    $elements = $dom->getElementsByTagName($tag);
    foreach ($elements as $element) {
      /** @var \DOMElement $element */
      $applicable = TRUE
        && (!$classes || !array_diff($classes, explode(' ', $element->getAttribute('class'))))
        && (!$id || $element->getAttribute('id') === $id);

      if ($applicable) {
        $old_classes = explode(' ', $element->getAttribute('class'));
        $new_classes = array_merge($classes_to_apply, $old_classes);
        $element->setAttribute('class', implode(' ', array_unique($new_classes)));
      }
    }
  }

  /**
   * Parse a CSS-like selector into its components.
   *
   * @param string $selector
   *   The selector to parse.
   *
   * @return array
   *   The components for tag name, classes list and id attribute.
   */
  protected function parseSelector(string $selector): array {
    // @todo Add support for attribute filtering, e.g. a[href^="http"]
    preg_match('/^(?P<tag>[\w0-9]+)(?P<class>(?:\.[-_0-9\w]+)+)?(?P<id>#[-_0-9\w]+)?/', $selector, $matches);

    [, $tag, $class, $id] = ($matches + [NULL, '', '', '']);

    $classes = array_map(
      fn($item) => ltrim($item, '.'),
      array_filter(explode('.', $class))
    );
    $id = ltrim($id ?? '', '#');

    return [$tag, $classes, $id];
  }

}
