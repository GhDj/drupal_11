<?php

namespace Drupal\bsi_custom\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\group\Plugin\Group\Relation\GroupRelationTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides derivative plugins for the EntityField condition plugin.
 */
class EntityGroupConditionDeriver extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * Creates a EntityFieldConditionDeriver object.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly GroupRelationTypeManagerInterface $groupRelationTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('group_relation_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    $entity_types = array_filter($this->entityTypeManager->getDefinitions(),
      fn (EntityTypeInterface $entity_type): bool
      => (bool) $this->groupRelationTypeManager->getPluginIdsByEntityTypeId($entity_type->id()),
    );
    foreach ($entity_types as $entity_type_id => $entity_type) {
      $this->derivatives[$entity_type_id] = $base_plugin_definition;
      $this->derivatives[$entity_type_id]['context_definitions']['entity'] = new EntityContextDefinition(
        $entity_type_id,
        $entity_type->getLabel(),
      );
      $this->derivatives[$entity_type_id]['label'] = $this->t('@entity_type group relation', [
        '@entity_type' => $entity_type->getLabel(),
      ]);
      $this->derivatives[$entity_type_id]['base_plugin_label'] = (string) $base_plugin_definition['label'];
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
