<?php

namespace Drupal\short_url\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides block plugin definitions for short URL entity types.
 *
 * @see \Drupal\system\Plugin\Block\SystemMenuBlock
 */
class ShortUrlBlockDeriver extends DeriverBase implements ContainerDeriverInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    $entity_types = array_intersect_key(
      $this->entityTypeManager->getDefinitions(),
      array_flip($this->configFactory->get('short_url.settings')->get('entity_types') ?? []),
    );
    foreach ($entity_types as $entity_type_id => $entity_type) {
      $this->derivatives[$entity_type_id] = $base_plugin_definition;
      $this->derivatives[$entity_type_id]['label'] = $entity_type->getLabel();
      $this->derivatives[$entity_type_id]['provider'] = $entity_type->getProvider();
      $this->derivatives[$entity_type_id]['context_definitions'] = [
        'entity' => EntityContextDefinition::fromEntityType($entity_type),
      ];
    }
    return $this->derivatives;
  }

}
