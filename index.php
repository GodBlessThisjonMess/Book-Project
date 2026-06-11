<?php
// Root index.php - Fallback untuk akses langsung ke root
// Ketika server jalan dari /public folder, file ini tidak digunakan
// Hanya digunakan jika ada akses langsung ke http://localhost/index.php

if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $url;
    if (is_file($file)) {
        return false;
    }
}

// Forward ke public/index.php
require __DIR__ . '/public/index.php';
