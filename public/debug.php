<?php
// Debug file untuk memahami Apache REQUEST variables
echo "<pre>";
echo "=== REQUEST DEBUG INFO ===\n\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "\n";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'NOT SET') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "\n";
echo "\nPATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "\n";
echo "ORIG_PATH_INFO: " . ($_SERVER['ORIG_PATH_INFO'] ?? 'NOT SET') . "\n";
echo "REDIRECT_URL: " . ($_SERVER['REDIRECT_URL'] ?? 'NOT SET') . "\n";
echo "\n=== PHP_SAPI ===\n";
echo php_sapi_name() . "\n";
echo "\n=== CALCULATED BASE_URL ===\n";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptDirName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if (basename($scriptDirName) === 'public') {
    $scriptDirName = dirname($scriptDirName);
}

$scriptDirName = rtrim($scriptDirName, '/');
if (empty($scriptDirName) || $scriptDirName === '/') {
    $scriptDirName = '';
}
$BASE_URL = $protocol . $domainName . $scriptDirName;
echo "BASE_URL: " . $BASE_URL . "\n";
echo "\n</pre>";
?>
