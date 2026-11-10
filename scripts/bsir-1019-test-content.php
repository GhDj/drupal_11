<?php

/**
 * @file
 * Creates test content to verify BSIR-1019 slider behavior with few elements.
 *
 * Usage: ddev drush php:script /path/to/bsir-1019-test-content.php
 *
 * Creates:
 * - 5 dummy article nodes (used as explorer references)
 * - 4 test pages, each with an explorer_content_types paragraph containing
 *   1, 2, 3, and 5 items respectively.
 * - 1 entry_page with 1 stage item (to test hero/opener single slide)
 *
 * Cleanup: run with --cleanup argument to delete all created content.
 */

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

const TEST_PREFIX = '[BSIR-1019 TEST] ';

// Check for cleanup mode.
if (in_array('--cleanup', $extra ?? [])) {
  cleanup();
  return;
}

// Step 1: Create dummy article nodes to reference.
$article_nids = [];
$needed = 5;

echo "Creating $needed dummy articles...\n";
for ($i = 1; $i <= $needed; $i++) {
  $article = Node::create([
    'type' => 'article',
    'title' => TEST_PREFIX . "Slider Test Article $i",
    'langcode' => 'de',
    'status' => 1,
    'moderation_state' => 'published',
  ]);
  $article->save();
  $article_nids[] = $article->id();
  echo "  Created article nid={$article->id()}: {$article->getTitle()}\n";
}

// Step 2: Create test pages with explorer paragraphs of varying item counts.
$test_cases = [1, 2, 3, 5];

foreach ($test_cases as $count) {
  $refs = array_slice($article_nids, 0, $count);

  $paragraph = Paragraph::create([
    'type' => 'explorer_content_types',
    'field_section_title' => TEST_PREFIX . "Explorer with $count item(s)",
    'field_content' => array_map(fn($nid) => ['target_id' => $nid], $refs),
  ]);
  $paragraph->save();

  $page = Node::create([
    'type' => 'page',
    'title' => TEST_PREFIX . "Slider Test - $count Explorer Item(s)",
    'langcode' => 'de',
    'status' => 1,
    'moderation_state' => 'published',
    'field_paragraphs' => [
      ['target_id' => $paragraph->id(), 'target_revision_id' => $paragraph->getRevisionId()],
    ],
  ]);
  $page->save();
  echo "Created page nid={$page->id()}: {$page->getTitle()}\n";
}

// Step 3: Create an entry_page with 1 stage item (tests opener/hero slider).
$teaser_paragraph = Paragraph::create([
  'type' => 'teaser_single',
  'field_section_title' => TEST_PREFIX . 'Single Stage Item',
]);
$teaser_paragraph->save();

$entry_page = Node::create([
  'type' => 'entry_page',
  'title' => TEST_PREFIX . 'Opener Single Slide Test',
  'langcode' => 'de',
  'status' => 1,
  'moderation_state' => 'published',
  'field_stage_items' => [
    ['target_id' => $teaser_paragraph->id(), 'target_revision_id' => $teaser_paragraph->getRevisionId()],
  ],
]);
$entry_page->save();
echo "Created entry_page nid={$entry_page->id()}: {$entry_page->getTitle()}\n";

echo "\nDone! Visit the created pages to test slider behavior.\n";
echo "To clean up: ddev drush php:script /path/to/bsir-1019-test-content.php --cleanup\n";

/**
 * Removes all test content created by this script.
 */
function cleanup(): void {
  $storage = \Drupal::entityTypeManager()->getStorage('node');

  $nids = $storage->getQuery()
    ->condition('title', TEST_PREFIX, 'STARTS_WITH')
    ->accessCheck(FALSE)
    ->execute();

  if (empty($nids)) {
    echo "No test content found.\n";
    return;
  }

  $nodes = $storage->loadMultiple($nids);
  foreach ($nodes as $node) {
    echo "Deleting nid={$node->id()}: {$node->getTitle()}\n";
    $node->delete();
  }
  echo "Cleaned up " . count($nids) . " nodes.\n";
}
