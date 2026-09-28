<?php

namespace Drupal\bsi_search\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search slot for Topic based search.
 */
#[Block(
  id: 'block_search_topic',
  admin_label: new TranslatableMarkup('Search slot'),
  category: new TranslatableMarkup('BSI'),
)]
class TopicExposedFilterBlock extends BlockBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {

  public const string SEARCH_VIEW_NAME = 'search';

  /**
   * Node Keep alias for the search page.
   */
  protected const string NODE_KEEP_ALIAS = 'global_search';

  /**
   * Constructs the block plugin.
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly RouteMatchInterface $currentRouteMatch,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'topic_tid' => NULL,
      'use_node_topic' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'topics']);

    $form['topic_tid'] = [
      '#type' => 'select',
      '#title' => $this->t('Topic'),
      '#options' => array_map(static fn (EntityInterface $term) => $term->label(), $terms),
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $this->configuration['topic_tid'],
    ];

    $form['use_node_topic'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use node topic'),
      '#description' => $this->t('When enabled, article and content pages will pass the Topics field as a Views argument.'),
      '#default_value' => $this->configuration['use_node_topic'] ?? FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['topic_tid'] = $form_state->getValue('topic_tid');
    $this->configuration['use_node_topic'] = $form_state->getValue('use_node_topic');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array|NULL {
    $use_node_topic = $this->configuration['use_node_topic'];

    $tid = $this->configuration['topic_tid'];
    if ($use_node_topic) {
      $node = $this->currentRouteMatch->getParameter('node');

      if ($node instanceof NodeInterface &&
        ($node->bundle() == 'article' || $node->bundle() == 'page') &&
        $node->hasField('field_topics') &&
        !$node->get('field_topics')->isEmpty()
      ) {
        $tid = $node->get('field_topics')->target_id;
      }
      else {
        return NULL;
      }
    }

    // Load the view.
    $view = Views::getView(self::SEARCH_VIEW_NAME);
    if (!$view) {
      return ['#plain_text' => $this->t('View not found')];
    }
    $view->setDisplay('block_search_slot');
    // Inject contextual filter argument.
    $view->setArguments($tid ? [$tid] : []);
    // Update action path for the search exposed filter.
    $view->override_url = $this->getSearchPageUrl(self::NODE_KEEP_ALIAS);
    $view->execute();

    $render = $view->render();

    // If render() returns NULL, return a safe fallback.
    // Required by Layout Builder.
    if (!is_array($render)) {
      return [
        '#plain_text' => $this->t('Unable to render the view.'),
      ];
    }

    $render['#pre_render'][] = [$this, 'preRender'];

    $term = $tid ? $this->entityTypeManager->getStorage('taxonomy_term')->load($tid) : NULL;
    $term_name = $term ? $term->getName() : NULL;

    return [
      'view' => $render,
      '#term' => $term_name,
    ];
  }

  /**
   * Get a node path from a Node Keep alias.
   */
  protected function getSearchPageUrl(string $keep_id): ?Url {
    $nodes = $this->entityTypeManager->getStorage('node')
      ->loadByProperties(['keeper_machine_name' => $keep_id]);

    if ($node = reset($nodes)) {
      return $node->toUrl();
    }

    return NULL;
  }

  /**
   * Pre-render callback to assist with styling.
   */
  public function preRender(array $build): array {
    if (empty($build['#exposed'])) {
      return $build;
    }

    if ($tid = $this->configuration['topic_tid']) {
      $build['#exposed']['topic'] = [
        '#type' => 'hidden',
        '#name' => 'f[0]',
        '#value' => 'topics:' . $tid,
      ];
    }

    $build['#cache']['keys'][] = 'search_topic:' . ($tid ?: 0);
    $build['#exposed']['#theme_wrappers'] = ['form__search_global'];
    $build['#exposed']['search']['#theme'] = 'input__search';
    $build['#exposed']['actions']['submit']['#theme_wrappers'] = ['input__submit_search'];

    return $build;
  }

  /**
   * Pre-render callback for the `bsi_bund_site_search` block placement.
   */
  public function preRenderHeaderSearch(array $build): array {
    $build['#exposed']['#theme_wrappers'] = ['form__search_global__header'];
    $build['#cache']['keys'][] = 'search_topic_global';
    return $build;
  }

  /**
   * Pre-render callback for the `bsi_bund_site_search` block placement.
   */
  public function preRenderHeaderSearchBlock(array $build): array {
    $build['content']['view']['#pre_render'][] = [$this, 'preRenderHeaderSearch'];
    return $build;
  }

  /**
   * Pre-render callback for other block placements.
   */
  public function preRenderCustomSearchBlock(array $build): array {
    $build['content']['view']['#pre_render'][] = [$this, 'preRenderCustomSearch'];
    return $build;

  }

  /**
   * Pre-render callback for other block placement.
   */
  public function preRenderCustomSearch(array $build): array {
    $build['#exposed']['#theme_wrappers'] = ['form__search_global__custom'];
    $build['#cache']['keys'][] = 'search_topic_custom';
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return [
      'preRender',
      'preRenderHeaderSearch',
      'preRenderCustomSearch',
      'preRenderCustomSearchBlock',
      'preRenderHeaderSearchBlock',
    ];
  }

}
