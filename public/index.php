<?php

/**
 * public/index.php — Entry Point dengan Filesystem-Based Routing
 *
 * URL di-resolve otomatis berdasarkan struktur folder di src/pages/:
 *
 *   /               → src/pages/index.php
 *   /about          → src/pages/about/index.php  (atau about.php)
 *   /blog           → src/pages/blog/index.php
 *   /blog/detail    → src/pages/blog/detail/index.php (atau blog/detail.php)
 */

define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH',  BASE_PATH . '/src');
define('PAGES_PATH', SRC_PATH . '/pages');

// -------------------------------------------------------
// 1. Load .env
// -------------------------------------------------------
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$name, $value] = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// -------------------------------------------------------
// 2. Filesystem-Based Router
// -------------------------------------------------------

/**
 * Resolve URL path ke file PHP di src/pages/.
 * Urutan pencarian:
 *   1. pages/{path}/index.php   ← folder dengan index (utama)
 *   2. pages/{path}.php         ← file langsung
 *   3. pages/404.php            ← fallback
 */
function resolve_page(string $path): string
{
    // Sanitasi: buang karakter berbahaya, hindari path traversal
    $path = '/' . trim(preg_replace('/\.{2,}/', '', $path), '/');

    $candidates = [
        PAGES_PATH . $path . '/index.php',  // /about   → pages/about/index.php
        PAGES_PATH . $path . '.php',        // /about   → pages/about.php
    ];

    foreach ($candidates as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }

    // Fallback 404
    http_response_code(404);
    return PAGES_PATH . '/404.php';
}

$path     = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pageFile = resolve_page($path);

require_once $pageFile;
