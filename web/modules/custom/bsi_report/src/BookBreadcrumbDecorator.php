<?php

namespace Drupal\bsi_report;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Decorates book.breadcrumb to adhere to menu_breadcrumb settings.
 */
class BookBreadcrumbDecorator implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * Configuration object of menu_breadcrumb.settings.
   */
  private ImmutableConfig $config;

  public function __construct(
    private readonly BreadcrumbBuilderInterface $inner,
    private readonly BreadcrumbBuilderInterface $menuBreadcrumb,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {
    $this->config = $configFactory->get('menu_breadcrumb.settings');
  }

  /**
   * {@inheritDoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return $this->inner->applies($route_match);
  }

  /**
   * {@inheritDoc}
   */
  public function build(RouteMatchInterface $route_match): Breadcrumb {
    $breadcrumb = $this->inner->build($route_match);

    $current_url = Url::fromRoute('<current>')->toString();
    $url_found = FALSE;

    $links = $breadcrumb->getLinks();
    foreach ($links as $index => $link) {
      $url = $link->getUrl();
      if ($this->config->get('exclude_empty_url') && empty($url->toString())) {
        unset($links[$index]);
      }
      elseif ($this->config->get('exclude_disabled_menu_items') && !$url->access()) {
        unset($links[$index]);
      }
      elseif ($this->config->get('stop_on_first_match') && $url->toString() == $current_url) {
        if ($url_found) {
          unset($links[$index]);
        }
        $url_found = TRUE;
      }
    }
    $this->ensureCurrentPage($links, $route_match);
    $this->ensureFrontPage($links, $route_match);

    // Cannot alter existing links, so create a new breadcrumb.
    $new_breadcrumb = new Breadcrumb();
    $new_breadcrumb->addCacheableDependency($breadcrumb);
    $new_breadcrumb->setLinks(array_values($links));

    return $new_breadcrumb;
  }

  /**
   * Dirty wrapper to avoid extending or copy-pasting.
   */
  protected function ensureCurrentPage(array &$links, RouteMatchInterface $route_match): void {
    $ref = new \ReflectionMethod($this->menuBreadcrumb, 'addMissingCurrentPage');
    $ref->setAccessible(TRUE);
    $ref->invokeArgs($this->menuBreadcrumb, [&$links, $route_match]);

    if (!empty($links)) {
      // Display the last item of the breadcrumbs trail as the options indicate.
      /** @var \Drupal\Core\Link $current */
      $current = array_pop($links);
      if ($this->config->get('append_current_page')) {
        if (!$this->config->get('current_page_as_link')) {
          $current->setUrl(new Url('<nolink>'));
        }
        array_push($links, $current);
      }
    }
  }

  /**
   * Frontpage link handling, copied from decorated service due to bad arch.
   *
   * If options are set to remove <front>, strip off that link, otherwise
   * replace it with a breadcrumb named according to option settings:
   */
  protected function ensureFrontPage(array &$links, RouteMatchInterface $routeMatch): void {
    $langcode = $this->languageManager
      ->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)
      ->getId();
    $site_config = $this->configFactory->get('system.site');

    // Create a breadcrumb for <front> which may be either added or replaced:
    $label = $this->config->get('front_title')
      ? $site_config->get('name')
      : $this->t('Home', [], ['langcode' => $langcode]);
    $home_link = Link::createFromRoute($label, '<front>');
    $front_url = Url::fromUri('internal:' . $site_config->get('page.front'));

    /** @var \Drupal\Core\Url $first_url */
    $first_url = $links[0]?->getUrl();
    $has_front = FALSE;
    if ($first_url && $first_url->isRouted() && $front_url->isRouted()) {
      $same_route = $front_url->getRouteName() === $first_url->getRouteName()
        && $front_url->getRouteParameters() === $first_url->getRouteParameters();
      $has_front = $first_url->getRouteName() === '<front>' || $same_route;
    }

    if ($has_front) {
      // According to the confusion hopefully cleared up in issue 2754521, the
      // sense of "remove_home" is slightly different than in Menu Breadcrumb:
      // we remove any match with <front> rather than replacing it.
      if ($this->config->get('remove_home')) {
        array_shift($links);
      }
      elseif ($this->config->get('front_title') != 2) {
        $links[0] = $home_link;
      }
    }
    else {
      // If trail *doesn't* begin with the home page, add it if that option set.
      if ($this->config->get('add_home')) {
        array_unshift($links, $home_link);
      }
    }
  }

}
