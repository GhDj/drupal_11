<?php

/**
 * @file
 * Creates test content for BSIR-131: Download paragraph with Publication thumbnail.
 *
 * Usage: drush php:script scripts/bsir-131-test-content.php
 *
 * Creates:
 * - 1 Publication media (with a cover image + PDF file)
 * - 1 Download media (with a PDF file, no cover)
 * - 1 Page node with a Downloads paragraph referencing both
 *
 * After running, visit the created node to verify:
 * - Publication thumbnail shows in portrait/cover format (not squared)
 * - Download shows the document icon (no thumbnail)
 * - Image is served via bsi_download style (inspect <picture> sources)
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

// --- 1. Create a dummy PDF file for both media items ---
$pdf_content = "%PDF-1.4 dummy test file";
$pdf_dir = 'public://test-bsir-131';
\Drupal::service('file_system')->prepareDirectory($pdf_dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);

$pdf_file = File::create([
  'uri' => $pdf_dir . '/test-publication.pdf',
  'filename' => 'test-publication.pdf',
  'filemime' => 'application/pdf',
  'status' => 1,
]);
\Drupal::service('file_system')->saveData($pdf_content, $pdf_file->getFileUri(), \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
$pdf_file->save();

$pdf_file2 = File::create([
  'uri' => $pdf_dir . '/test-download.pdf',
  'filename' => 'test-download.pdf',
  'filemime' => 'application/pdf',
  'status' => 1,
]);
\Drupal::service('file_system')->saveData($pdf_content, $pdf_file2->getFileUri(), \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
$pdf_file2->save();

// --- 2. Create a cover image for the publication ---
// Generate a simple 400x600 portrait image (red rectangle).
$img = imagecreatetruecolor(400, 600);
$red = imagecolorallocate($img, 200, 30, 30);
$white = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $red);
imagestring($img, 5, 100, 280, 'BSI Cover Test', $white);
$img_path = $pdf_dir . '/test-cover.png';
$img_real_path = \Drupal::service('file_system')->realpath('public://') . '/test-bsir-131/test-cover.png';
imagepng($img, $img_real_path);
imagedestroy($img);

$cover_file = File::create([
  'uri' => $img_path,
  'filename' => 'test-cover.png',
  'filemime' => 'image/png',
  'status' => 1,
]);
$cover_file->save();

// --- 3. Create Publication media (with cover thumbnail + PDF) ---
$publication = Media::create([
  'bundle' => 'publication',
  'name' => 'BSIR-131 Test Publikation - BSI Magazin',
  'status' => 1,
  'langcode' => 'de',
  'field_media_file' => [
    'target_id' => $pdf_file->id(),
    'display' => 1,
    'description' => 'BSIR-131 Test Publikation - BSI Magazin',
  ],
  'field_body' => [
    'value' => 'Dies ist eine Testpublikation fuer BSIR-131. Das Vorschaubild sollte im Hochformat (Portrait) angezeigt werden, nicht quadratisch zugeschnitten.',
    'format' => 'plain_text',
  ],
  'thumbnail' => [
    'target_id' => $cover_file->id(),
    'alt' => 'BSI Magazin Cover',
  ],
]);
$publication->save();

echo "Created Publication media: " . $publication->id() . " (" . $publication->label() . ")\n";

// --- 4. Create Download media (icon only, no cover) ---
$download = Media::create([
  'bundle' => 'download',
  'name' => 'BSIR-131 Test Download - PDF Beschreibung',
  'status' => 1,
  'langcode' => 'de',
  'field_media_file' => [
    'target_id' => $pdf_file2->id(),
    'display' => 1,
    'description' => 'BSIR-131 Test Download - PDF Beschreibung',
  ],
  'field_body' => [
    'value' => 'Dies ist ein Test-Download fuer BSIR-131. Hier sollte ein Dokument-Icon angezeigt werden, kein Vorschaubild.',
    'format' => 'plain_text',
  ],
]);
$download->save();

echo "Created Download media: " . $download->id() . " (" . $download->label() . ")\n";

// --- 5. Create Downloads paragraph referencing both media items ---
$paragraph = Paragraph::create([
  'type' => 'downloads',
  'field_section_title' => 'Downloads (BSIR-131 Test)',
  'field_media_items' => [
    ['target_id' => $publication->id()],
    ['target_id' => $download->id()],
  ],
]);
$paragraph->save();

echo "Created Downloads paragraph: " . $paragraph->id() . "\n";

// --- 6. Create a Page node with the paragraph ---
$node = Node::create([
  'type' => 'page',
  'title' => 'BSIR-131 Test - Download Thumbnail Fix',
  'status' => 1,
  'langcode' => 'de',
  'field_paragraphs' => [
    [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ],
  ],
]);
$node->save();

echo "\n";
echo "=== BSIR-131 Test Content Created ===\n";
echo "Node: /node/" . $node->id() . "\n";
echo "\n";
echo "What to verify:\n";
echo "  1. Publication (first item): cover image in PORTRAIT format, not squared\n";
echo "  2. Download (second item): document icon in a square frame\n";
echo "  3. Inspect <picture> sources: should reference bsi_download style, not bsi_content\n";
