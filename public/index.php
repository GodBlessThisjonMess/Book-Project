<?php

// ====================================================================
// APPLICATION FRONT CONTROLLER
// ====================================================================

// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = __DIR__ . '/../app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });
}

use App\Core\Router;

// 2. Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. BASE_URL - Pendeteksian Dinamis untuk PHP Built-in Server dan Apache htdocs
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Deteksi folder dasar instalasi secara dinamis dari SCRIPT_NAME
$scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']); // e.g. /book-reading-tracker/public/index.php atau /book-reading-tracker/index.php
$baseDir = dirname($scriptPath);

// Jika script berada di dalam folder public, potong "/public" dari akhir path
if (substr($baseDir, -7) === '/public') {
    $baseDir = substr($baseDir, 0, -7);
}

// Normalisasi folder dasar agar kosong jika berada di root domain
$baseDir = rtrim($baseDir, '/\\');
if ($baseDir === '/' || empty($baseDir)) {
    $baseDir = '';
}

define('BASE_URL', $protocol . $host . $baseDir);

// 4. Router
$router = new Router();

// Routes
$router->add('GET', '/', 'DashboardController@index');
$router->add('POST', '/confirm-reading', 'DashboardController@confirmActivity');
$router->add('GET', '/books', 'BookController@index');
$router->add('GET', '/books/create', 'BookController@create');
$router->add('POST', '/books', 'BookController@store');
$router->add('GET', '/books/{id}', 'BookController@show');
$router->add('GET', '/books/{id}/edit', 'BookController@edit');
$router->add('POST', '/books/{id}/update', 'BookController@update');
$router->add('POST', '/books/{id}/status', 'BookController@updateStatus');
$router->add('POST', '/books/{id}/delete', 'BookController@destroy');
$router->add('GET', '/api/search', 'BookController@search');
$router->add('GET', '/trash', 'BookController@trash');
$router->add('POST', '/books/{id}/restore', 'BookController@restore');
$router->add('POST', '/books/{id}/force-delete', 'BookController@forceDelete');
$router->add('GET', '/calendar', 'CalendarController@index');
$router->add('POST', '/calendar', 'CalendarController@store');
$router->add('POST', '/calendar/{id}/delete', 'CalendarController@destroy');
$router->add('POST', '/books/{id}/review', 'ReviewController@storeOrUpdate');
$router->add('POST', '/books/{id}/journal', 'JournalController@store');
$router->add('POST', '/journal/{id}/delete', 'JournalController@destroy');
$router->add('POST', '/journal/{id}/update', 'JournalController@update');

// Rute Autentikasi & Pengaturan Profil
$router->add('GET', '/login', 'AuthController@login');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/register', 'AuthController@register');
$router->add('POST', '/register', 'AuthController@register');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('GET', '/settings', 'UserController@settings');
$router->add('POST', '/settings', 'UserController@settings');

// 5. Process request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Hapus folder dasar (subfolder) dari URI jika ada
if ($baseDir !== '' && strpos($uri, $baseDir) === 0) {
    $uri = substr($uri, strlen($baseDir));
}

// Hapus prefix /public jika ada
if (strpos($uri, '/public') === 0) {
    $uri = substr($uri, 7);
}

// Hapus prefix /index.php jika diakses langsung tanpa URL rewrite (fallback)
if (strpos($uri, '/index.php') === 0) {
    $uri = substr($uri, 10);
}

// Ensure URI starts with /
if (empty($uri) || $uri[0] !== '/') {
    $uri = '/' . ($uri ?? '');
}

// Remove trailing slash except for root
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
}

// 5.5 Pengecekan Autentikasi (Middleware Sederhana)
$isLoggedIn = isset($_SESSION['user_id']);
$publicRoutes = ['/login', '/register'];

if (!$isLoggedIn && !in_array($uri, $publicRoutes)) {
    // Alihkan tamu ke halaman login
    header("Location: " . BASE_URL . "/login");
    exit;
}

if ($isLoggedIn && in_array($uri, $publicRoutes)) {
    // Pengguna yang sudah masuk tidak boleh membuka form login/register kembali
    header("Location: " . BASE_URL . "/");
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = $_POST['_method'];
}

// Dispatch
$router->dispatch($uri, $method);
