<?php

/**
 * @file
 * Creates a test page node and adds it to the CSN (DEH) group.
 *
 * Usage: drush php:script create_csn_test_content.php
 *
 * Prerequisites:
 *   - The CSN group "Digitale Ersthelfende" must exist.
 *   - The group relationship type csn-group_node-page must be installed.
 */

use Drupal\group\Entity\Group;
use Drupal\node\Entity\Node;

// Find the CSN group by label.
$groups = \Drupal::entityTypeManager()
  ->getStorage('group')
  ->loadByProperties(['type' => 'csn']);

if (empty($groups)) {
  echo "Error: No CSN group found. Create one first.\n";
  return;
}

$group = reset($groups);
echo "Found CSN group: {$group->label()} (id: {$group->id()})\n";

// Create a test page node.
$node = Node::create([
  'type' => 'page',
  'title' => 'CSN Testseite – Digitale Ersthelfende',
  'body' => [
    'value' => '<p>Dies ist eine Testseite im geschlossenen CSN-Bereich. Sie dient zur Überprüfung der CSN-Header-Sichtbarkeit.</p><p>Der CSN-Header (Branding-Block) sollte auf dieser Seite angezeigt werden, da sie zur DEH-Gruppe gehört.</p>',
    'format' => 'full_html',
  ],
  'langcode' => 'de',
  'status' => 1,
]);
$node->save();
echo "Created page node: {$node->label()} (nid: {$node->id()})\n";

// Add the node to the CSN group.
$group->addRelationship($node, 'group_node:page');
echo "Added node to CSN group '{$group->label()}'.\n";

// Verify.
/** @var \Drupal\group\Entity\Storage\GroupRelationshipStorageInterface $storage */
$storage = \Drupal::entityTypeManager()->getStorage('group_relationship');
$relationships = $storage->loadByEntity($node);
if (!empty($relationships)) {
  echo "Verified: Node belongs to " . count($relationships) . " group(s).\n";
} else {
  echo "Warning: No group relationship found after creation.\n";
}

echo "\nDone! Visit: /node/{$node->id()}\n";
echo "The CSN Branding block should appear if:\n";
echo "  1. bsi_csn module is enabled (drush en bsi_csn)\n";
echo "  2. Block is placed in header region with Group Content Visibility condition\n";
echo "  3. CSN group type is selected in the visibility condition\n";
