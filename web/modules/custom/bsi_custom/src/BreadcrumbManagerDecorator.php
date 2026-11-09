<?php

namespace Drupal\bsi_custom;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Breadcrumb\BreadcrumbManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Decorates breadcrumb to adhere to menu_breadcrumb settings.
 */
class BreadcrumbManagerDecorator implements BreadcrumbBuilderInterface {
  use StringTranslationTrait;

  /**
   * Configuration object of menu_breadcrumb.settings.
   */
  private ImmutableConfig $config;

  /**
   * Configuration object of system.site.
   */
  private ImmutableConfig $siteConfig;

  public function __construct(
    private readonly BreadcrumbManager $inner,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly BreadcrumbBuilderInterface $menuBreadcrumb,
  ) {
    $this->config = $this->configFactory->get('menu_breadcrumb.settings');
    $this->siteConfig = $this->configFactory->get('system.site');
  }

  /**
   * Delegate undeclared methods to inner service.
   */
  public function __call(string $name, array $arguments) {
    return call_user_func_array([$this->inner, $name], $arguments);
  }

  /**
   * {@inheritDoc}
   */
  public function applies(RouteMatchInterface $route_match): bool {
    return TRUE;
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

    // If menu_breadcrumbs configuration changes, invalidate all breadcrumbs.
    $breadcrumb->addCacheableDependency($this->config);
    // Changing the <front> page will invalidate any generated breadcrumb.
    $breadcrumb->addCacheableDependency($this->siteConfig);
    if ($this->languageManager->isMultilingual()) {
      // Current page title is language dependent.
      $breadcrumb->addCacheContexts(['languages:' . LanguageInterface::TYPE_CONTENT]);
    }

    // Cannot alter existing links, so create a new breadcrumb.
    $new_breadcrumb = new Breadcrumb();
    $new_breadcrumb->addCacheableDependency($breadcrumb);
    $new_breadcrumb->setLinks(array_values($links));

    return $new_breadcrumb;
  }

  /**
   * Current page handling: Add, rewrite or removed based on config.
   *
   * @see \Drupal\menu_breadcrumb\MenuBasedBreadcrumbBuilder::addMissingCurrentPage
   */
  protected function ensureCurrentPage(array &$links, RouteMatchInterface $route_match): void {
    // Dirty wrapper to avoid extending or copy-pasting.
    // @todo Upstream this class and reuse original code instead of reflection.
    $ref = new \ReflectionMethod($this->menuBreadcrumb, 'addMissingCurrentPage');
    $ref->setAccessible(TRUE);
    $ref->invokeArgs($this->menuBreadcrumb, [&$links, $route_match]);

    // Prevent duplicate <front> link if add_home is also enabled.
    // MenuBreadcrumb already does this but fails on detecting <front>.
    $hide_on_front = $this->urlIsFront(Url::fromRouteMatch($route_match))
      && $this->config->get('add_home');

    if (!empty($links)) {
      // Display the last item of the breadcrumbs trail as configured.
      /** @var \Drupal\Core\Link $current */
      $current = array_pop($links);
      if ($this->config->get('append_current_page') && !$hide_on_front) {
        if (!$this->config->get('current_page_as_link')) {
          $current->setUrl(new Url('<nolink>'));
        }
        $links[] = $current;
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

    // Create a breadcrumb for <front> which may be either added or replaced:
    $label = $this->config->get('front_title')
      ? $this->siteConfig->get('name')
      : $this->t('Home', [], ['langcode' => $langcode]);
    $home_link = Link::createFromRoute($label, '<front>');

    /** @var \Drupal\Core\Url $first_url */
    $first_url = ($links[0] ?? NULL)?->getUrl();
    if ($first_url && $this->urlIsFront($first_url)) {
      // According to the confusion hopefully cleared up in issue 2754521, the
      // sense of "remove_home" is slightly different in Menu Breadcrumb:
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

  /**
   * Helper to determine if a URL targets the front page.
   */
  protected function urlIsFront(Url $url): bool {
    $front_url = Url::fromUri('internal:' . $this->siteConfig->get('page.front'));

    if (!$url->isRouted()) {
      return FALSE;
    }
    if (!$front_url->isRouted()) {
      return FALSE;
    }

    $same_route = $front_url->getRouteName() === $url->getRouteName()
      && $front_url->getRouteParameters() === $url->getRouteParameters();
    return $url->getRouteName() === '<front>' || $same_route;
  }

}
