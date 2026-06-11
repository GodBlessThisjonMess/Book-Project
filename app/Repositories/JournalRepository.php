<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Journal;
use PDO;

class JournalRepository implements JournalRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Mengambil semua catatan jurnal berdasarkan ID Buku secara kronologis terbalik
     */
    public function getByBookId($bookId) {
        $stmt = $this->db->prepare("SELECT * FROM journals WHERE book_id = :book_id ORDER BY logged_at DESC, id DESC");
        $stmt->execute(['book_id' => (int)$bookId]);
        $rows = $stmt->fetchAll();

        $journals = [];
        foreach ($rows as $row) {
            $journals[] = new Journal($row);
        }
        return $journals;
    }

    /**
     * Membuat catatan jurnal baru
     */
    public function create(array $data) {
        $stmt = $this->db->prepare("
            INSERT INTO journals (book_id, notes, read_to_page, logged_at) 
            VALUES (:book_id, :notes, :read_to_page, :logged_at)
        ");
        return $stmt->execute([
            'book_id'      => (int)$data['book_id'],
            'notes'        => htmlspecialchars($data['notes']),
            'read_to_page' => isset($data['read_to_page']) ? (int)$data['read_to_page'] : 0,
            'logged_at'    => $data['logged_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Menghapus catatan jurnal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM journals WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    /**
     * Memperbarui catatan jurnal
     */
    public function update($id, array $data) {
        $stmt = $this->db->prepare("UPDATE journals SET notes = :notes, read_to_page = :read_to_page WHERE id = :id");
        return $stmt->execute([
            'id'           => (int)$id,
            'notes'        => htmlspecialchars($data['notes']),
            'read_to_page' => isset($data['read_to_page']) ? (int)$data['read_to_page'] : 0
        ]);
    }
}
