<?php
// Simple debug script to test server configuration
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '  <meta charset="UTF-8">';
echo '  <meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '  <title>ProgenPHP - Debug</title>';
echo '  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.2/css/bulma.min.css">';
echo '  <style>.hero,.box,.notification,.message,.message-body,.button,.tag,.input,.textarea,.select select,.table{border-radius:0!important}.box,.notification,.message,.hero{box-shadow:none!important}.box,.notification,.message,.hero,.table{border:1px solid hsl(0,0%,86%)}</style>';
echo '</head>';
echo '<body class="has-background-dark">';
echo '  <section class="section">';
echo '    <div class="container is-max-desktop">';
echo '      <h1 class="title">Debug Info</h1>';

echo '      <div class="box">';
echo '        <h2 class="subtitle">Current Directory</h2>';
echo '        <pre>' . htmlspecialchars(__DIR__) . '</pre>';
echo '      </div>';

echo '      <div class="box">';
echo '        <h2 class="subtitle">File Exists Check</h2>';
echo '        <ul>';
echo '          <li>access-test.php exists: <strong>' . (file_exists(__DIR__ . '/access-test.php') ? 'YES' : 'NO') . '</strong></li>';
echo '          <li>security-test.php exists: <strong>' . (file_exists(__DIR__ . '/security-test.php') ? 'YES' : 'NO') . '</strong></li>';
echo '        </ul>';
echo '      </div>';

echo '      <div class="box">';
echo '        <h2 class="subtitle">Directory Contents</h2>';
echo '        <pre>';
print_r(scandir(__DIR__));
echo '        </pre>';
echo '      </div>';

echo '      <div class="box">';
echo '        <h2 class="subtitle">Server Info</h2>';
echo '        <ul>';
echo '          <li>Server: ' . htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</li>';
echo '          <li>Document Root: ' . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '</li>';
echo '          <li>Script Name: ' . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . '</li>';
echo '          <li>Request URI: ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') . '</li>';
echo '        </ul>';
echo '      </div>';

echo '      <div class="buttons">';
echo '        <a class="button is-link is-light" href="access-test.php">Access Test</a>';
echo '        <a class="button is-info is-light" href="security-test.php">Security Test</a>';
echo '        <a class="button is-light" href="../">Back to Main</a>';
echo '      </div>';

echo '    </div>';
echo '  </section>';
echo '</body>';
echo '</html>';
?>