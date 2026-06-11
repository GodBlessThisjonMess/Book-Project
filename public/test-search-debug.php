<?php
// Load PSR-4 autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    
    if (strpos($class, $prefix) === 0) {
        $relative_class = substr($class, strlen($prefix));
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

use App\Config\Database;

$db = Database::getConnection();

echo "<h1>Testing Search Query</h1>";
echo "<p>Testing with query='1984'</p>";

try {
    $query = '1984';
    $pattern = '%' . $query . '%';
    $stmt = $db->prepare("
        SELECT * FROM books 
        WHERE is_deleted = 0 AND (title LIKE ? OR author LIKE ?)
        ORDER BY id DESC
        LIMIT 10
    ");
    
    echo "<p>Statement prepared successfully</p>";
    
    echo "<p>Parameters: ['" . $pattern . "', '" . $pattern . "']</p>";
    
    $result = $stmt->execute([$pattern, $pattern]);
    
    echo "<p>Statement executed successfully</p>";
    
    $rows = $stmt->fetchAll();
    echo "<p>Rows found: " . count($rows) . "</p>";
    echo "<pre>" . json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
} catch (\Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>Code: " . $e->getCode() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
