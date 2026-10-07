<?php

/**
 * @file
 * Creates a demo page with Dynamic List paragraphs.
 *
 * Run via: drush php:script scripts/create-demo-dynamic-list.php
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

// 1. General Content list — shows date, title, summary.
$paragraph_general = Paragraph::create([
  'type' => 'view',
  'field_section_title' => 'Aktuelle Inhalte',
  'field_section_text' => [
    'value' => '<p>Automatisch generierte Liste aller aktuellen Inhalte.</p>',
    'format' => 'full_html',
  ],
  'field_view' => [
    'target_id' => 'search_lists',
    'display_id' => 'nodes',
    'data' => serialize([
      'field_select' => [
        'node_created' => 'node_created',
        'title' => 'title',
        'node_summary' => 'node_summary',
      ],
      'sort_select' => [],
      'argument_override' => [],
      'limit' => 5,
    ]),
  ],
]);
$paragraph_general->save();
echo "Created paragraph: Aktuelle Inhalte (General Content)\n";

// 2. Jobs list — shows title, location, deadline.
$paragraph_jobs = Paragraph::create([
  'type' => 'view',
  'field_section_title' => 'Stellenanzeigen',
  'field_section_text' => [
    'value' => '<p>Offene Stellen beim BSI.</p>',
    'format' => 'full_html',
  ],
  'field_view' => [
    'target_id' => 'search_lists',
    'display_id' => 'jobs',
    'data' => serialize([
      'field_select' => [
        'title' => 'title',
        'field_location' => 'field_location',
        'field_deadline' => 'field_deadline',
      ],
      'sort_select' => [],
      'argument_override' => [],
      'limit' => 5,
    ]),
  ],
]);
$paragraph_jobs->save();
echo "Created paragraph: Stellenanzeigen (Jobs)\n";

// 3. Certificates list — shows title, applicant, certification date.
$paragraph_certs = Paragraph::create([
  'type' => 'view',
  'field_section_title' => 'Zertifikate',
  'field_view' => [
    'target_id' => 'search_lists',
    'display_id' => 'certificates',
    'data' => serialize([
      'field_select' => [
        'title' => 'title',
        'field_applicant' => 'field_applicant',
        'field_certification_date' => 'field_certification_date',
      ],
      'sort_select' => [],
      'argument_override' => [],
      'limit' => 5,
    ]),
  ],
]);
$paragraph_certs->save();
echo "Created paragraph: Zertifikate (Certificates)\n";

// Create the demo page with all three paragraphs.
$node = Node::create([
  'type' => 'page',
  'title' => 'Demo: Dynamische Listen (BSIR-174)',
  'langcode' => 'de',
  'status' => 1,
  'field_paragraphs' => [
    ['target_id' => $paragraph_general->id(), 'target_revision_id' => $paragraph_general->getRevisionId()],
    ['target_id' => $paragraph_jobs->id(), 'target_revision_id' => $paragraph_jobs->getRevisionId()],
    ['target_id' => $paragraph_certs->id(), 'target_revision_id' => $paragraph_certs->getRevisionId()],
  ],
]);
$node->save();

echo "\n✓ Demo page created: /node/" . $node->id() . "\n";
echo "  Title: " . $node->getTitle() . "\n";
echo "  Edit: /node/" . $node->id() . "/edit\n";
