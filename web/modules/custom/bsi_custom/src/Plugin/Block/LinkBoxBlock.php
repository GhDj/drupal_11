<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Provides a link box block.
 */
#[Block(
  id: 'bsi_custom_link_box',
  admin_label: new TranslatableMarkup('Link box'),
  category: new TranslatableMarkup('BSI'),
  context_definitions: [
    'entity' => new EntityContextDefinition('entity:node'),
  ],
)]
final class LinkBoxBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
  protected function blockAccess(AccountInterface $account, $return_as_object = FALSE): AccessResultInterface {
    $access_result = parent::blockAccess($account);

    /** @var \Drupal\Core\Entity\FieldableEntityInterface $entity */
    if ($entity = $this->getContextValue('entity')) {
      $access_result->andIf($entity->access('view', $account, TRUE));
    }
    else {
      $access_result->andIf(new AccessResultForbidden('No Entity Found.'));
    }

    return $access_result;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface $entity */
    $entity = $this->getContextValue('entity');
    if (!$entity instanceof FieldableEntityInterface) {
      return [];
    }

    $build = [];
    // @todo In a perfect world, the field names would be configurable.
    $build['links'] = $this->buildLinks($entity, 'field_links', 'field_links_title');
    $build['downloads'] = $this->buildLinks($entity, 'field_downloads', 'field_downloads_title');

    return array_filter($build);
  }

  /**
   * Builds the links render array, based on the provided field names.
   */
  protected function buildLinks(FieldableEntityInterface $entity, string $link_field, string $heading_field): array {
    $links = $entity->hasField($link_field) ? $entity->get($link_field) : NULL;
    if (!$links || $links->isEmpty()) {
      return [];
    }
    $this->prepareLinks($links);

    $links = $links->view('linkbox');
    $heading = $entity->get($heading_field)->view('linkbox');

    return [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => $heading[0] ?? NULL,
      '#items' => array_intersect_key(
        $links,
        array_flip(Element::children($links)),
      ),
      '#weight' => $links['#weight'],
    ];
  }

  /**
   * Generate link render arrays from field data.
   *
   * Sets entity title if known and property is empty and provide entity in
   * the Url object options (url.options.entity).
   */
  protected function prepareLinks(FieldItemListInterface $links): void {
    /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $link */
    foreach ($links as $link) {
      if ($link->isExternal()) {
        continue;
      }

      $route_name = $link->getUrl()->getRouteName();
      if (preg_match('/entity\.([^.]+)\.canonical/', $route_name)) {
        $route_params = $link->getUrl()->getRouteParameters();
        try {
          $entity = $this->entityTypeManager->getStorage(key($route_params))
            ->load(reset($route_params));
        }
        catch (\Throwable $th) {
          $entity = NULL;
        }

        if ($entity) {
          if ($links->getName() === 'field_downloads' && $entity->getEntityTypeId() === 'media') {
            // Reroute to download link.
            $url = Url::fromRoute('media_entity_download.download', [
              'media' => $entity->id(),
            ]);
            $link->uri = 'internal:/' . $url->getInternalPath();
          }
          $link->title = $link->getTitle() ?: $entity->label();
          $link->options = ['entity' => $entity] + $link->options;
        }
      }
    }
  }

}
