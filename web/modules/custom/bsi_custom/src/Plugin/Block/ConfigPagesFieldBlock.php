<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\config_pages\Plugin\Block\ConfigPagesBlock;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a block that renders a single field of a config_pages entity.
 */
#[Block(
  id: 'config_pages_field',
  admin_label: new TranslatableMarkup('ConfigPages field'),
  category: new TranslatableMarkup('Config Pages'),
)]
final class ConfigPagesFieldBlock extends ConfigPagesBlock {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'field_name' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);

    /** @var array<\Drupal\config_pages\Entity\ConfigPages> $config_pages */
    $config_pages = $this->entityTypeManager->getStorage('config_pages')->loadMultiple();
    $options = [];
    foreach ($config_pages as $config_page) {
      foreach ($config_page->getFieldDefinitions() as $key => $definition) {
        $options[$config_page->getBundleEntity()->label()][$key] = $definition->getLabel();
      }
    }

    $form['field_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Field'),
      '#options' => $options,
      '#default_value' => $this->configuration['field_name'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    parent::blockSubmit($form, $form_state);
    $this->setConfigurationValue('field_name', $form_state->getValue('field_name'));
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $parent_build = parent::build();
    if (!$parent_build['#config_pages'] instanceof ConfigPages) {
      return [];
    }

    $entity = $parent_build['#config_pages'];

    $build = $entity->get($this->configuration['field_name'])
      ->view($this->configuration['config_page_view_mode']);

    $build['#contextual_links'] = $parent_build['#contextual_links'] ?? [];
    $build['#cache']['keys'] = $parent_build['#cache']['keys'] ?? [];
    $build['#cache']['keys'][] = $this->configuration['field_name'];

    $metadata = BubbleableMetadata::createFromRenderArray($build);
    $metadata->merge(BubbleableMetadata::createFromRenderArray($parent_build))
      ->applyTo($build);

    return $build;
  }

}
