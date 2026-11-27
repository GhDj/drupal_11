<?php

declare(strict_types=1);

namespace Drupal\csn_custom\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Provides a Conditional fields constraint.
 *
 * @see https://www.drupal.org/node/2015723.
 */
#[Constraint(
  id: 'ConditionalFields',
  label: new TranslatableMarkup('Conditional fields', options: ['context' => 'Validation'])
)]
final class ConditionalFieldsConstraint extends SymfonyConstraint {

  /**
   * The error message.
   */
  public string $message = 'Field %field_name is required when %depends_on is set to %value.';

  /**
   * The configuration for conditional fields.
   *
   * @see \Drupal\csn_custom\Hook\IncidentReportHooks::$conditionalFields
   */
  public array $conditionalFields = [];

}
