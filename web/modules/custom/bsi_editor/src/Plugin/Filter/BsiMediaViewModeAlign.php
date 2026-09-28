<?php

declare(strict_types=1);

namespace Drupal\bsi_editor\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Override View Mode for embedded media with certain alignments.
 */
#[Filter(
  id: 'bsi_media_view_mode_align',
  title: new TranslatableMarkup('Media View Mode Override'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  settings: ['view_mode_override' => []],
)]
final class BsiMediaViewModeAlign extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new BsiMediaViewModeAlign instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityDisplayRepositoryInterface $entityDisplayRepository,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_display.repository'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $view_modes = $this->entityDisplayRepository->getViewModes('media');
    $options = array_combine(
      array_keys($view_modes),
      array_column($view_modes, 'label')
    );

    $form['view_mode_override'] = [
      '#type' => 'item',
      '#title' => 'Override View Mode of embedded media based on selected alignment.',
      '#input' => FALSE,
    ];
    foreach (['left', 'right', 'center'] as $alignment) {
      $form['view_mode_override'][$alignment] = [
        '#type' => 'select',
        '#title' => $this->t('@align alignment', [
          '@align' => ucfirst($alignment),
        ]),
        '#options' => $options,
        '#empty_option' => $this->t('- None -'),
        '#default_value' => $this->settings['view_mode_override'][$alignment] ?? NULL,
      ];
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);
    if (stristr($text, '<drupal-media') === FALSE) {
      return $result;
    }

    $dom = Html::load($text);
    $xpath = new \DOMXPath($dom);

    foreach ($xpath->query('//drupal-media[' .
        '@data-entity-type="media" and normalize-space(@data-entity-uuid)!=""' .
        'and contains(normalize-space(@class), "align-")' .
      ']') as $node) {
      /** @var \DOMElement $node */
      $classes = ' ' . $node->getAttribute('class') . ' ';

      $view_mode_id = NULL;
      foreach (['left', 'right', 'center'] as $alignment) {
        if (str_contains($classes, ' align-' . $alignment . ' ')) {
          $view_mode_id = $this->settings['view_mode_override'][$alignment] ?? NULL;
        }
      }
      if ($view_mode_id !== NULL) {
        $node->setAttribute('data-view-mode', $view_mode_id);
      }
    }

    return $result->setProcessedText(Html::serialize($dom));
  }

}
