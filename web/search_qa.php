<?php

/**
 * @file
 * Standalone QA page that renders all search views for visual testing.
 *
 * Access via: /search_qa.php (must be logged in as admin)
 * Renders each search view display on a single page so you can visually
 * compare all search layouts against Storybook.
 */

use Drupal\Core\DrupalKernel;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal fully (handle request to initialize session/auth).
$autoloader = require_once __DIR__ . '/autoload.php';
$request = Request::createFromGlobals();
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->boot();
$kernel->preHandle($request);
$container = $kernel->getContainer();

// Authenticate the current request (session-based).
$container->get('session_handler.write_safe')->setSessionWritable(TRUE);
if ($container->has('authentication')) {
  $container->get('authentication')->authenticate($request);
}

// Check user is logged in and has admin access.
$current_user = $container->get('current_user');
if ($current_user->isAnonymous() || !$current_user->hasPermission('administer site configuration')) {
  http_response_code(403);
  echo 'Access denied. Log in as admin at the normal Drupal site first, then reload this page.';
  exit;
}

$renderer = $container->get('renderer');
$theme_initializer = $container->get('theme.initialization');
$theme_initializer->initTheme();

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

$slot_views = [
  ['view_id' => 'search', 'display_id' => 'block_search_slot', 'label' => 'Globaler Suchschlitz'],
  ['view_id' => 'search_yearly_report', 'display_id' => 'block_search_report_slot', 'label' => 'Jahreslagebericht Suchschlitz'],
];

// Get the active theme's CSS/JS libraries.
$active_theme = $container->get('theme.manager')->getActiveTheme();
$theme_path = $active_theme->getPath();

?><!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>QA: Alle Suchen</title>
  <?php
  // Attach the theme CSS.
  $css_file = '/' . $theme_path . '/dist/styles.css';
  if (file_exists(DRUPAL_ROOT . $css_file)) {
    echo '<link rel="stylesheet" href="' . $css_file . '" />' . "\n";
  }
  ?>
  <style>
    body { font-family: system-ui, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
    .qa-header { max-width: 1200px; margin: 0 auto 30px; }
    .qa-header h1 { color: #003366; margin: 0 0 10px; }
    .qa-toc { background: #fff; padding: 20px; border-radius: 4px; box-shadow: 0 0 4px rgba(0,0,0,.15); margin-bottom: 30px; max-width: 1200px; margin-left: auto; margin-right: auto; }
    .qa-toc ol { columns: 2; column-gap: 30px; }
    .qa-toc li { margin-bottom: 6px; }
    .qa-toc a { color: #003366; }
    .qa-toc small { color: #666; }
    .qa-section { max-width: 1200px; margin: 0 auto 40px; background: #fff; border-radius: 4px; box-shadow: 0 0 4px rgba(0,0,0,.15); overflow: hidden; }
    .qa-section-header { background: #003366; color: #fff; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
    .qa-section-header h3 { margin: 0; font-size: 16px; }
    .qa-section-header code { background: rgba(255,255,255,.2); padding: 2px 8px; border-radius: 3px; font-size: 12px; }
    .qa-section-body { padding: 20px; }
    .qa-error { color: #c00; font-weight: bold; padding: 20px; }
    .qa-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }
    .qa-status--ok { background: #d4edda; color: #155724; }
    .qa-status--error { background: #f8d7da; color: #721c24; }
    .qa-back-top { display: block; text-align: right; padding: 10px 20px; color: #003366; font-size: 13px; }
  </style>
</head>
<body>

<div class="qa-header">
  <h1>QA: Alle Suchen — Visuelle Prüfung</h1>
  <p>Jede Suche wird mit dem aktiven Theme gerendert. Vergleiche mit Storybook.</p>
</div>

<div class="qa-toc">
  <h3>Inhaltsverzeichnis</h3>
  <ol>
    <?php foreach ($search_views as $i => $sv): ?>
      <li>
        <a href="#search-<?= $i ?>"><?= htmlspecialchars($sv['label']) ?></a>
        <small>(<?= $sv['view_id'] ?>/<?= $sv['display_id'] ?>)</small>
      </li>
    <?php endforeach; ?>
    <li><a href="#search-slots">Suchschlitze (Formulare)</a></li>
  </ol>
</div>

<?php
foreach ($search_views as $i => $sv):
  $view = Views::getView($sv['view_id']);
  $html = '';
  $error = '';

  if (!$view) {
    $error = 'View "' . $sv['view_id'] . '" nicht gefunden!';
  }
  elseif (!$view->setDisplay($sv['display_id'])) {
    $error = 'Display "' . $sv['display_id'] . '" nicht gefunden!';
  }
  else {
    try {
      $view->preExecute();
      $view->execute();
      $render = $view->render();
      $html = (string) $renderer->renderRoot($render);
    }
    catch (\Exception $e) {
      $error = $e->getMessage();
    }
    $view->destroy();
  }
?>

<div id="search-<?= $i ?>" class="qa-section">
  <div class="qa-section-header">
    <h3><?= ($i + 1) ?>. <?= htmlspecialchars($sv['label']) ?>
      <span class="qa-status <?= $error ? 'qa-status--error' : 'qa-status--ok' ?>">
        <?= $error ? 'FEHLER' : 'OK' ?>
      </span>
    </h3>
    <code><?= $sv['view_id'] ?> / <?= $sv['display_id'] ?></code>
  </div>
  <div class="qa-section-body">
    <?php if ($error): ?>
      <p class="qa-error"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
      <?= $html ?>
    <?php endif; ?>
  </div>
  <a href="#" class="qa-back-top">↑ Nach oben</a>
</div>

<?php endforeach; ?>

<div id="search-slots" class="qa-section">
  <div class="qa-section-header">
    <h3>Suchschlitze (Formulare ohne Ergebnisse)</h3>
    <code>form-only displays</code>
  </div>
  <div class="qa-section-body">
    <?php foreach ($slot_views as $sv):
      $view = Views::getView($sv['view_id']);
      if ($view && $view->setDisplay($sv['display_id'])):
        try {
          $view->preExecute();
          $view->execute();
          $render = $view->render();
          $html = (string) $renderer->renderRoot($render);
        }
        catch (\Exception $e) {
          $html = '<p class="qa-error">' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        $view->destroy();
    ?>
      <h4><?= htmlspecialchars($sv['label']) ?> <small>(<?= $sv['view_id'] ?>/<?= $sv['display_id'] ?>)</small></h4>
      <?= $html ?>
    <?php endif; endforeach; ?>
  </div>
</div>

<?php
// Attach theme JS.
$js_file = '/' . $theme_path . '/dist/index.js';
if (file_exists(DRUPAL_ROOT . $js_file)) {
  echo '<script src="' . $js_file . '"></script>' . "\n";
}
?>
</body>
</html>
