<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Hook;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\csn_custom\Form\FirstResponderContentForm;
use Drupal\taxonomy\TermInterface;

/**
 * Hooks related to Incident Report nodes.
 */
class IncidentReportHooks {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Contains mappings for conditionally shown fields.
   *
   * Used to show <field_name> when <depends_on> has term <key> selected.
   *
   * @see csn_custom/content/taxonomy_term/incident_types.
   */
  private static array $conditionalFields = [
    'bf7eb534-025e-4ca5-a629-0912327ef935' => [
      'field_name' => 'field_incident_types_other',
      'depends_on' => 'field_incident_types',
    ],
    'f1a4da8e-d540-45a3-b476-d7e80713f03c' => [
      'field_name' => 'field_incident_types_abuse',
      'depends_on' => 'field_incident_types',
    ],
    '5b503d77-8d05-4abd-97ce-6f56f6d93df2' => [
      'field_name' => 'field_incident_types_abuse_other',
      'depends_on' => 'field_incident_types_abuse',
    ],
  ];

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node_form.
   */
  #[Hook('form_node_form_alter')]
  public function alterNodeForm(array &$form, FormStateInterface $formState, string $formId): void {
    $entity = $formState->getFormObject()->getEntity();
    if (!$entity || $entity->bundle() !== 'incident_report') {
      return;
    }

    /** @var array<int, \Drupal\taxonomy\TermInterface> $terms */
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'vid' => 'incident_types',
      'uuid' => array_keys(self::$conditionalFields),
    ]);
    $terms = array_combine(array_map(static fn(TermInterface $term) => $term->uuid(), $terms), $terms);

    foreach (static::$conditionalFields as $uuid => $conditional_field) {
      if (!isset($form[$conditional_field['field_name']]) || !isset($terms[$uuid])) {
        continue;
      }

      // @note 'required' state is picky on complex fields, rely on validation.
      // @see https://www.drupal.org/docs/drupal-apis/form-api/conditional-form-fields.
      $form[$conditional_field['field_name']]['#states']['visible'] = [
        [':input[name="' . $conditional_field['depends_on'] . '"]' => ['visible' => TRUE]],
        [':input[name="' . $conditional_field['depends_on'] . '[' . $terms[$uuid]->id() . ']"]' => ['checked' => TRUE]],
      ];
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_insert() for node.
   */
  #[Hook('node_insert')]
  public function nodeInsert(EntityInterface $entity): void {
    if ($entity->bundle() === 'incident_report') {
      // @todo Change to DEH user/profile label (e.g. `DEH-1234`) + node id.
      $entity->title = sprintf('DEH-%s-%s', $entity->getOwnerId(), $entity->id());
      $entity->save();
    }
  }

  /**
   * Implements hook_help().
   *
   * @see \Drupal\node\Hook\NodeHelpHooks::help
   */
  #[Hook('help')]
  public function help($route_name, RouteMatchInterface $route_match): ?string {
    if ($route_name === 'csn_custom.incident_report.add') {
      $type = $route_match->getParameter('node_type');
      $help = $type->getHelp();
      return !empty($help) ? Xss::filterAdmin($help) : '';
    }
    return NULL;
  }

  /**
   * Implements hook_entity_type_build().
   *
   * @param array<string, \Drupal\Core\Entity\EntityTypeInterface> $entity_types
   *   The entity types known to Drupal.
   */
  #[Hook('entity_type_build')]
  public function buildEntityType(array &$entity_types) : void {
    $entity_types['node']->addConstraint('ConditionalFields', [
      'conditionalFields' => static::$conditionalFields,
    ]);

    $entity_types['node']->setFormClass(
      'first_responder',
      FirstResponderContentForm::class,
    );
  }

}
