<?php
// Debug test untuk search API

require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Repositories/BookRepository.php';

header('Content-Type: application/json; charset=utf-8');

try {
  
    $db = \App\Config\Database::getConnection();
    
    
    $bookRepo = new \App\Repositories\BookRepository($db);

    $allBooks = $bookRepo->getAll();
    $totalBooks = count($allBooks);
 
    $testQuery = 'a'; 
    $searchResults = $bookRepo->search($testQuery);
    $searchCount = count($searchResults);
    
  
    $response = [
        'status' => 'ok',
        'tests' => [
            'database_connected' => true,
            'total_books' => $totalBooks,
            'search_test_query' => $testQuery,
            'search_results_found' => $searchCount,
            'sample_results' => []
        ]
    ];
    
   
    foreach (array_slice($searchResults, 0, 3) as $book) {
        $response['tests']['sample_results'][] = [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
