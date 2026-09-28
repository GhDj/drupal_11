<?php

declare(strict_types=1);

namespace Drupal\bsi_webform\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformElementManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Bsi webform settings for this site.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typedConfigManager,
    private readonly WebformElementManagerInterface $elementManager,
  ) {
    parent::__construct($config_factory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('plugin.manager.webform.element'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bsi_webform_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bsi_webform.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    // Get all plugin definitions (element types).
    $definitions = $this->elementManager->getDefinitions();

    // Prepare a simple list of element IDs and labels.
    $elements = [];
    foreach ($definitions as $id => $definition) {
      $elements[$id] = $definition['label'] ?? $id;
    }

    // Sort alphabetically by label.
    asort($elements);

    // Add multiple checkboxes field.
    $form['element_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select Webform Element Types'),
      '#options' => $elements,
      '#description' => $this->t('Choose one or more Webform element types. These will be the allowed element types in all webforms. Will be ignored if user has bypass permission.'),
      '#config_target' => 'bsi_webform.settings:element_types',
    ];

    return parent::buildForm($form, $form_state);
  }

}
