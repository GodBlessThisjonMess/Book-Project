<?php

require_once __DIR__ . '/../app/Config/Database.php';

use App\Config\Database;

try {
    $db = Database::getConnection();
    
    // Check if table columns exist
    $query = $db->query("DESCRIBE `books`");
    $columns = $query->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in `books` table:\n";
    print_r($columns);
    
    if (!in_array('cover_url', $columns)) {
        echo "Adding `cover_url` column...\n";
        $db->exec("ALTER TABLE `books` ADD COLUMN `cover_url` VARCHAR(2048) NULL AFTER `status`");
        echo "Added `cover_url` successfully.\n";
    } else {
        echo "`cover_url` column already exists.\n";
    }
    
    if (!in_array('is_deleted', $columns)) {
        echo "Adding `is_deleted` column...\n";
        $db->exec("ALTER TABLE `books` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cover_url`");
        $db->exec("ALTER TABLE `books` ADD INDEX `idx_books_deleted` (`is_deleted`)");
        echo "Added `is_deleted` successfully.\n";
    } else {
        echo "`is_deleted` column already exists.\n";
    }
    
    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
