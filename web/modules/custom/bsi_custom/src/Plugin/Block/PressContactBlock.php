<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a press contact block.
 */
#[Block(
  id: 'bsi_custom_press_contact',
  admin_label: new TranslatableMarkup('Press contact'),
  category: new TranslatableMarkup('BSI'),
  context_definitions: [
    'node' => new EntityContextDefinition('entity:node'),
  ],
)]
final class PressContactBlock extends BlockBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $press_contact = $this->configuration['press_contact'] ?? NULL;
    if ($press_contact) {
      $press_contact = $this->entityTypeManager->getStorage('media')
        ->load($press_contact);
    }
    $form['press_contact'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Press contact'),
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['contact'],
      ],
      '#default_value' => $press_contact,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['press_contact'] = $form_state->getValue('press_contact');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    /** @var \Drupal\node\NodeInterface|null $entity */
    $entity = $this->getContextValue('node');
    if (!$entity || empty($this->configuration['press_contact'])
      || !$entity->hasField('field_article_type')
      || $entity->get('field_article_type')->value !== 'press_release') {
      return [];
    }

    $press_contact = $this->entityTypeManager->getStorage('media')
      ->load($this->configuration['press_contact']);
    if (!$press_contact instanceof EntityInterface) {
      return [];
    }

    $build['#media'] = $press_contact;
    // @todo Use separate view mode?
    $build['press_contact'] = $this->entityTypeManager->getViewBuilder('media')
      ->view($press_contact, 'default');

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface|null $entity */
    $entity = $this->getContextValue('node');
    if (!$entity instanceof EntityInterface) {
      return AccessResult::forbidden();
    }
    return $entity->access('view', NULL, TRUE);
  }

}
