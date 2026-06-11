<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Book;
use PDO;

class BookRepository implements BookRepositoryInterface {
    private $db;

    public function __construct() {
        // Mendapatkan koneksi tunggal PDO
        $this->db = Database::getConnection();
    }

    /**
     * Mendapatkan semua koleksi buku aktif (is_deleted = 0)
     */
    public function getAll() {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT b.*, 
                   (SELECT COUNT(*) FROM reading_calendar rc WHERE rc.book_id = b.id) as total_days,
                   (SELECT COUNT(*) FROM reading_calendar rc WHERE rc.book_id = b.id AND rc.reading_date <= CURRENT_DATE) as passed_days,
                   (SELECT COALESCE(MAX(j.read_to_page), 0) FROM journals j WHERE j.book_id = b.id) as last_page_read
            FROM books b 
            WHERE b.is_deleted = 0 AND b.user_id = :user_id
            ORDER BY b.id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();
        
        $books = [];
        foreach ($rows as $row) {
            $books[] = new Book($row);
        }
        return $books;
    }

    /**
     * Mencari buku aktif berdasarkan ID
     */
    public function findById($id) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT b.*, 
                   (SELECT COUNT(*) FROM reading_calendar rc WHERE rc.book_id = b.id) as total_days,
                   (SELECT COUNT(*) FROM reading_calendar rc WHERE rc.book_id = b.id AND rc.reading_date <= CURRENT_DATE) as passed_days,
                   (SELECT COALESCE(MAX(j.read_to_page), 0) FROM journals j WHERE j.book_id = b.id) as last_page_read
            FROM books b 
            WHERE b.id = :id AND b.is_deleted = 0 AND b.user_id = :user_id
        ");
        $stmt->execute(['id' => (int)$id, 'user_id' => $userId]);
        $row = $stmt->fetch();
        
        return $row ? new Book($row) : null;
    }

    /**
     * Menyimpan data buku baru beserta cover_url dan user_id
     */
    public function create(array $data) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            INSERT INTO books (title, author, description, status, cover_url, total_pages, user_id) 
            VALUES (:title, :author, :description, :status, :cover_url, :total_pages, :user_id)
        ");
        return $stmt->execute([
            'title'       => htmlspecialchars(strip_tags($data['title'])),
            'author'      => htmlspecialchars(strip_tags($data['author'])),
            'description' => isset($data['description']) ? htmlspecialchars($data['description']) : null,
            'status'      => $data['status'] ?? 'Not Read',
            'cover_url'   => !empty($data['cover_url']) ? htmlspecialchars(strip_tags($data['cover_url'])) : null,
            'total_pages' => isset($data['total_pages']) ? (int)$data['total_pages'] : 0,
            'user_id'     => $userId
        ]);
    }

    /**
     * Memperbarui informasi buku beserta cover_url
     */
    public function update($id, array $data) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            UPDATE books 
            SET title = :title, author = :author, description = :description, status = :status, cover_url = :cover_url, total_pages = :total_pages
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'id'          => (int)$id,
            'title'       => htmlspecialchars(strip_tags($data['title'])),
            'author'      => htmlspecialchars(strip_tags($data['author'])),
            'description' => isset($data['description']) ? htmlspecialchars($data['description']) : null,
            'status'      => $data['status'],
            'cover_url'   => !empty($data['cover_url']) ? htmlspecialchars(strip_tags($data['cover_url'])) : null,
            'total_pages' => isset($data['total_pages']) ? (int)$data['total_pages'] : 0,
            'user_id'     => $userId
        ]);
    }

    /**
     * Soft Delete Buku (Mengubah bendera is_deleted = 1)
     */
    public function delete($id) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("UPDATE books SET is_deleted = 1 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => (int)$id, 'user_id' => $userId]);
    }

    /**
     * Mendapatkan daftar semua buku di dalam tong sampah (is_deleted = 1)
     */
    public function getTrash() {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT * FROM books 
            WHERE is_deleted = 1 AND user_id = :user_id
            ORDER BY id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();
        
        $books = [];
        foreach ($rows as $row) {
            $books[] = new Book($row);
        }
        return $books;
    }

    /**
     * Mengembalikan buku dari tong sampah (is_deleted = 0)
     */
    public function restore($id) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("UPDATE books SET is_deleted = 0 WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => (int)$id, 'user_id' => $userId]);
    }

    /**
     * Menghapus buku secara permanen dari basis data
     */
    public function forceDelete($id) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => (int)$id, 'user_id' => $userId]);
    }

    /**
     * Mengambil statistik status buku aktif untuk dashboard
     */
    public function getStats() {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as count 
            FROM books 
            WHERE is_deleted = 0 AND user_id = :user_id
            GROUP BY status
        ");
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();
        
        $stats = [
            'Not Read'   => 0,
            'On Going'   => 0,
            'Done'       => 0,
            'Unfinished' => 0
        ];
        
        foreach ($rows as $row) {
            $stats[$row['status']] = (int)$row['count'];
        }
        
        return $stats;
    }

    /**
     * Memperbarui status buku saja
     */
    public function updateStatus($id, $status) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("UPDATE books SET status = :status WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            'id'     => (int)$id,
            'status' => $status,
            'user_id' => $userId
        ]);
    }

    /**
     * Mengambil buku terakhir yang aktif berdasarkan catatan jurnal paling baru (status != Done)
     */
    public function getLatestAddedBook() {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT b.*, 
                   (SELECT MAX(j.logged_at) FROM journals j WHERE j.book_id = b.id) as last_activity
            FROM books b
            WHERE b.status != 'Done' AND b.is_deleted = 0 AND b.user_id = :user_id
              AND EXISTS (SELECT 1 FROM journals j WHERE j.book_id = b.id)
            ORDER BY last_activity DESC 
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ? new Book($row) : null;
    }

    /**
     * Melakukan kueri pencarian teks pada judul dan penulis (Notion-style Search API)
     */
    public function search($query) {
        $userId = $_SESSION['user_id'] ?? null;
        $pattern = '%' . $query . '%';
        $stmt = $this->db->prepare("
            SELECT * FROM books 
            WHERE is_deleted = 0 AND (title LIKE :query OR author LIKE :query) AND user_id = :user_id
            ORDER BY id DESC
            LIMIT 10
        ");
        $stmt->execute(['query' => $pattern, 'user_id' => $userId]);
        $rows = $stmt->fetchAll();
        
        $books = [];
        foreach ($rows as $row) {
            $books[] = new Book($row);
        }
        return $books;
    }

    /**
     * Mengambil daftar buku yang memiliki review/ulasan bintang serta minimal satu catatan jurnal
     */
    public function getReviewedBooksWithJournals() {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT b.*, r.rating, r.review_text,
                   (SELECT notes FROM journals j WHERE j.book_id = b.id ORDER BY j.logged_at DESC, j.id DESC LIMIT 1) as latest_note,
                   (SELECT logged_at FROM journals j WHERE j.book_id = b.id ORDER BY j.logged_at DESC, j.id DESC LIMIT 1) as latest_note_date
            FROM books b
            JOIN reviews r ON b.id = r.book_id
            WHERE b.is_deleted = 0 AND b.user_id = :user_id
              AND EXISTS (SELECT 1 FROM journals j WHERE j.book_id = b.id)
            ORDER BY r.updated_at DESC, b.id DESC
            LIMIT 3
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
