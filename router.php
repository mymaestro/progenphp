<?php
/**
 * Simple Router for Development Server
 * 
 * This file handles routing for the PHP development server
 * to properly serve files from different directories.
 */

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove query parameters
$path = parse_url($requestUri, PHP_URL_PATH);

// Handle test pages
if (strpos($path, '/tests/') === 0) {
    $testFile = __DIR__ . $path;
    if (file_exists($testFile)) {
        require $testFile;
        return true;
    }
}

// Handle public files (default behavior)
$publicFile = __DIR__ . '/public' . $path;
if ($path === '/') {
    $publicFile = __DIR__ . '/public/index.php';
}

if (file_exists($publicFile)) {
    if (pathinfo($publicFile, PATHINFO_EXTENSION) === 'php') {
        require $publicFile;
        return true;
    } else {
        return false; // Let the server handle static files
    }
}

// 404 for everything else
http_response_code(404);
echo "404 - File not found";
return true;