<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\ViewsReferenceSetting;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\ViewExecutable;
use Drupal\viewsreference\Plugin\ViewsReferenceSettingInterface;
use Drupal\viewsreference_filter\ViewsRefFilterUtilityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Views reference setting plugin to select a subset of fields to render.
 *
 * @ViewsReferenceSetting(
 *   id = "field_select",
 *   label = @Translation("Field selection"),
 *   default_value = {},
 * )
 */
class ViewsReferenceFields extends PluginBase implements ViewsReferenceSettingInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new ViewsReferenceExposedFilters object.
   *
   * @param array $configuration
   *   The configuration.
   * @param string $pluginId
   *   The plugin_id for the plugin instance.
   * @param mixed $pluginDefinition
   *   The plugin implementation definition.
   * @param \Drupal\viewsreference_filter\ViewsRefFilterUtilityInterface $viewsUtility
   *   The views reference filter utility.
   */
  public function __construct(
    array $configuration,
    string $pluginId,
    mixed $pluginDefinition,
    protected readonly ViewsRefFilterUtilityInterface $viewsUtility,
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('viewsreference_filter.views_utility')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alterFormField(array &$form_field): void {
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $this->viewsUtility->loadView(
      $this->configuration['view_name'],
      $this->configuration['display_id'],
    );
    if (!$view) {
      $form_field = [];
      return;
    }

    $style = $view->getDisplay()->getOption('style');
    if ($style['type'] !== 'table' && !($style['options']['uses_fields'] ?? FALSE)) {
      $form_field = [];
      return;
    }

    $options = [];
    foreach ($view->field as $key => $plugin) {
      if ($plugin->options['exclude'] !== TRUE) {
        $options[$key] = $plugin->adminLabel();
      }
    }

    $description = [];
    $description[] = $this->t('Select @type to display.', [
      '@type' => $this->t('fields'),
    ]);
    $description[] = $this->t('Leave empty to use the default.');

    $form_field = [
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $form_field['#default_value'] ?: [],
      '#description' => array_map(
        static fn ($text) => ['#markup' => $text, '#suffix' => ' '],
        $description,
      ),
      '#sorted' => TRUE,
      '#element_validate' => [
        [static::class, 'validateElement'],
      ],
    ] + $form_field;
  }

  /**
   * {@inheritdoc}
   */
  public function alterView(ViewExecutable $view, mixed $value): void {
    if (!$value || !$view->inited) {
      return;
    }

    foreach ($view->field as $plugin) {
      $id = $plugin->options['id'];
      if (!isset($value[$id])) {
        unset($view->field[$id]);
      }
    }
  }

  /**
   * Validation callback to only store active values.
   */
  public static function validateElement(array $element, FormStateInterface $form_state, array $form): array {
    $submitted = $form_state->getValue($element['#parents']);
    $form_state->setValue($element['#parents'], array_filter($submitted));

    return $element;
  }

}
