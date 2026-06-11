<?php
// Debug file untuk memverifikasi BASE_URL dan routing di environment berbeda
// Akses file ini dengan URL dan periksa nilai BASE_URL dan $_SERVER variables

error_reporting(E_ALL);
ini_set('display_errors', 1);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Simulated BASE_URL calculation
if (php_sapi_name() === 'cli-server') {
    $baseDir = '';
} else {
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    if (substr_count($scriptPath, '/public/') > 0) {
        $baseDir = str_replace('/public/index.php', '', $scriptPath);
    } else {
        $baseDir = '/';
    }
    
    if ($baseDir === '/' || empty($baseDir)) {
        $baseDir = '';
    } else {
        $baseDir = rtrim($baseDir, '/');
    }
}

$BASE_URL = $protocol . $host . $baseDir;

?>
<!DOCTYPE html>
<html>
<head>
    <title>BASE_URL Debug</title>
    <style>
        body { font-family: monospace; margin: 20px; }
        .box { border: 1px solid #ccc; padding: 15px; margin: 10px 0; background: #f5f5f5; }
        .label { font-weight: bold; color: #333; }
        .value { color: #0066cc; }
        .success { color: #00aa00; }
        .warning { color: #ff6600; }
    </style>
</head>
<body>
    <h1>BASE_URL Debug Information</h1>
    
    <div class="box">
        <div class="label">Environment:</div>
        <div class="value"><?= php_sapi_name() === 'cli-server' ? 'PHP Built-in Server' : 'Apache/Other Web Server' ?></div>
    </div>
    
    <div class="box">
        <div class="label">Server Variables:</div>
        <div>HTTP_HOST: <span class="value"><?= $_SERVER['HTTP_HOST'] ?></span></div>
        <div>SCRIPT_NAME: <span class="value"><?= $_SERVER['SCRIPT_NAME'] ?></span></div>
        <div>REQUEST_URI: <span class="value"><?= $_SERVER['REQUEST_URI'] ?></span></div>
        <div>PHP_SELF: <span class="value"><?= $_SERVER['PHP_SELF'] ?></span></div>
    </div>
    
    <div class="box">
        <div class="label">Calculated Values:</div>
        <div>baseDir: <span class="value"><?= $baseDir === '' ? '(empty)' : $baseDir ?></span></div>
        <div>BASE_URL: <span class="success"><?= $BASE_URL ?></span></div>
    </div>
    
    <div class="box">
        <div class="label">Expected URLs:</div>
        <div>Dashboard: <span class="value"><?= $BASE_URL ?>/</span></div>
        <div>Books: <span class="value"><?= $BASE_URL ?>/books</span></div>
        <div>Calendar: <span class="value"><?= $BASE_URL ?>/calendar</span></div>
        <div>Create Book: <span class="value"><?= $BASE_URL ?>/books/create</span></div>
        <div>API Search: <span class="value"><?= $BASE_URL ?>/api/search?q=test</span></div>
    </div>
    
    <div class="box">
        <div class="label">Setup Instructions:</div>
        <div style="margin-top: 10px;">
            <p><strong>For PHP Built-in Server:</strong></p>
            <pre style="background: #fff; padding: 10px; border-radius: 3px;">cd /path/to/book-reading-tracker
php -S localhost:8000 -t public</pre>
            <p><strong>For htdocs (Apache):</strong></p>
            <pre style="background: #fff; padding: 10px; border-radius: 3px;">Copy entire folder to: C:\xampp\htdocs\book-reading-tracker\
Access: http://localhost/book-reading-tracker</pre>
            <p><strong>Note:</strong> All navigation links use BASE_URL constant, so they should work in both environments.</p>
        </div>
    </div>
</body>
</html>
