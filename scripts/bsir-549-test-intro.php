<?php

/**
 * @file
 * Test script for BSIR-549: Vorfallsbericht title + intro text.
 *
 * Creates a temporary vorfallsbericht content type (if needed), adds the
 * field_paragraphs field with text_section, and creates a test node with
 * intro content. Verifies data is stored correctly.
 *
 * Usage:
 *   drush php:script scripts/bsir-549-test-intro.php
 *   drush php:script scripts/bsir-549-test-intro.php -- --cleanup
 */

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\paragraphs\Entity\Paragraph;

$cleanup = in_array('--cleanup', $extra ?? []);

if ($cleanup) {
  echo "=== CLEANUP MODE ===\n\n";

  // Delete test nodes.
  $nids = \Drupal::entityQuery('node')
    ->condition('type', 'vorfallsbericht')
    ->condition('title', 'BSIR-549 Test%', 'LIKE')
    ->accessCheck(FALSE)
    ->execute();

  if ($nids) {
    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $node) {
      echo "Deleting node: {$node->label()} (nid: {$node->id()})\n";
      $node->delete();
    }
  }
  else {
    echo "No test nodes found.\n";
  }

  // Remove field instance if CT was created by this script.
  $field = FieldConfig::loadByName('node', 'vorfallsbericht', 'field_paragraphs');
  if ($field) {
    echo "Deleting field_paragraphs field from vorfallsbericht.\n";
    $field->delete();
  }

  // Remove CT if it has no nodes.
  $type = NodeType::load('vorfallsbericht');
  if ($type) {
    $remaining = \Drupal::entityQuery('node')
      ->condition('type', 'vorfallsbericht')
      ->accessCheck(FALSE)
      ->count()
      ->execute();
    if ($remaining == 0) {
      echo "Deleting vorfallsbericht content type.\n";
      $type->delete();
    }
    else {
      echo "Keeping vorfallsbericht CT ({$remaining} nodes still exist).\n";
    }
  }

  echo "\nCleanup complete.\n";
  return;
}

echo "=== BSIR-549: Vorfallsbericht Title + Intro Test ===\n\n";

// Step 1: Ensure vorfallsbericht CT exists.
$type = NodeType::load('vorfallsbericht');
if (!$type) {
  echo "Creating vorfallsbericht content type...\n";
  $type = NodeType::create([
    'type' => 'vorfallsbericht',
    'name' => 'Vorfallsbericht',
    'description' => 'Incident report for the CSN area.',
    'new_revision' => TRUE,
  ]);
  $type->save();
  echo "  -> Created.\n";
}
else {
  echo "Vorfallsbericht CT already exists.\n";
}

// Step 2: Ensure field_paragraphs storage exists.
$storage = FieldStorageConfig::loadByName('node', 'field_paragraphs');
if (!$storage) {
  echo "ERROR: field.storage.node.field_paragraphs does not exist.\n";
  echo "This storage is shared across all content types and should already be present.\n";
  return;
}
echo "Field storage field_paragraphs exists.\n";

// Step 3: Add field_paragraphs to vorfallsbericht (text_section only).
$field = FieldConfig::loadByName('node', 'vorfallsbericht', 'field_paragraphs');
if (!$field) {
  echo "Adding field_paragraphs to vorfallsbericht...\n";
  $field = FieldConfig::create([
    'field_storage' => $storage,
    'bundle' => 'vorfallsbericht',
    'label' => 'Paragraphs',
    'required' => FALSE,
    'translatable' => FALSE,
    'settings' => [
      'handler' => 'default:paragraph',
      'handler_settings' => [
        'target_bundles' => [
          'text_section' => 'text_section',
        ],
        'negate' => 0,
        'target_bundles_drag_drop' => [
          'text_section' => [
            'weight' => 0,
            'enabled' => TRUE,
          ],
        ],
      ],
    ],
  ]);
  $field->save();
  echo "  -> Added.\n";
}
else {
  echo "field_paragraphs already exists on vorfallsbericht.\n";
}

// Step 4: Create a test node with a text_section paragraph.
echo "\nCreating test content...\n";

$paragraph = Paragraph::create([
  'type' => 'text_section',
  'field_section_title' => 'Vorfallsbericht erstellen',
  'field_section_text' => [
    'value' => '<p>Bitte tragen Sie alle relevanten Informationen zu Ihrem Vorfall in diesem Bericht ein, bevor Sie ihn an uns senden. Ihre Eingaben können Sie am Ende des Berichts unter "Speichern" zwischenspeichern.</p>',
    'format' => 'basic',
  ],
]);
$paragraph->save();
echo "  -> Created text_section paragraph (id: {$paragraph->id()})\n";

$node = Node::create([
  'type' => 'vorfallsbericht',
  'title' => 'BSIR-549 Test: Vorfallsbericht mit Intro',
  'status' => 1,
  'field_paragraphs' => [
    [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ],
  ],
]);
$node->save();
echo "  -> Created node: {$node->label()} (nid: {$node->id()})\n";

// Step 5: Verify data retrieval (simulates API logic).
echo "\n=== Verification ===\n";

$loaded = Node::load($node->id());
$paragraphs = $loaded->get('field_paragraphs')->referencedEntities();

if (empty($paragraphs)) {
  echo "FAIL: No paragraphs found on the node.\n";
  return;
}

$intro = NULL;
foreach ($paragraphs as $p) {
  if ($p->bundle() === 'text_section') {
    $intro = $p;
    break;
  }
}

if (!$intro) {
  echo "FAIL: No text_section paragraph found.\n";
  return;
}

$headline = $intro->get('field_section_title')->value ?? '';
$text = $intro->get('field_section_text')->value ?? '';
$format = $intro->get('field_section_text')->format ?? '';

echo "Headline: {$headline}\n";
echo "Text: " . mb_substr(strip_tags($text), 0, 80) . "...\n";
echo "Format: {$format}\n";

$ok = TRUE;
if ($headline !== 'Vorfallsbericht erstellen') {
  echo "FAIL: Headline mismatch.\n";
  $ok = FALSE;
}
if (empty($text)) {
  echo "FAIL: Text is empty.\n";
  $ok = FALSE;
}

if ($ok) {
  echo "\nPASS: All checks passed.\n";
}

echo "\nAPI endpoint: /csn/api/vorfallsbericht/{$node->id()}/intro\n";
echo "Cleanup: drush php:script scripts/bsir-549-test-intro.php -- --cleanup\n";
