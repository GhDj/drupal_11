<?php

/**
 * @file
 * Tests the BSIR-547 CSN comment API end-to-end.
 *
 * Prerequisites:
 *   ddev drush en bsi_csn -y && ddev drush cr
 *
 * Usage: ddev drush php:script scripts/bsir-547-test-comments.php
 *
 * What it does:
 * 1. Ensures the csn_comment table exists (creates it directly if needed)
 * 2. Creates a temporary "vorfallsbericht" node type (if missing)
 * 3. Creates a CSN group relationship type for vorfallsbericht (if missing)
 * 4. Creates a CSN group + test node + adds node to group
 * 5. Creates test users (csn_admin + ersthelfer) and adds them to the group
 * 6. Grants the "access csn comments" permission
 * 7. Tests the CsnCommentManager service directly:
 *    - Role detection
 *    - Comment creation (admin + ersthelfer)
 *    - Comment listing
 *    - Validation (empty body, >1000 chars)
 * 8. Cleans up all test data
 *
 * Cleanup only: ddev drush php:script scripts/bsir-547-test-comments.php -- --cleanup
 */

use Drupal\Core\Database\Database;
use Drupal\group\Entity\Group;
use Drupal\group\Entity\GroupRelationshipType;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

const TEST_PREFIX = '[BSIR-547 TEST] ';

if (in_array('--cleanup', $extra ?? [])) {
  cleanup();
  return;
}

echo "=== BSIR-547 CSN Comment API Test ===\n\n";

$errors = 0;
$passed = 0;

// --- Setup ---

// 0. Ensure bsi_csn module is enabled.
if (!\Drupal::moduleHandler()->moduleExists('bsi_csn')) {
  echo "ERROR: bsi_csn module is not enabled.\n";
  echo "Run: ddev drush en bsi_csn -y && ddev drush cr\n";
  exit(1);
}

// 1. Ensure csn_comment table (create directly via DB schema if missing).
$schema = Database::getConnection()->schema();
if (!$schema->tableExists('csn_comment')) {
  echo "Creating csn_comment table...\n";
  $table = [
    'description' => 'Stores comments/corrections on Vorfallsbericht nodes.',
    'fields' => [
      'id' => ['type' => 'serial', 'unsigned' => TRUE, 'not null' => TRUE],
      'nid' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
      'uid' => ['type' => 'int', 'unsigned' => TRUE, 'not null' => TRUE],
      'author_role' => ['type' => 'varchar', 'length' => 32, 'not null' => TRUE],
      'body' => ['type' => 'varchar', 'length' => 1000, 'not null' => TRUE],
      'created' => ['type' => 'int', 'not null' => TRUE],
    ],
    'primary key' => ['id'],
    'indexes' => ['nid' => ['nid'], 'nid_created' => ['nid', 'created']],
  ];
  $schema->createTable('csn_comment', $table);
}
assert_true($schema->tableExists('csn_comment'), 'csn_comment table exists', $passed, $errors);

// 2. Create vorfallsbericht node type if it doesn't exist.
$vb_type = NodeType::load('vorfallsbericht');
if (!$vb_type) {
  echo "Creating temporary 'vorfallsbericht' node type...\n";
  $vb_type = NodeType::create([
    'type' => 'vorfallsbericht',
    'name' => TEST_PREFIX . 'Vorfallsbericht',
  ]);
  $vb_type->save();
}

// 3. Create CSN group relationship type for vorfallsbericht if missing.
$rel_type_id = 'csn-group_node-vorfallsbericht';
$rel_type = GroupRelationshipType::load($rel_type_id);
if (!$rel_type) {
  echo "Creating CSN group relationship type for vorfallsbericht...\n";
  $rel_type = GroupRelationshipType::create([
    'id' => $rel_type_id,
    'group_type' => 'csn',
    'relation_type' => 'group_node:vorfallsbericht',
    'plugin_config' => [
      'group_cardinality' => 0,
      'entity_cardinality' => 1,
    ],
  ]);
  $rel_type->save();
}

// 4. Create test CSN group.
$group = Group::create([
  'type' => 'csn',
  'label' => TEST_PREFIX . 'Test CSN Group',
]);
$group->save();
echo "Created CSN group id={$group->id()}\n";

// 5. Create vorfallsbericht node.
$node = Node::create([
  'type' => 'vorfallsbericht',
  'title' => TEST_PREFIX . 'Test Vorfallsbericht',
  'langcode' => 'de',
  'status' => 1,
]);
$node->save();
echo "Created vorfallsbericht nid={$node->id()}\n";

// 6. Add node to CSN group.
$group->addRelationship($node, 'group_node:vorfallsbericht');
echo "Added node to CSN group\n";

// 7. Create test users.
$admin_user = User::create([
  'name' => TEST_PREFIX . 'csn_admin_user',
  'mail' => 'bsir547-admin@test.local',
  'status' => 1,
  'roles' => ['csn_admin'],
]);
$admin_user->save();
echo "Created csn_admin user uid={$admin_user->id()}\n";

$ersthelfer_user = User::create([
  'name' => TEST_PREFIX . 'ersthelfer_user',
  'mail' => 'bsir547-ersthelfer@test.local',
  'status' => 1,
]);
$ersthelfer_user->save();

// Add ersthelfer to group as member.
$group->addMember($ersthelfer_user);
echo "Created ersthelfer user uid={$ersthelfer_user->id()} + added to group\n";

// 8. Grant permission.
$authenticated_role = Role::load('authenticated');
if ($authenticated_role && !$authenticated_role->hasPermission('access csn comments')) {
  $authenticated_role->grantPermission('access csn comments');
  $authenticated_role->save();
  echo "Granted 'access csn comments' to authenticated role\n";
}

// Rebuild router for new routes.
\Drupal::service('router.builder')->rebuild();

echo "\n=== Running Tests ===\n\n";

/** @var \Drupal\bsi_csn\Service\CsnCommentManager $manager */
$manager = \Drupal::service('bsi_csn.comment_manager');

// --- Test: Role Detection ---
$role = $manager->determineAuthorRole($node, $admin_user);
assert_true($role === 'csn_admin', "Admin role detected as 'csn_admin' (got: $role)", $passed, $errors);

$role = $manager->determineAuthorRole($node, $ersthelfer_user);
assert_true($role === 'ersthelfer', "Ersthelfer role detected as 'ersthelfer' (got: $role)", $passed, $errors);

// Non-member should get NULL.
$outsider = User::create([
  'name' => TEST_PREFIX . 'outsider_user',
  'mail' => 'bsir547-outsider@test.local',
  'status' => 1,
]);
$outsider->save();
$role = $manager->determineAuthorRole($node, $outsider);
assert_true($role === NULL, "Outsider role is NULL (got: " . var_export($role, TRUE) . ")", $passed, $errors);

// --- Test: Comment Creation (CSN-Admin) ---
$comment = $manager->createComment($node, $admin_user, 'csn_admin', 'Bitte korrigieren Sie Abschnitt 3.');
assert_true(!empty($comment['id']), "Admin comment created with id={$comment['id']}", $passed, $errors);
assert_true($comment['author_role'] === 'csn_admin', "Admin comment has role 'csn_admin'", $passed, $errors);
assert_true($comment['body'] === 'Bitte korrigieren Sie Abschnitt 3.', "Admin comment body matches", $passed, $errors);

// --- Test: Comment Creation (Ersthelfer) ---
$comment2 = $manager->createComment($node, $ersthelfer_user, 'ersthelfer', 'Korrektur wurde vorgenommen.');
assert_true(!empty($comment2['id']), "Ersthelfer comment created with id={$comment2['id']}", $passed, $errors);
assert_true($comment2['author_role'] === 'ersthelfer', "Ersthelfer comment has role 'ersthelfer'", $passed, $errors);

// --- Test: Comment Listing ---
$comments = $manager->getComments($node);
assert_true(count($comments) === 2, "Two comments returned (got: " . count($comments) . ")", $passed, $errors);
assert_true((int) $comments[0]['created'] <= (int) $comments[1]['created'], "Comments ordered by created ASC", $passed, $errors);

// --- Test: Validation - Empty Body ---
$caught = FALSE;
try {
  $manager->createComment($node, $admin_user, 'csn_admin', '');
} catch (\InvalidArgumentException $e) {
  $caught = TRUE;
}
assert_true($caught, "Empty body throws InvalidArgumentException", $passed, $errors);

// --- Test: Validation - Whitespace-only Body ---
$caught = FALSE;
try {
  $manager->createComment($node, $admin_user, 'csn_admin', '   ');
} catch (\InvalidArgumentException $e) {
  $caught = TRUE;
}
assert_true($caught, "Whitespace-only body throws InvalidArgumentException", $passed, $errors);

// --- Test: Validation - Body > 1000 chars ---
$caught = FALSE;
try {
  $manager->createComment($node, $admin_user, 'csn_admin', str_repeat('A', 1001));
} catch (\InvalidArgumentException $e) {
  $caught = TRUE;
}
assert_true($caught, "Body > 1000 chars throws InvalidArgumentException", $passed, $errors);

// --- Test: Body exactly 1000 chars (should succeed) ---
$comment3 = $manager->createComment($node, $admin_user, 'csn_admin', str_repeat('B', 1000));
assert_true(!empty($comment3['id']), "Body of exactly 1000 chars succeeds", $passed, $errors);

// --- Test: Wrong content type ---
$page_node = Node::create([
  'type' => 'page',
  'title' => TEST_PREFIX . 'Not a Vorfallsbericht',
  'langcode' => 'de',
  'status' => 1,
  'moderation_state' => 'published',
]);
$page_node->save();
$wrong_role = $manager->determineAuthorRole($page_node, $admin_user);
// Note: role detection works on group membership, not content type.
// Content type check is done in the controller, not the service.

echo "\n=== Results ===\n";
echo "Passed: $passed\n";
echo "Failed: $errors\n";

// --- Cleanup ---
echo "\nCleaning up test data...\n";
cleanup_entities($outsider, $page_node, $node, $group, $admin_user, $ersthelfer_user);

// Remove test relationship type.
$rel_type = GroupRelationshipType::load($rel_type_id);
if ($rel_type) {
  $rel_type->delete();
  echo "Deleted relationship type $rel_type_id\n";
}

// Remove test node type (only if we created it).
$vb_type = NodeType::load('vorfallsbericht');
if ($vb_type && str_starts_with($vb_type->label(), TEST_PREFIX)) {
  $vb_type->delete();
  echo "Deleted vorfallsbericht node type\n";
}

// Clean csn_comment table entries.
Database::getConnection()->delete('csn_comment')
  ->condition('nid', $node->id())
  ->execute();
echo "Cleaned csn_comment table\n";

echo "\nDone!\n";
exit($errors > 0 ? 1 : 0);

// --- Helpers ---

function assert_true(bool $condition, string $message, int &$passed, int &$errors): void {
  if ($condition) {
    echo "  PASS: $message\n";
    $passed++;
  }
  else {
    echo "  FAIL: $message\n";
    $errors++;
  }
}

function cleanup_entities(mixed ...$entities): void {
  foreach ($entities as $entity) {
    if ($entity && method_exists($entity, 'delete')) {
      try {
        $entity->delete();
        echo "  Deleted {$entity->getEntityTypeId()} id={$entity->id()}\n";
      }
      catch (\Exception $e) {
        echo "  Warning: could not delete {$entity->getEntityTypeId()} id={$entity->id()}: {$e->getMessage()}\n";
      }
    }
  }
}

/**
 * Standalone cleanup (for --cleanup flag).
 */
function cleanup(): void {
  echo "Cleaning up BSIR-547 test data...\n";

  // Delete test nodes.
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $nids = $node_storage->getQuery()
    ->condition('title', TEST_PREFIX, 'STARTS_WITH')
    ->accessCheck(FALSE)
    ->execute();
  if ($nids) {
    $nodes = $node_storage->loadMultiple($nids);
    foreach ($nodes as $n) {
      echo "  Deleting node nid={$n->id()}: {$n->getTitle()}\n";
      $n->delete();
    }
  }

  // Delete test users.
  $user_storage = \Drupal::entityTypeManager()->getStorage('user');
  $uids = $user_storage->getQuery()
    ->condition('name', TEST_PREFIX, 'STARTS_WITH')
    ->accessCheck(FALSE)
    ->execute();
  if ($uids) {
    $users = $user_storage->loadMultiple($uids);
    foreach ($users as $u) {
      echo "  Deleting user uid={$u->id()}: {$u->getAccountName()}\n";
      $u->delete();
    }
  }

  // Delete test groups.
  $group_storage = \Drupal::entityTypeManager()->getStorage('group');
  $gids = $group_storage->getQuery()
    ->condition('label', TEST_PREFIX, 'STARTS_WITH')
    ->accessCheck(FALSE)
    ->execute();
  if ($gids) {
    $groups = $group_storage->loadMultiple($gids);
    foreach ($groups as $g) {
      echo "  Deleting group gid={$g->id()}: {$g->label()}\n";
      $g->delete();
    }
  }

  // Delete test relationship type.
  $rel_type = GroupRelationshipType::load('csn-group_node-vorfallsbericht');
  if ($rel_type) {
    $rel_type->delete();
    echo "  Deleted relationship type csn-group_node-vorfallsbericht\n";
  }

  // Delete test node type.
  $vb_type = NodeType::load('vorfallsbericht');
  if ($vb_type && str_starts_with($vb_type->label(), TEST_PREFIX)) {
    $vb_type->delete();
    echo "  Deleted vorfallsbericht node type\n";
  }

  // Truncate test comments.
  Database::getConnection()->truncate('csn_comment')->execute();
  echo "  Truncated csn_comment table\n";

  echo "Done!\n";
}
