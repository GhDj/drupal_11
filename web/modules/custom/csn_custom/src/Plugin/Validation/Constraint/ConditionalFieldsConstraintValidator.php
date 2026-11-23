<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Plugin\Validation\Constraint;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Conditional fields constraint.
 */
final class ConditionalFieldsConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $entity, Constraint $constraint): void {
    if (!$entity instanceof EntityInterface) {
      throw new \InvalidArgumentException(
        sprintf('The validated value must be instance of \Drupal\Core\Entity\EntityInterface, %s was given.', get_debug_type($entity))
      );
    }
    if ($entity->getEntityTypeId() !== 'node' || $entity->bundle() !== 'incident_report') {
      return;
    }

    foreach ($constraint->conditionalFields as $uuid => $conditional_field) {
      // @todo Also ensure field is empty when depends_on.value is not checked?
      if (!$entity->hasField($conditional_field['field_name'])
        || !$entity->hasField($conditional_field['depends_on'])
        || !$entity->get($conditional_field['field_name'])->isEmpty()
        || $entity->get($conditional_field['depends_on'])->isEmpty()) {
        continue;
      }

      /** @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items */
      $items = $entity->get($conditional_field['depends_on']);
      $referencedEntities = $items->referencedEntities();

      $found = array_find($referencedEntities, static fn(EntityInterface $referencedEntity)
        => $referencedEntity->uuid() === $uuid);
      if ($found) {
        $this->context->buildViolation($constraint->message)
          ->atPath($conditional_field['field_name'])
          ->setParameter('%field_name', $entity->get($conditional_field['field_name'])
            ->getFieldDefinition()->getLabel())
          ->setParameter('%depends_on', $entity->get($conditional_field['depends_on'])
            ->getFieldDefinition()->getLabel())
          ->setParameter('%value', $found->label())
          ->addViolation();
      }
    }
  }

}
