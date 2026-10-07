<?php

/**
 * @file
 * Post update hooks for BSI media module.
 */

/**
 * Populate publication date from media created date.
 */
function bsi_media_post_update_set_publication_date_from_created(&$sandbox): void {

  if (!isset($sandbox['total'])) {

    $sandbox['mids'] = array_values(
      \Drupal::entityQuery('media')
        ->accessCheck(FALSE)
        ->condition('bundle', ['publication', 'download'], 'IN')
        ->execute()
    );

    $sandbox['total'] = count($sandbox['mids']);
    $sandbox['current'] = 0;
  }

  $batch_size = 50;

  $mids = array_slice(
    $sandbox['mids'],
    $sandbox['current'],
    $batch_size
  );

  if ($mids) {

    /** @var \Drupal\media\MediaStorage $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('media');

    /** @var \Drupal\media\Entity\Media[] $media_entities */
    $media_entities = $storage->loadMultiple($mids);

    foreach ($media_entities as $media) {

      $created_date = date(
        'Y-m-d',
        $media->getUntranslated()->getCreatedTime()
      );

      // Update every translation.
      foreach ($media->getTranslationLanguages() as $langcode => $language) {

        if (!$media->hasTranslation($langcode)) {
          continue;
        }

        /** @var \Drupal\media\MediaInterface $translation */
        $translation = $media->getTranslation($langcode);

        if (!$translation->hasField('field_publication_date')) {
          continue;
        }

        $translation->set(
          'field_publication_date',
          $created_date
        );
      }

      $media->save();

      $sandbox['current']++;
    }
  }

  $sandbox['#finished'] = $sandbox['total']
    ? $sandbox['current'] / $sandbox['total']
    : 1;
}
