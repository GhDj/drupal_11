<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a condition that checks if the current page belongs to a group.
 */
#[Condition(
  id: 'group_content_visibility',
  label: new TranslatableMarkup('Group Content Visibility'),
)]
final class GroupContentVisibility extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RouteMatchInterface $routeMatch,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'group_types' => [],
      'route_names' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $group_types = $this->entityTypeManager->getStorage('group_type')->loadMultiple();
    $options = [];
    foreach ($group_types as $id => $type) {
      $options[$id] = $type->label();
    }

    $form['group_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Group types'),
      '#description' => $this->t('Show this block on pages that belong to the selected group types.'),
      '#options' => $options,
      '#default_value' => $this->configuration['group_types'],
    ];

    $form['route_names'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Additional route names'),
      '#description' => $this->t("Additional routes where this block should appear. Enter one per line. The '*' character is a wildcard."),
      '#default_value' => $this->configuration['route_names'],
    ];

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['group_types'] = array_filter($form_state->getValue('group_types'));
    $this->configuration['route_names'] = $form_state->getValue('route_names');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    $group_types = array_filter($this->configuration['group_types']);
    if (empty($group_types)) {
      return (string) $this->t('No group types selected');
    }
    return (string) $this->t('Group types: @types', [
      '@types' => implode(', ', $group_types),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    $group_types = array_filter($this->configuration['group_types']);
    $route_names = trim($this->configuration['route_names']);

    if (empty($group_types) && empty($route_names)) {
      return TRUE;
    }

    // Check additional route names.
    if (!empty($route_names) && $this->matchesRouteName($route_names)) {
      return TRUE;
    }

    // Check if the current route has a group parameter.
    $group = $this->routeMatch->getParameter('group');
    if ($group && isset($group_types[$group->bundle()])) {
      return TRUE;
    }

    // Check if the current node belongs to one of the selected group types.
    if (!empty($group_types)) {
      $node = $this->routeMatch->getParameter('node');
      if ($node instanceof NodeInterface) {
        return $this->nodeInGroup($node, $group_types);
      }
    }

    return FALSE;
  }

  /**
   * Check if a node belongs to any of the given group types.
   */
  private function nodeInGroup(NodeInterface $node, array $group_types): bool {
    /** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $storage */
    $storage = $this->entityTypeManager->getStorage('group_relationship');
    $relationships = $storage->loadByEntity($node);

    foreach ($relationships as $relationship) {
      if (isset($group_types[$relationship->getGroup()->bundle()])) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Check if the current route matches the configured route name patterns.
   */
  private function matchesRouteName(string $route_names): bool {
    $current_route_name = $this->routeMatch->getRouteName();
    if (!$current_route_name) {
      return FALSE;
    }

    $to_replace = [
      '/(\r\n?|\n)/',
      '/\\\\\*/',
    ];
    $replacements = [
      '|',
      '[^.]*',
    ];
    $patterns_quoted = preg_quote(mb_strtolower($route_names), '/');
    $pattern = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
    return preg_match($pattern, $current_route_name) === 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'route';
    return $contexts;
  }

}
