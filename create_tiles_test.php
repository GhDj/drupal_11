<?php

/**
 * @file
 * Creates a test report_page with tiles paragraphs for BSIR-205 QA testing.
 *
 * Usage: drush php:script create_tiles_test.php
 *
 * Creates one report_page node with 3 tiles paragraphs (one per color theme),
 * each containing tile_items covering all test scenarios:
 * - Short body (no [more] expected)
 * - Long body >300 chars ([more] + lightbox expected)
 * - Summary + body ([more] + lightbox expected)
 * - With image media ([more] + lightbox, maximize icon test)
 * - Kennzahl variants
 */

use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

// -- Helper: create a placeholder image using GD --
function _create_test_image(string $label, string $bg_color = '#3366CC'): ?Media {
  // Generate a 800x500 placeholder image with PHP GD.
  $width = 800;
  $height = 500;
  $img = imagecreatetruecolor($width, $height);

  // Parse hex color.
  $r = hexdec(substr($bg_color, 1, 2));
  $g = hexdec(substr($bg_color, 3, 2));
  $b = hexdec(substr($bg_color, 5, 2));
  $bg = imagecolorallocate($img, $r, $g, $b);
  $white = imagecolorallocate($img, 255, 255, 255);
  $dark = imagecolorallocate($img, 50, 50, 50);

  imagefill($img, 0, 0, $bg);

  // Draw a simple bar chart mockup.
  $bars = [180, 280, 220, 350, 300, 250, 320];
  $bar_width = 60;
  $gap = 30;
  $start_x = 100;
  foreach ($bars as $i => $bar_height) {
    $x1 = $start_x + $i * ($bar_width + $gap);
    $y1 = $height - 60 - $bar_height;
    $x2 = $x1 + $bar_width;
    $y2 = $height - 60;
    imagefilledrectangle($img, $x1, $y1, $x2, $y2, $white);
  }

  // Add label text.
  imagestring($img, 5, 100, 30, $label, $white);
  imagestring($img, 3, 100, 55, 'Testbild fuer BSIR-205', $dark);

  // Save to Drupal's public files.
  $directory = 'public://image/' . date('Y-m');
  \Drupal::service('file_system')->prepareDirectory($directory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);

  $filename = 'bsir205-test-' . preg_replace('/[^a-z0-9]/', '-', strtolower($label)) . '.png';
  $filepath = $directory . '/' . $filename;
  $real_path = \Drupal::service('file_system')->realpath($directory) . '/' . $filename;

  // Ensure the directory for realpath exists.
  $dir = dirname($real_path);
  if (!is_dir($dir)) {
    mkdir($dir, 0775, TRUE);
  }

  imagepng($img, $real_path);
  imagedestroy($img);

  // Create file entity.
  $file = File::create([
    'uri' => $filepath,
    'filename' => $filename,
    'filemime' => 'image/png',
    'status' => 1,
  ]);
  $file->save();

  // Create media entity.
  $media = Media::create([
    'bundle' => 'image',
    'name' => $label,
    'langcode' => 'de',
    'status' => 1,
    'field_media_image' => [
      'target_id' => $file->id(),
      'alt' => $label,
      'title' => $label,
    ],
  ]);
  $media->save();

  echo "  Created image media: {$label} (mid: {$media->id()})\n";
  return $media;
}

// -- Helper: create a tile_item paragraph --
function _create_tile_item(string $title, string $body, string $variant, array $options = []): Paragraph {
  $fields = [
    'type' => 'tile_item',
    'field_section_title' => $title,
    'field_display_variant' => $variant,
    'langcode' => 'de',
  ];

  // Body: supports summary + value via text_with_summary.
  if (!empty($options['summary'])) {
    $fields['field_body'] = [
      'value' => $body,
      'summary' => $options['summary'],
      'format' => 'advanced',
    ];
  }
  else {
    $fields['field_body'] = [
      'value' => $body,
      'summary' => '',
      'format' => 'advanced',
    ];
  }

  // Highlight (Kennzahl) — mutually exclusive with media.
  if (!empty($options['highlight'])) {
    $fields['field_highlight'] = $options['highlight'];
  }

  // Media (image) — mutually exclusive with highlight.
  if (!empty($options['media_id'])) {
    $fields['field_media'] = ['target_id' => $options['media_id']];
  }

  // Links.
  if (!empty($options['links'])) {
    $fields['field_links'] = $options['links'];
  }

  $paragraph = Paragraph::create($fields);
  $paragraph->save();
  echo "  Created tile_item: {$title} ({$variant})\n";
  return $paragraph;
}

// -- Tile content data --

$short_body = '<p>Kurzer Text der unter 300 Zeichen bleibt. Hier sollte kein [mehr]-Link erscheinen.</p>';

$long_body = '<p>Mit Portscans suchen Cyberangreifer nach Schwachstellen in Systemen, die aus dem Internet erreichbar sind. Mit der Ausnutzung von Schwachstellen haben Angreifer im aktuellen Berichtszeitraum die grössten Schadwirkungen erzielt. Die Anzahl der bekannt gewordenen Schwachstellen in Software- und Hardwareprodukten erreichte im Berichtszeitraum einen neuen Höchststand. Das BSI hat über 2.000 kritische Schwachstellen identifiziert und entsprechende Warnungen veröffentlicht. Besonders betroffen waren Betriebssysteme, Webbrowser und Netzwerkkomponenten.</p>';

$summary_text = 'Kurze Zusammenfassung die auf der Kachel angezeigt wird. Der vollständige Text erscheint in der Lightbox.';

$full_body_for_summary = '<p>Dies ist der vollständige Textkörper, der in der Lightbox angezeigt wird. Er enthält wesentlich mehr Informationen als die Zusammenfassung. Die Bedrohungslage im Bereich der Cybersicherheit hat sich im Berichtszeitraum weiter verschärft. Die Anzahl der registrierten Cyberangriffe stieg im Vergleich zum Vorjahr um 25 Prozent. Insbesondere Ransomware-Angriffe auf Unternehmen und öffentliche Einrichtungen haben deutlich zugenommen. Die Angreifer nutzen zunehmend komplexe Methoden und automatisierte Tools, um Schwachstellen in IT-Systemen auszunutzen.</p>';

$themes = [
  'blue-100' => 'Standard (hell)',
  'blue-800' => 'Medium blue',
  'blue-900' => 'Dark blue',
];

// Create test images (one per theme for variety).
echo "Creating test images...\n";
$images = [
  'blue-100' => _create_test_image('Angreiferaktivitaeten 2025', '#3366CC'),
  'blue-800' => _create_test_image('Schwachstellen Statistik', '#CC6633'),
  'blue-900' => _create_test_image('Ransomware Trends', '#339966'),
];

$all_tiles_paragraphs = [];

foreach ($themes as $variant => $theme_label) {
  echo "\nCreating tiles group: {$theme_label} ({$variant})\n";

  $tile_items = [];

  // 1) Text tile with SHORT body — no [more] expected.
  $tile_items[] = _create_tile_item(
    "Kurzer Text ({$theme_label})",
    $short_body,
    $variant,
    [
      'links' => [
        ['uri' => 'internal:/node/1', 'title' => 'Interne Seite'],
      ],
    ]
  );

  // 2) Text tile with LONG body — [more] + lightbox expected.
  $tile_items[] = _create_tile_item(
    "Langer Text ({$theme_label})",
    $long_body,
    $variant,
    [
      'links' => [
        ['uri' => 'internal:/node/1', 'title' => 'Mehr erfahren'],
        ['uri' => 'https://www.bsi.bund.de', 'title' => 'Externer Link'],
      ],
    ]
  );

  // 3) Text tile with SUMMARY + body — [more] expected.
  $tile_items[] = _create_tile_item(
    "Mit Zusammenfassung ({$theme_label})",
    $full_body_for_summary,
    $variant,
    [
      'summary' => $summary_text,
    ]
  );

  // 4) Image tile with long body — [more] + lightbox + maximize icon.
  $tile_items[] = _create_tile_item(
    "Bild-Kachel ({$theme_label})",
    $long_body,
    $variant,
    [
      'media_id' => $images[$variant]->id(),
      'links' => [
        ['uri' => 'internal:/node/1', 'title' => 'Zum Kapitel'],
      ],
    ]
  );

  // 5) Kennzahl tile with highlight — short body, no [more].
  $tile_items[] = _create_tile_item(
    "Kennzahl-Kachel ({$theme_label})",
    $short_body,
    $variant,
    [
      'highlight' => '75%',
      'links' => [
        ['uri' => 'internal:/node/1', 'title' => 'Details anzeigen'],
      ],
    ]
  );

  // 6) Kennzahl tile with highlight + LONG body — [more] expected.
  $tile_items[] = _create_tile_item(
    "Kennzahl mit Lightbox ({$theme_label})",
    $long_body,
    $variant,
    [
      'highlight' => '2.000+',
      'links' => [
        ['uri' => 'internal:/node/1', 'title' => 'Schwachstellen ansehen'],
      ],
    ]
  );

  // Create the tiles container paragraph.
  $tiles_paragraph = Paragraph::create([
    'type' => 'tiles',
    'field_items' => array_map(function ($p) {
      return [
        'target_id' => $p->id(),
        'target_revision_id' => $p->getRevisionId(),
      ];
    }, $tile_items),
    'langcode' => 'de',
  ]);
  $tiles_paragraph->save();
  echo "  => Tiles container saved (id: {$tiles_paragraph->id()})\n";

  $all_tiles_paragraphs[] = $tiles_paragraph;
}

// Create the report_page node.
$node = Node::create([
  'type' => 'report_page',
  'title' => 'BSIR-205 Tiles Test Page (with images)',
  'langcode' => 'de',
  'status' => 1,
  'field_paragraphs' => array_map(function ($p) {
    return [
      'target_id' => $p->id(),
      'target_revision_id' => $p->getRevisionId(),
    ];
  }, $all_tiles_paragraphs),
]);

$node->save();

echo "\n==========================================\n";
echo "Test page created: node/{$node->id()}\n";
echo "URL: /node/{$node->id()}\n";
echo "==========================================\n";
echo "\nTest scenarios per color theme (x3 themes):\n";
echo "  1. Short body text          -> NO [more] link\n";
echo "  2. Long body (>300ch)       -> [more] link + lightbox\n";
echo "  3. Summary + body           -> [more] link + lightbox\n";
echo "  4. Image + long body        -> [more] + lightbox + image in lightbox\n";
echo "  5. Kennzahl (short)         -> NO [more] link\n";
echo "  6. Kennzahl (long body)     -> [more] link + lightbox\n";
echo "\nCheck:\n";
echo "  - [more] link WHITE on blue-800 and blue-900 tiles\n";
echo "  - [more] link UNDERLINED on all themes\n";
echo "  - [more] link CLICKABLE (not blocked by full-card overlay)\n";
echo "  - Image tiles show image in lightbox\n";
echo "  - Short text tiles do NOT show [more]\n";
echo "\nNote: Chart tiles must be created via UI (Media > Add > Chart)\n";
echo "      then added to a tile_item via paragraph editing.\n";
