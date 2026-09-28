<?php

namespace Drupal\bsi_charts\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;

/**
 * Chart entity media source.
 */
#[MediaSource(
  id: 'chart',
  label: new TranslatableMarkup('Chart'),
  description: new TranslatableMarkup('Use charts configuration as reusable media.'),
  default_thumbnail_filename: 'chart.png',
  allowed_field_types: ['chart_config'],
)]
class Chart extends MediaSourceBase {

  /**
   * Key for "Name" metadata attribute.
   *
   * @var string
   */
  const METADATA_ATTRIBUTE_NAME = 'name';

  /**
   * Key for "type" metadata attribute.
   *
   * @var string
   */
  const METADATA_ATTRIBUTE_TYPE = 'type';

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {
    return [
      static::METADATA_ATTRIBUTE_NAME => $this->t('Name'),
      static::METADATA_ATTRIBUTE_TYPE => $this->t('Type'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    $field = $media->get($this->configuration['source_field']);
    if ($field->isEmpty()) {
      return parent::getMetadata($media, $attribute_name);
    }

    switch ($attribute_name) {
      case static::METADATA_ATTRIBUTE_NAME:
      case 'default_name':
        return $media->getName();

      case static::METADATA_ATTRIBUTE_TYPE:
        return $field->type;

      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

}
