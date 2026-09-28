<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Condition;

use Drupal\Core\Condition\Attribute\Condition;
use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Route Name' condition.
 */
#[Condition(
  id: 'request_route_name',
  label: new TranslatableMarkup('Request Route Name'),
)]
final class RequestRouteName extends ConditionPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new RouteName instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly RouteMatchInterface $routeMatch,
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'route_names' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['route_names'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Route names'),
      '#description' => $this->t("Enter one item per line. The '*' character is a wildcard. Example: %example.", [
        '%example' => 'entity.*.canonical',
      ]),
      '#default_value' => $this->configuration['route_names'],
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['route_names'] = $form_state->getValue('route_names');
    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function summary(): string {
    return (string) $this->t(
      '@key: @value', [
        '@key' => $this->t('Route names'),
        '@value' => str_replace("\n", "; ", $this->configuration['route_name']),
      ],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(): bool {
    $route_names = mb_strtolower($this->configuration['route_names']);
    if (!$route_names) {
      return TRUE;
    }
    $current_route_name = $this->routeMatch->getRouteName();
    if (!$current_route_name) {
      return FALSE;
    }

    // Convert route names settings to a regular expression.
    // @see \Drupal\Core\Path\PathMatcher::matchPath
    $to_replace = [
      // Replace newlines with a logical 'or'.
      '/(\r\n?|\n)/',
      // Quote asterisks.
      '/\\\\\*/',
    ];
    $replacements = [
      '|',
      '[^.]*',
    ];
    $patterns_quoted = preg_quote($route_names, '/');
    $pattern = '/^(' . preg_replace($to_replace, $replacements, $patterns_quoted) . ')$/';
    return preg_match($pattern, $current_route_name) === 1;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'route.name';
    return $contexts;
  }

}
