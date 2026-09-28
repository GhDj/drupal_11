<?php

namespace Drupal\bsi_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;

/**
 * Address media source.
 */
#[MediaSource(
  id: 'address',
  label: new TranslatableMarkup('Address'),
  description: new TranslatableMarkup('Media type based on address field data.'),
  allowed_field_types: ['address'],
  default_thumbnail_filename: 'address.png',
)]
class Address extends MediaSourceBase {

  /**
   * Key for "Name" metadata attribute.
   *
   * @var string
   */
  const string METADATA_ATTRIBUTE_NAME = 'name';

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes(): array {
    return [
      static::METADATA_ATTRIBUTE_NAME => $this->t('Name'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    /** @var \Drupal\address\Plugin\Field\FieldType\AddressItem $source_field */
    $source_field = $media->get($this->configuration['source_field']);
    if ($source_field->isEmpty()) {
      return parent::getMetadata($media, $attribute_name);
    }

    switch ($attribute_name) {
      case static::METADATA_ATTRIBUTE_NAME:
        $name = trim(implode(' ', [
          $source_field->getOrganization(),
          $source_field->getFamilyName(),
          $source_field->getGivenName(),
        ]), ', ');
        if ($name) {
          return $name;
        }
        // Could not determine name, try using the default.
      case 'default_name':
        return $media->getName();

      case self::METADATA_ATTRIBUTE_LINK_TARGET:
      case 'thumbnail_uri':
      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

}
