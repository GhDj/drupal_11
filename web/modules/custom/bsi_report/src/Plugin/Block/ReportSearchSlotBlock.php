<?php

declare(strict_types=1);

namespace Drupal\bsi_report\Plugin\Block;

use Drupal\book\BookInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\views\Views;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search slot for annual report pages.
 *
 * Renders the search form scoped to the current book. The form submission
 * redirects to the book's search page, identified as such:
 *   - node in the same book
 *   - node has a keeper machine name "report_search_<year>"
 *
 * @todo On non-book pages, relay to latest report -OR- site search?
 */
#[Block(
  id: 'bsi_report_search_slot',
  admin_label: new TranslatableMarkup('Report search slot'),
  category: new TranslatableMarkup('BSI Report'),
  context_definitions: [
    'node' => new EntityContextDefinition('entity:node', required: FALSE),
  ],
)]
class ReportSearchSlotBlock extends BlockBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {

  /**
   * Node Keep machine name for the 'report search' page.
   */
  const string PAGE_REPORT_SEARCH_RESULTS = 'report_search';

  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
    protected readonly LoggerInterface $logger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('logger.factory')->get('bsi'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Using getContextValues() over getContextValue() for legacy support.
    // Existing placements without context would otherwise cause whitepages.
    $node = $this->getContextValues()['node'] ?? NULL;
    if (!$node) {
      // @todo Remove warning, $this->logger and make context required.
      $this->logger->warning('Deprecated report search slot configuration. Please ensure a valid node context or expect future whitepages.');
    }
    if (!$node instanceof BookInterface || empty($node->getBook()['bid'])) {
      return [];
    }

    if (!($view = Views::getView('search_yearly_report'))) {
      return [];
    }

    $bid = (int) $node->getBook()['bid'];
    $view->setDisplay('block_search_report_slot');
    $view->setArguments([$bid]);

    // Redirect form submission to the report search page.
    $view->override_url = $this->getSearchPageUrl(static::PAGE_REPORT_SEARCH_RESULTS, $bid);

    $view->execute();

    if (!($render = $view->render())) {
      return [];
    }
    $render['#pre_render'][] = [$this, 'preRender'];

    return [
      'heading' => [
        '#type' => 'inline_template',
        '#template' => '<span class="bsi-searchbar__title">{{ title }}</span>',
        '#context' => [
          'title' => $view->getTitle(),
        ],
      ],
      'view' => $render,
      '#attributes' => [
        'class' => ['bsi-report-search'],
      ],
    ];
  }

  /**
   * Get a node path from a Node Keep alias.
   */
  protected function getSearchPageUrl(string $keep_id, int $bid): ?Url {
    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('keeper_machine_name', $keep_id . '_%', 'LIKE')
      ->range(0, 1)
      ->addTag('restrict_book_bid')
      ->addMetaData('book_bid', $bid);

    $nodes = $storage->loadMultiple($query->execute());
    if ($node = reset($nodes)) {
      return $node->toUrl();
    }

    $this->logger->warning('Missing report search page, please add a page to this book with a node keeper machine name like <code>' . static::PAGE_REPORT_SEARCH_RESULTS . '_&lt;year&gt;</code>');
    return NULL;
  }

  /**
   * Pre-render callback to apply search form theming.
   */
  public function preRender(array $build): array {
    if (empty($build['#exposed'])) {
      return $build;
    }

    $build['#exposed']['#theme_wrappers'] = ['form__search_global'];
    $build['#exposed']['search']['#theme'] = 'input__search';
    $build['#exposed']['actions']['submit']['#theme_wrappers'] = ['input__submit_search'];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRender'];
  }

}
