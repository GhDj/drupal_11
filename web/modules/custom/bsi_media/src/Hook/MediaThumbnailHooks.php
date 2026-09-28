<?php

declare(strict_types=1);

namespace Drupal\bsi_media\Hook;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\media\MediaInterface;

/**
 * Hooks related to media thumbnail generation.
 */
class MediaThumbnailHooks {

  use DependencySerializationTrait;

  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_entity_base_field_info_alter().
   */
  #[Hook('entity_base_field_info_alter')]
  public function alterMediaThumbnailField(array &$fields, EntityTypeInterface $entity_type): void {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    if ($entity_type->id() !== 'media' || empty($fields['thumbnail'])) {
      return;
    }

    // Allow editors to override the thumbnail.
    $fields['thumbnail']->setDisplayConfigurable('form', TRUE);

    // As files are stored privately, also store file thumbnails that way.
    $fields['thumbnail']->setSetting('uri_scheme', 'private');
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for media_form.
   */
  #[Hook('form_media_form_alter')]
  public function alterMediaForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    /** @var \Drupal\media\MediaInterface $entity */
    $entity = $form_state->getFormObject()->getEntity();
    if ($entity->bundle() === 'bits') {
      $form['#entity_builders'][] = [$this, 'bitsThumbnailBuilder'];
    }
  }

  /**
   * Entity builder for bits media to set thumbnail based on criticality.
   */
  public function bitsThumbnailBuilder($entity_type, MediaInterface $media, &$form, FormStateInterface $form_state): void {
    $path = \Drupal::config('media.settings')->get('icon_base_uri');

    $criticality = $media->get('field_criticality')->value;
    $icon = $path . DIRECTORY_SEPARATOR . 'criticality-' . $criticality . '.png';

    $media->set('thumbnail', $this->loadThumbnail($media, $icon));
  }

  /**
   * Loads or creates a file entity for the given thumbnail/icon.
   *
   * @see \Drupal\media\Entity\Media::loadThumbnail
   */
  protected function loadThumbnail(MediaInterface $media, string $thumbnail_uri) {
    $file_storage = $this->entityTypeManager->getStorage('file');

    $values = [
      'uri' => $thumbnail_uri,
    ];

    $existing = $file_storage->loadByProperties($values);
    if ($existing) {
      return reset($existing);
    }
    /** @var \Drupal\file\FileInterface $file */
    $file = $file_storage->create($values);
    if ($owner = $media->getOwner()) {
      $file->setOwner($owner);
    }
    $file->setPermanent();
    $file->save();

    return $file;
  }

}
