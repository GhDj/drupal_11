<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Text filter to reuse existing <figcaption> elements.
 */
#[Filter(
  id: 'bsi_editor_figcaption',
  title: new TranslatableMarkup('Caption images via existing figcaption'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Place this filter after the "Embed media" filter.'),
  settings: ['remove_empty' => TRUE],
)]
final class Figcaption extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['remove_empty'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove empty <code>@element</code> element', [
        '@element' => '<figcaption>',
      ]),
      '#default_value' => $this->settings['remove_empty'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $dom = Html::load($text);
    $xpath = new \DOMXPath($dom);
    $nodes = $xpath->query('//figcaption-wrapper');
    /** @var \DOMNode $wrapper */
    foreach ($nodes as $wrapper) {
      /** @var \DOMNode $node */
      $node = $wrapper->firstElementChild;

      if ($class_attr = $wrapper->getAttribute('class')) {
        $node->setAttribute('class', $class_attr);
      }

      // @todo Log error if no figcaption is found?
      if (($caption_attr = $wrapper->getAttribute('data-caption'))
        && ($figcaption = $node->getElementsByTagName('figcaption')->item(0))) {
        $figcaption->nodeValue = $caption_attr ?: $figcaption->nodeValue;
      }

      // Replace node to remove the wrapper element.
      $parent = $wrapper->parentNode;
      while ($wrapper->firstChild) {
        $parent->insertBefore($wrapper->firstChild, $wrapper);
      }
      $parent->removeChild($wrapper);
    }

    if ($this->settings['remove_empty']) {
      $nodes = $xpath->query('//figcaption[normalize-space(string(.))=""]');
      foreach ($nodes as $figcaption) {
        /** @var \DOMNode $figcaption */
        $figcaption->parentNode->removeChild($figcaption);
      }
    }

    $result = new FilterProcessResult($text);
    return $result->setProcessedText(Html::serialize($dom));
  }

}
