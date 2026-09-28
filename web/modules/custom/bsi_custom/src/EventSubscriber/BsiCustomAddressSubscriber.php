<?php

declare(strict_types=1);

namespace Drupal\bsi_custom\EventSubscriber;

use CommerceGuys\Addressing\AddressFormat\AdministrativeAreaType;
use Drupal\address\Event\AddressEvents;
use Drupal\address\Event\AddressFormatEvent;
use Drupal\address\Event\SubdivisionsEvent;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Enable administrative area input for German addresses.
 */
final class BsiCustomAddressSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      AddressEvents::ADDRESS_FORMAT => ['onAddressFormat'],
      AddressEvents::SUBDIVISIONS => ['onSubdivisions'],
    ];
  }

  /**
   * Add the administrative area (Bundesland) to the address format.
   */
  public function onAddressFormat(AddressFormatEvent $event): void {
    $definition = $event->getDefinition();
    if ($definition['country_code'] == 'DE') {
      $definition['format'] = $definition['format'] . "\n%administrativeArea";
      $definition['administrative_area_type'] = AdministrativeAreaType::STATE;
      $definition['subdivision_depth'] = 1;
      $event->setDefinition($definition);
    }
  }

  /**
   * Add administrative areas (Bundesland) for Germany.
   */
  public function onSubdivisions(SubdivisionsEvent $event): void {
    $options = [
      'context' => 'German state',
    ];

    if ($event->getParents() === ['DE']) {
      $event->setDefinitions([
        'country_code' => 'DE',
        'subdivisions' => [
          'BW' => [
            'local_name' => 'Baden-Württemberg',
            'name' => $this->t('Baden-Württemberg', [], $options),
          ],
          'BY' => [
            'local_name' => 'Bayern',
            'name' => $this->t('Bavaria', [], $options),
          ],
          'BE' => [
            'local_name' => 'Berlin',
            'name' => $this->t('Berlin', [], $options),
          ],
          'BB' => [
            'local_name' => 'Brandenburg',
            'name' => $this->t('Brandenburg', [], $options),
          ],
          'HB' => [
            'local_name' => 'Bremen',
            'name' => $this->t('Bremen', [], $options),
          ],
          'HH' => [
            'local_name' => 'Hamburg',
            'name' => $this->t('Hamburg', [], $options),
          ],
          'HE' => [
            'local_name' => 'Hessen',
            'name' => $this->t('Hesse', [], $options),
          ],
          'NI' => [
            'local_name' => 'Niedersachsen',
            'name' => $this->t('Lower Saxony', [], $options),
          ],
          'MV' => [
            'local_name' => 'Mecklenburg-Vorpommern',
            'name' => $this->t('Mecklenburg-Western Pomerania', [], $options),
          ],
          'NW' => [
            'local_name' => 'Nordrhein-Westfalen',
            'name' => $this->t('North Rhine-Westphalia', [], $options),
          ],
          'RP' => [
            'local_name' => 'Rheinland-Pfalz',
            'name' => $this->t('Rhineland-Palatinate', [], $options),
          ],
          'SH' => [
            'local_name' => 'Schleswig-Holstein',
            'name' => $this->t('Schleswig-Holstein', [], $options),
          ],
          'SL' => [
            'local_name' => 'Saarland',
            'name' => $this->t('Saarland', [], $options),
          ],
          'SN' => [
            'local_name' => 'Sachsen',
            'name' => $this->t('Saxony', [], $options),
          ],
          'ST' => [
            'local_name' => 'Sachsen-Anhalt',
            'name' => $this->t('Saxony-Anhalt', [], $options),
          ],
          'TH' => [
            'local_name' => 'Thüringen',
            'name' => $this->t('Thuringia', [], $options),
          ],
        ],
      ]);
    }
  }

}
