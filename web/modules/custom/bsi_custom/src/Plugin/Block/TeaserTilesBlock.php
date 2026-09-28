<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Plugin\Block;

use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\views\ResultRow;
use Drupal\views\Views;

/**
 * Provides a teaser tiles block.
 */
#[Block(
  id: 'bsi_custom_teaser_tiles',
  admin_label: new TranslatableMarkup('Teaser tiles'),
  category: new TranslatableMarkup('BSI'),
  context_definitions: [
    'entity' => new EntityContextDefinition('entity:node'),
  ],
)]
final class TeaserTilesBlock extends BlockBase implements ContainerFactoryPluginInterface {

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
      $access_result->andIf(new AccessResultForbidden('No entity found.'));
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
    $entities = $entity->hasField('field_teaser_tiles')
      ? $entity->get('field_teaser_tiles')->referencedEntities()
      : [];

    if (!$entities) {
      $results = Views::getViewResult('menu_items', 'children')
        ?: Views::getViewResult('menu_items', 'siblings')
        ?: Views::getViewResult('content_tiles', 'default');
      $entities = array_map(static fn(ResultRow $result) => $result->_relationship_entities['node_id'] ?? $result->_entity, $results);
    }
    if (!$entities) {
      return [];
    }

    // @todo Use view mode as configured in view.
    $build['#entity_type'] = reset($entities)->getEntityTypeId();
    $build['items'] = $this->entityTypeManager->getViewBuilder($build['#entity_type'])
      ->viewMultiple($entities, 'teaser_tile');

    return array_filter($build);
  }

}
