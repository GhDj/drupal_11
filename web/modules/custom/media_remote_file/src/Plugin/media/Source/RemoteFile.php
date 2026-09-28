<?php

namespace Drupal\media_remote_file\Plugin\media\Source;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\field\FieldConfigInterface;
use Drupal\link\LinkItemInterface;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use Drupal\media\MediaTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mime\MimeTypeGuesserInterface;

/**
 * Remote file entity media source.
 */
#[MediaSource(
  id: 'remote_file',
  label: new TranslatableMarkup('Remote file'),
  description: new TranslatableMarkup('Use remote files for video and audio.'),
  allowed_field_types: ['link'],
  default_thumbnail_filename: 'generic.png',
  thumbnail_alt_metadata_attribute: 'default_name',
)]
class RemoteFile extends MediaSourceBase {

  /**
   * Key for "Name" metadata attribute.
   *
   * @var string
   */
  const string METADATA_ATTRIBUTE_NAME = 'name';

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    string $plugin_id,
    array $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    FieldTypePluginManagerInterface $field_type_manager,
    ConfigFactoryInterface $config_factory,
    protected readonly MimeTypeGuesserInterface $mimeTypeGuesser,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $entity_field_manager, $field_type_manager, $config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('config.factory'),
      $container->get('file.mime_type.guesser')
    );
  }

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
    /** @var \Drupal\Core\Field\FieldItemListInterface&LinkItemInterface $source_field */
    $source_field = $media->get($this->configuration['source_field']);
    if ($source_field->isEmpty()) {
      return parent::getMetadata($media, $attribute_name);
    }

    switch ($attribute_name) {
      case static::METADATA_ATTRIBUTE_NAME:
      case 'default_name':
        return $media->getName();

      case 'thumbnail_uri':
        $iconName = $this->getMultimediaType($source_field);
        return $this->configFactory->get('media.settings')->get('icon_base_uri') . '/' . $iconName;

      case self::METADATA_ATTRIBUTE_LINK_TARGET:
        return parent::getMetadata($media, $attribute_name)
          ?: $source_field->getUrl()->setAbsolute(TRUE);

      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

  /**
   * Determine whether a file field contains audio or video media.
   */
  private function getMultimediaType(FieldItemListInterface $source_field): ?string {
    /** @todo Support file uris with fragments or query, maybe via HEAD-based guesser? */
    $mimeType = $this->mimeTypeGuesser->guessMimeType($source_field->uri);

    return match (TRUE) {
      $mimeType && str_starts_with($mimeType, 'video/') => 'video.png',
      $mimeType && str_starts_with($mimeType, 'audio/') => 'audio.png',
      default => $this->pluginDefinition['default_thumbnail_filename'],
    };
  }

  /**
   * {@inheritdoc}
   */
  public function createSourceField(MediaTypeInterface $type): FieldConfigInterface {
    return parent::createSourceField($type)->set('settings', [
      'title' => FALSE,
      'link_type' => LinkItemInterface::LINK_EXTERNAL,
    ]);
  }

}
