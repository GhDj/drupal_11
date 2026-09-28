<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\Hook;

use Drupal\Core\Entity\ContentEntityFormInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\group\Plugin\Group\Relation\GroupRelationTypeManagerInterface;

/**
 * Hooks related to access restriction indicators.
 */
class AccessRestrictionsHooks {

  use StringTranslationTrait;

  const string NEW_ENTITY_ID_PLACEHOLDER = '';

  public function __construct(
    protected readonly EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    protected readonly GroupRelationTypeManagerInterface $relationTypeManager,
    protected readonly AccountProxyInterface $currentUser,
    protected readonly RouteMatchInterface $routeMatch,
  ) {
  }

  /**
   * Implements hook_entity_extra_field_info().
   */
  #[Hook('entity_extra_field_info')]
  public function addExtraField(): array {
    /** @var array<string, \Drupal\group\Plugin\Group\Relation\GroupRelationTypeInterface> $installed_definitions */
    $installed_definitions = array_intersect_key(
      $this->relationTypeManager->getDefinitions(),
      array_flip($this->relationTypeManager->getAllInstalledIds()),
    );

    $extra_field = [
      'label' => $this->t('Access restrictions'),
      'description' => $this->t('The groups that restrict access to this content.'),
      'weight' => 0,
      'visible' => FALSE,
    ];

    $extra = [];
    foreach ($installed_definitions as $definition) {
      $extra[$definition->getEntityTypeId()][$definition->getEntityBundle()] = [
        'form' => [
          'access_restrictions' => $extra_field,
        ],
        'display' => [
          'access_restrictions' => $extra_field,
        ],
      ];
    }
    return $extra;
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function alterForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof ContentEntityFormInterface) {
      return;
    }
    $display = $form_object->getFormDisplay($form_state);
    if ($display->getTargetEntityTypeId() === 'group_relationship'
      || $display->getOriginalMode() === 'delete'
      || !($component = $display->getComponent('access_restrictions'))) {
      return;
    }
    $form['access_restrictions'] = [
      '#type' => 'item',
      '#title' => $this->t('Access restrictions'),
      '#weight' => $component['weight'],
    ];
    $form['access_restrictions'][] = $this->build($form_object->getEntity());
  }

  /**
   * Implements hook_entity_view().
   */
  #[Hook('entity_view')]
  public function prepareView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode): void {
    if (!$component = $display->getComponent('access_restrictions')) {
      return;
    }
    $build['access_restrictions'] = $this->build($entity) + [
      '#title' => $this->t('Access restrictions'),
      '#weight' => $component['weight'],
      '#access' => $this->currentUser->hasPermission('view access restrictions'),
    ];
  }

  /**
   * Get relationships for the given entity.
   *
   * @return array<\Drupal\group\Entity\GroupRelationshipInterface>
   *   The entity's group relationships.
   */
  protected function getRelationships(EntityInterface $entity): array {
    /** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('group_relationship');
    $relationships = $storage->loadByEntity($entity);

    if ($this->routeMatch->getRouteName() === 'entity.group_relationship.create_form') {
      $group = $this->routeMatch->getParameter('group');
      $plugin_id = $this->routeMatch->getParameter('plugin_id');
      $entity->{$entity->getEntityType()->getKey('id')} = static::NEW_ENTITY_ID_PLACEHOLDER;
      $relationships[$entity->id()] = $storage->createForEntityInGroup($entity, $group, $plugin_id);
    }

    return $relationships;
  }

  /**
   * Build a renderable array for the entity's groups.
   */
  protected function build(EntityInterface $entity): array {
    $build = [
      '#theme' => 'item_list__access_restrictions',
      '#context' => ['list_style' => 'comma-list'],
      '#empty' => $this->t('Unrestricted'),
    ];

    $relationships = $this->getRelationships($entity);
    foreach ($relationships as $relationship) {
      $build['#items'][$relationship->id()] = $relationship->getGroup()->toLink()->toString();
    }
    return $build;
  }

}
