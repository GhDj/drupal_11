<?php

namespace Drupal\bsi_media;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\BundlePermissionHandlerTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\media\MediaTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions for each media type.
 */
class MediaDownloadPermissions implements ContainerInjectionInterface {
  use BundlePermissionHandlerTrait;
  use StringTranslationTrait;

  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Returns an array of media type permissions.
   *
   * @return array
   *   The media type permissions.
   *
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function mediaTypePermissions(): array {
    // Generate media permissions for all media types.
    /** @var array<string, \Drupal\media\MediaTypeInterface> $media_types */
    $media_types = $this->entityTypeManager->getStorage('media_type')
      ->loadMultiple();
    $media_types = array_filter($media_types, static fn (MediaTypeInterface $mediaType) =>
      $mediaType->getSource()->getSourceFieldDefinition($mediaType)
        ->getFieldStorageDefinition()->getSetting('target_type') === 'file'
    );

    return $this->generatePermissions($media_types, [$this, 'buildPermissions']);
  }

  /**
   * Returns a list of media permissions for a given media type.
   *
   * @param \Drupal\media\MediaTypeInterface $type
   *   The media type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(MediaTypeInterface $type): array {
    $type_id = $type->id();
    $type_params = ['%type_name' => $type->label()];

    return [
      "download $type_id media" => [
        'title' => $this->t('%type_name: Download file', $type_params),
      ],
    ];
  }

}
