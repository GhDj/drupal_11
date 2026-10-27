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
 * - With CTA links
 * - With media (if available)
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

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

  // Highlight (Kennzahl).
  if (!empty($options['highlight'])) {
    $fields['field_highlight'] = $options['highlight'];
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

  // 4) Kennzahl tile with highlight — short body, no [more].
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

  // 5) Kennzahl tile with highlight + LONG body — [more] expected.
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
  'title' => 'BSIR-205 Tiles Test Page',
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
echo "\nTest scenarios per color theme:\n";
echo "  1. Short body text      -> NO [more] link\n";
echo "  2. Long body (>300ch)   -> [more] link + lightbox\n";
echo "  3. Summary + body       -> [more] link + lightbox\n";
echo "  4. Kennzahl (short)     -> NO [more] link\n";
echo "  5. Kennzahl (long body) -> [more] link + lightbox\n";
echo "\nCheck:\n";
echo "  - [more] link WHITE on blue-800 and blue-900 tiles\n";
echo "  - [more] link UNDERLINED on all themes\n";
echo "  - [more] link CLICKABLE (not blocked by full-card overlay)\n";
echo "  - Maximize icon BLACK (if media/chart tiles exist)\n";
echo "  - Short text tiles do NOT show [more]\n";
