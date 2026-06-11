<?php

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Repositories/BookRepositoryInterface.php';
require_once __DIR__ . '/../app/Repositories/BookRepository.php';

use App\Config\Database;
use App\Repositories\BookRepository;

try {
    $db = Database::getConnection();
    
    // 1. Show all books in the database to see what exists
    $all = $db->query("SELECT id, title, author, is_deleted FROM books")->fetchAll();
    echo "=== DAFTAR SEMUA BUKU DI DB ===\n";
    foreach ($all as $b) {
        echo "ID: {$b['id']} | Title: '{$b['title']}' | Author: '{$b['author']}' | Deleted: {$b['is_deleted']}\n";
    }
    echo "===============================\n\n";

    // 2. Perform a test search
    $repo = new BookRepository();
    
    // Test queries
    $queries = ['Bumi', 'bumi', 'Pramoedya', 'a'];
    
    foreach ($queries as $q) {
        echo "Searching for: '$q'\n";
        $results = $repo->search($q);
        echo "Found " . count($results) . " results:\n";
        foreach ($results as $book) {
            echo " - ID: {$book->id} | Title: {$book->title} | Author: {$book->author}\n";
        }
        echo "-------------------------------\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
