<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Review;
use PDO;

class ReviewRepository implements ReviewRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Mencari review berdasarkan ID Buku
     */
    public function findByBookId($bookId) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE book_id = :book_id");
        $stmt->execute(['book_id' => (int)$bookId]);
        $row = $stmt->fetch();
        return $row ? new Review($row) : null;
    }

    /**
     * Menyimpan atau memperbarui review buku (UPSERT)
     */
    public function save(array $data) {
        $existing = $this->findByBookId($data['book_id']);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE reviews 
                SET rating = :rating, review_text = :review_text 
                WHERE book_id = :book_id
            ");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO reviews (book_id, rating, review_text) 
                VALUES (:book_id, :rating, :review_text)
            ");
        }
        
        return $stmt->execute([
            'book_id'     => (int)$data['book_id'],
            'rating'      => (int)$data['rating'],
            'review_text' => isset($data['review_text']) ? htmlspecialchars($data['review_text']) : null
        ]);
    }

    /**
     * Menghapus review
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}
