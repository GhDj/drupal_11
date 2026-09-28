<?php

declare(strict_types=1);

namespace Drupal\markup_decorator\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;

/**
 * Decorate external links by adding attributes.
 *
 * @todo Add 'title' options, see DownloadLinkDecorator.
 */
#[Filter(
  id: 'decorate_external_link',
  title: new TranslatableMarkup('External link decorator'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  settings: [
    'class' => '',
    'target' => '_self',
    'custom_target' => '',
    'rel' => [],
  ],
)]
final class ExternalLinkDecorator extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['help'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Alter links to external documents by specifying additional attributes.'),
    ];

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CSS class'),
      '#default_value' => $this->settings['class'],
    ];

    $form['target'] = [
      '#type' => 'select',
      '#title' => $this->t('Target'),
      '#description' => $this->t('Specifies where to open the linked document.'),
      '#options' => [
        '_self' => $this->t('Open in the same frame or window'),
        '_blank' => $this->t('Open in a new window or tab'),
        '_top' => $this->t('Open in the full body of the window'),
        '_parent' => $this->t('Open in the parent frame'),
        'custom' => $this->t('Open in a named iframe'),
      ],
      '#default_value' => $this->settings['target'],
    ];

    $form['custom_target'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom target'),
      '#description' => $this->t('Specify the name of the <code>iframe</code> element in which to open the link.'),
      '#states' => [
        'visible' => [
          ':input[name="filters[decorate_external_link][settings][target]"]' => ['value' => 'custom'],
        ],
        'required' => [
          ':input[name="filters[decorate_external_link][settings][target]"]' => ['value' => 'custom'],
        ],
      ],
    ];

    $options = [
      'external' => $this->t('Linked document is not part of this site'),
      'nofollow' => $this->t('Linked document is unendorsed'),
      'noopener' => $this->t('Linked document has no access to this site'),
      'noreferrer' => $this->t('No referrer header will be included with the request'),
    ];
    array_walk($options, function (&$description, string $rel) {
      $description = $this->t('<a href=":url"><code>@rel</code></a>: @description', [
        '@rel' => $rel,
        ':url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Attributes/rel/' . $rel,
        '@description' => $description,
      ]);
    });

    $form['rel'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Relationship with the linked document'),
      '#description' => $this->t('Specifies the relationship between this page and the linked document.'),
      '#options' => $options,
      '#default_value' => $this->settings['rel'],
      '#element_validate' => [[static::class, 'validateCheckboxes']],
    ];

    return $form;
  }

  /**
   * Validation callback to remove unchecked options from stored data.
   */
  public static function validateCheckboxes(array &$element, FormStateInterface $form_state, array &$complete_form): void {
    $value = $form_state->getValue($element['#parents']);
    $form_state->setValue($element['#parents'], array_filter($value));
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    if ($this->settings === $this->defaultConfiguration()['settings']) {
      return new FilterProcessResult($text);
    }
    $html_dom = Html::load($text);

    $links = $html_dom->getElementsByTagName('a');
    foreach ($links as $link) {
      if (!$link->hasAttribute('href') || !UrlHelper::isExternal($link->getAttribute('href'))) {
        continue;
      }

      if ($this->settings['class'] !== '') {
        $class = $link->getAttribute('class') . ' ' . $this->settings['class'];
        $link->setAttribute('class', trim($class));
      }
      if ($this->settings['target'] !== '_self') {
        $target = $this->settings['target'] === 'custom' ? $this->settings['custom_target'] : $this->settings['target'];
        $link->setAttribute('target', $target);
      }
      if ($this->settings['rel'] !== []) {
        $link->setAttribute('rel', implode(' ', $this->settings['rel']));
      }
    }

    return new FilterProcessResult(Html::serialize($html_dom));
  }

}
