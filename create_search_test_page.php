<?php

/**
 * @file
 * Creates a test page (entry_page) that embeds all search views for visual QA.
 *
 * Usage: drush php:script create_search_test_page.php
 *
 * Creates an entry_page node with paragraphs embedding each search view
 * via viewsreference fields, so you can check all search layouts on one page.
 *
 * Since viewsreference paragraphs may not support all view displays,
 * this script builds a single HTML page with rendered views using
 * Drupal's Views API and outputs a node with that markup.
 */

use Drupal\node\Entity\Node;
use Drupal\views\Views;

// All search view displays to test.
$search_views = [
  ['view_id' => 'search', 'display_id' => 'block_search_global', 'label' => 'Globale Suche'],
  ['view_id' => 'search_yearly_report', 'display_id' => 'block_search_yearly_reports', 'label' => 'Jahreslagebericht Suchergebnisse'],
  ['view_id' => 'search_pages', 'display_id' => 'block_search_article', 'label' => 'Artikelsuche'],
  ['view_id' => 'search_pages', 'display_id' => 'block_search_bits', 'label' => 'BITS-Suche'],
  ['view_id' => 'search_pages', 'display_id' => 'block_search_publication', 'label' => 'Publikationssuche'],
  ['view_id' => 'search_pages', 'display_id' => 'block_search_security_label', 'label' => 'IT-Sicherheitskennzeichen'],
  ['view_id' => 'search_events', 'display_id' => 'block_events', 'label' => 'Termine / Events'],
  ['view_id' => 'search_media', 'display_id' => 'block_media', 'label' => 'Mediensuche'],
  ['view_id' => 'search_lists', 'display_id' => 'block_search_certificates', 'label' => 'Zertifikate'],
  ['view_id' => 'search_lists', 'display_id' => 'block_search_lists_jobs', 'label' => 'Stellenanzeigen'],
  ['view_id' => 'search_lists', 'display_id' => 'block_search_lists_publications', 'label' => 'Publikationen (Liste)'],
  ['view_id' => 'search_lists', 'display_id' => 'block_search_lists_keywords', 'label' => 'Schlagwörter'],
];

$body = '<h2>Übersicht aller Suchen — Visuelle QA</h2>' . "\n";
$body .= '<p>Diese Seite zeigt alle Such-Views auf einer Seite zur visuellen Prüfung.</p>' . "\n";

// Build a table of contents.
$body .= '<nav><h3>Inhaltsverzeichnis</h3><ol>' . "\n";
foreach ($search_views as $i => $sv) {
  $anchor = 'search-' . $i;
  $body .= '<li><a href="#' . $anchor . '">' . $sv['label'] . '</a>';
  $body .= ' <small>(' . $sv['view_id'] . ' / ' . $sv['display_id'] . ')</small></li>' . "\n";
}
$body .= '</ol></nav><hr />' . "\n\n";

// Render each view.
$renderer = \Drupal::service('renderer');
$rendered_count = 0;

foreach ($search_views as $i => $sv) {
  $anchor = 'search-' . $i;
  $view = Views::getView($sv['view_id']);

  $body .= '<div id="' . $anchor . '" style="margin-top: 40px; padding-top: 20px; border-top: 3px solid #003366;">' . "\n";
  $body .= '<h3>' . ($i + 1) . '. ' . $sv['label'] . '</h3>' . "\n";
  $body .= '<p><code>' . $sv['view_id'] . ' / ' . $sv['display_id'] . '</code></p>' . "\n";

  if (!$view) {
    $body .= '<p style="color: red;">View "' . $sv['view_id'] . '" nicht gefunden!</p>' . "\n";
    $body .= '</div>' . "\n";
    continue;
  }

  if (!$view->setDisplay($sv['display_id'])) {
    $body .= '<p style="color: red;">Display "' . $sv['display_id'] . '" nicht gefunden!</p>' . "\n";
    $body .= '</div>' . "\n";
    continue;
  }

  try {
    $view->preExecute();
    $view->execute();
    $render = $view->render();
    $html = $renderer->renderRoot($render);
    $body .= (string) $html;
    $rendered_count++;
    echo "OK: {$sv['label']} ({$sv['view_id']}/{$sv['display_id']})\n";
  }
  catch (\Exception $e) {
    $body .= '<p style="color: red;">Fehler: ' . htmlspecialchars($e->getMessage()) . '</p>' . "\n";
    echo "FEHLER: {$sv['label']} - {$e->getMessage()}\n";
  }

  $body .= '</div>' . "\n\n";

  // Reset view for next iteration.
  $view->destroy();
}

// Also render the search slot (form-only) for comparison.
$body .= '<div id="search-slot" style="margin-top: 40px; padding-top: 20px; border-top: 3px solid #003366;">' . "\n";
$body .= '<h3>Suchschlitz (Formular ohne Ergebnisse)</h3>' . "\n";

$slot_views = [
  ['view_id' => 'search', 'display_id' => 'block_search_slot', 'label' => 'Globaler Suchschlitz'],
  ['view_id' => 'search_yearly_report', 'display_id' => 'block_search_report_slot', 'label' => 'Jahreslagebericht Suchschlitz'],
];

foreach ($slot_views as $sv) {
  $body .= '<h4>' . $sv['label'] . ' <small>(' . $sv['view_id'] . ' / ' . $sv['display_id'] . ')</small></h4>' . "\n";
  $view = Views::getView($sv['view_id']);
  if ($view && $view->setDisplay($sv['display_id'])) {
    try {
      $view->preExecute();
      $view->execute();
      $render = $view->render();
      $html = $renderer->renderRoot($render);
      $body .= (string) $html;
      echo "OK: {$sv['label']} ({$sv['view_id']}/{$sv['display_id']})\n";
    }
    catch (\Exception $e) {
      $body .= '<p style="color: red;">Fehler: ' . htmlspecialchars($e->getMessage()) . '</p>' . "\n";
    }
    $view->destroy();
  }
}
$body .= '</div>' . "\n";

// Create the test node.
$node = Node::create([
  'type' => 'entry_page',
  'title' => 'QA: Alle Suchen — Visuelle Prüfung',
  'body' => [
    'value' => $body,
    'format' => 'full_html',
  ],
  'langcode' => 'de',
  'status' => 1,
]);
$node->save();

echo "\n========================================\n";
echo "Test-Seite erstellt!\n";
echo "NID: {$node->id()}\n";
echo "URL: /node/{$node->id()}\n";
echo "Gerenderte Views: {$rendered_count}/" . count($search_views) . "\n";
echo "========================================\n";
echo "\nNächste Schritte:\n";
echo "  drush cr\n";
echo "  Seite öffnen: /node/{$node->id()}\n";
