<?php

namespace Drupal\bsi_report\Plugin\Condition;

use Drupal\book\BookInterface;
use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a 'Book level' condition.
 */
#[Condition(
  id: 'book_level',
  label: new TranslatableMarkup('Book level'),
  context_definitions: [
    'node' => new EntityContextDefinition('entity:node'),
  ]
)]
class BookLevel extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'level' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\ContextException
   */
  public function evaluate(): bool {
    $node = $this->getContextValue('node');
    if (!$node instanceof BookInterface || !$node->getBook()) {
      return FALSE;
    }
    return $node->getBook()['depth'] === $this->configuration['level'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['level'] = [
      '#title' => $this->t('Books'),
      '#type' => 'select',
      '#options' => [
        1 => $this->t('Level 1 (top level)'),
        2 => $this->t('Level 2'),
        3 => $this->t('Level 3'),
        4 => $this->t('Level 4'),
        5 => $this->t('Level 5'),
        6 => $this->t('Level 6'),
        7 => $this->t('Level 7'),
        8 => $this->t('Level 8'),
        9 => $this->t('Level 9'),
      ],
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $this->configuration['level'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['level'] = $form_state->getValue('level');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    if ($this->isNegated()) {
      return $this->t('Content is not on level @level', [
        '@level' => $this->configuration['level'],
      ]);
    }
    return $this->t('Content is on level @level', [
      '@level' => $this->configuration['level'],
    ]);
  }

}

