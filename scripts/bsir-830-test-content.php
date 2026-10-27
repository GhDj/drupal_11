<?php

/**
 * @file
 * Creates test content for BSIR-830: Alt text optional on thumbnails.
 *
 * Usage: drush php:script scripts/bsir-830-test-content.php
 *
 * Creates a Publication and a Download media with empty alt text
 * on their thumbnails to verify:
 * - Save succeeds without alt text (field is no longer required)
 * - Rendered HTML has alt="" on the thumbnail image
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

$pdf_content = "%PDF-1.4 dummy test file";
$dir = 'public://test-bsir-830';
\Drupal::service('file_system')->prepareDirectory($dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);

// Create a PDF file.
$pdf_uri = $dir . '/test.pdf';
\Drupal::service('file_system')->saveData($pdf_content, $pdf_uri, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
$pdf_file = File::create([
  'uri' => $pdf_uri,
  'filename' => 'test.pdf',
  'filemime' => 'application/pdf',
  'status' => 1,
]);
$pdf_file->save();

// Create a cover image (portrait).
$img = imagecreatetruecolor(400, 600);
$blue = imagecolorallocate($img, 0, 51, 153);
$white = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $blue);
imagestring($img, 5, 80, 280, 'BSIR-830 Alt Test', $white);
$img_real_path = \Drupal::service('file_system')->realpath('public://') . '/test-bsir-830/cover.png';
imagepng($img, $img_real_path);
imagedestroy($img);

$cover_file = File::create([
  'uri' => $dir . '/cover.png',
  'filename' => 'cover.png',
  'filemime' => 'image/png',
  'status' => 1,
]);
$cover_file->save();

// --- Publication with EMPTY alt text ---
$publication = Media::create([
  'bundle' => 'publication',
  'name' => 'BSIR-830 Test Publikation (leerer Alt-Text)',
  'status' => 1,
  'langcode' => 'de',
  'field_media_file' => [
    'target_id' => $pdf_file->id(),
    'display' => 1,
    'description' => 'BSIR-830 Test Publikation',
  ],
]);
$publication->save();

// Set thumbnail with empty alt (second save so media_thumbnails doesn't overwrite).
$publication->set('thumbnail', [
  'target_id' => $cover_file->id(),
  'alt' => '',
]);
$publication->save();

echo "Publication media: /media/" . $publication->id() . "/edit\n";

// --- Download with EMPTY alt text ---
$pdf_file2 = File::create([
  'uri' => $dir . '/test-dl.pdf',
  'filename' => 'test-dl.pdf',
  'filemime' => 'application/pdf',
  'status' => 1,
]);
\Drupal::service('file_system')->saveData($pdf_content, $pdf_file2->getFileUri(), \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
$pdf_file2->save();

$download = Media::create([
  'bundle' => 'download',
  'name' => 'BSIR-830 Test Download (leerer Alt-Text)',
  'status' => 1,
  'langcode' => 'de',
  'field_media_file' => [
    'target_id' => $pdf_file2->id(),
    'display' => 1,
    'description' => 'BSIR-830 Test Download',
  ],
]);
$download->save();

echo "Download media:    /media/" . $download->id() . "/edit\n";

echo "\nWhat to verify:\n";
echo "  1. Edit each media -> alt text field is NOT required\n";
echo "  2. Save with empty alt -> succeeds without error\n";
echo "  3. View rendered output -> <img alt=\"\"> in HTML\n";
