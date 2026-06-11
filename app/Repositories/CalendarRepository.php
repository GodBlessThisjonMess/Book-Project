<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\Book;
use PDO;

class CalendarRepository implements CalendarRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Menambahkan jadwal membaca untuk buku pada tanggal tertentu (mencegah duplikat dengan INSERT IGNORE)
     */
    public function addSchedule($bookId, $date) {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO reading_calendar (book_id, reading_date) 
            VALUES (:book_id, :reading_date)
        ");
        return $stmt->execute([
            'book_id'      => (int)$bookId,
            'reading_date' => $date
        ]);
    }

    /**
     * Menghapus jadwal membaca berdasarkan ID Kalender
     */
    public function removeSchedule($id) {
        $stmt = $this->db->prepare("DELETE FROM reading_calendar WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    /**
     * Mendapatkan semua jadwal membaca dalam bulan dan tahun tertentu dengan informasi judul buku
     */
    public function getSchedulesByMonth($year, $month) {
        $userId = $_SESSION['user_id'] ?? null;
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate   = date("Y-m-t", strtotime($startDate));

        $stmt = $this->db->prepare("
            SELECT rc.id, rc.book_id, rc.reading_date, b.title as book_title, b.status as book_status
            FROM reading_calendar rc
            JOIN books b ON rc.book_id = b.id
            WHERE rc.reading_date BETWEEN :start_date AND :end_date AND b.user_id = :user_id
            ORDER BY rc.reading_date ASC, rc.id ASC
        ");
        $stmt->execute([
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'user_id'    => $userId
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan daftar buku yang dijadwalkan dibaca pada tanggal spesifik
     */
    public function getBooksScheduledForDate($date) {
        $userId = $_SESSION['user_id'] ?? null;
        $stmt = $this->db->prepare("
            SELECT b.* 
            FROM books b
            JOIN reading_calendar rc ON b.id = rc.book_id
            WHERE rc.reading_date = :reading_date AND b.user_id = :user_id
        ");
        $stmt->execute(['reading_date' => $date, 'user_id' => $userId]);
        $rows = $stmt->fetchAll();

        $books = [];
        foreach ($rows as $row) {
            $books[] = new Book($row);
        }
        return $books;
    }
}
