<?php

declare(strict_types=1);

namespace Drupal\bsi_report\Plugin\Block;

use Drupal\book\BookManagerInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Search slot for annual report pages.
 *
 * Renders the search form scoped to the current book. The form submission
 * redirects to the book's entry page so results are shown there.
 */
#[Block(
  id: 'bsi_report_search_slot',
  admin_label: new TranslatableMarkup('Report search slot'),
  category: new TranslatableMarkup('BSI Bericht'),
)]
class ReportSearchSlotBlock extends BlockBase implements ContainerFactoryPluginInterface, TrustedCallbackInterface {

  public function __construct(
    array $configuration,
    string $plugin_id,
    $plugin_definition,
    protected readonly BookManagerInterface $bookManager,
    protected readonly RouteMatchInterface $currentRouteMatch,
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
      $container->get('book.manager'),
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array|null {
    $node = $this->currentRouteMatch->getParameter('node');
    if (!$node instanceof NodeInterface) {
      return NULL;
    }

    $bookLink = $this->bookManager->loadBookLink($node->id());
    if (empty($bookLink['bid'])) {
      return NULL;
    }

    $bid = (string) $bookLink['bid'];

    $view = Views::getView('search_yearly_report');
    if (!$view) {
      return NULL;
    }

    $view->setDisplay('block_search_slot');
    $view->setArguments([$bid]);

    // Redirect form submission to the entry page (book root node).
    $entryPageUrl = $this->getEntryPageUrl((int) $bid);
    if ($entryPageUrl) {
      $view->override_url = $entryPageUrl;
    }

    $view->execute();
    $render = $view->render();

    if (!is_array($render)) {
      return NULL;
    }

    $render['#pre_render'][] = [$this, 'preRender'];

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['bsi-report-search'],
      ],
      'heading' => [
        '#markup' => '<span class="bsi-searchbar__title">' . $this->t('Search annual report') . '</span>',
      ],
      'view' => $render,
    ];
  }

  /**
   * Get the URL of the entry page (book root node).
   */
  protected function getEntryPageUrl(int $bid): ?Url {
    $bookLinks = $this->bookManager->loadBookLinks([$bid]);
    if (!empty($bookLinks[$bid])) {
      return Url::fromRoute('entity.node.canonical', ['node' => $bid]);
    }

    // Fallback: the bid is the nid of the root node.
    return Url::fromRoute('entity.node.canonical', ['node' => $bid]);
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
