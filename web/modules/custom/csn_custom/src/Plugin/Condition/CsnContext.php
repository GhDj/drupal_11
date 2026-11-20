<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Plugin\Condition;

use Drupal\csn_custom\CsnContextChecker;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CSN page' condition.
 */
#[Condition(
  id: 'csn_page',
  label: new TranslatableMarkup('CSN page'),
  context_definitions: [
    'group' => new EntityContextDefinition('entity:group', required: FALSE),
    // @note All entity types configured for use in group_relationships.
    'node' => new EntityContextDefinition('entity:node', required: FALSE),
    'media' => new EntityContextDefinition('entity:media', required: FALSE),
    'user' => new EntityContextDefinition('entity:user', required: FALSE),
  ],
)]
final class CsnContext extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new CsnContext instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly CsnContextChecker $contextChecker,
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
      $container->get('Drupal\csn_custom\CsnContextChecker'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'csn_page' => FALSE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['csn_page'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is CSN page'),
      '#default_value' => $this->configuration['csn_page'],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['csn_page'] = $form_state->getValue('csn_page');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): MarkupInterface|string {
    return $this->isNegated()
      ? $this->t('Not on CSN pages')
      : $this->t('Only on CSN pages');
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    return $this->contextChecker->isCsnPage($this->getEntity());
  }

  /**
   * Get the primary entity to evaluate.
   */
  private function getEntity(): ?ContentEntityInterface {
    return $this->getContextValue('group')
      ?? $this->getContextValue('node')
      ?? $this->getContextValue('media')
      ?? $this->getContextValue('user');
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags(): array {
    return Cache::mergeTags(parent::getCacheTags(),
      $this->contextChecker->getCacheTags(),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(),
      $this->contextChecker->getCacheContexts(),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::mergeMaxAges(parent::getCacheMaxAge(),
      $this->contextChecker->getCacheMaxAge(),
    );
  }

}
