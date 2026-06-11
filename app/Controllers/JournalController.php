<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\JournalRepositoryInterface;
use App\Repositories\CalendarRepositoryInterface;

class JournalController extends Controller {
    private $journalRepo;
    private $calendarRepo;

    /**
     * Konstruktor diinjeksi dengan repositori jurnal dan repositori kalender sekaligus
     */
    public function __construct(
        JournalRepositoryInterface $journalRepo,
        CalendarRepositoryInterface $calendarRepo
    ) {
        $this->journalRepo = $journalRepo;
        $this->calendarRepo = $calendarRepo;
    }

    /**
     * Memproses penulisan catatan jurnal membaca baru dan merekam aktivitas membaca di kalender
     *
     * @param int $bookId ID Buku terkait
     */
    public function store($bookId) {
        $bookId = (int)$bookId;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes = $_POST['notes'] ?? '';
            $readToPage = isset($_POST['read_to_page']) ? (int)$_POST['read_to_page'] : 0;
            $loggedAtDate = $_POST['logged_at'] ?? date('Y-m-d');
            
            // Gabungkan tanggal input dengan waktu saat ini untuk format DATETIME basis data
            $loggedAt = $loggedAtDate . ' ' . date('H:i:s');

            if (!empty($notes)) {
                try {
                    // 1. Simpan catatan jurnal ke basis data
                    $this->journalRepo->create([
                        'book_id'      => $bookId,
                        'notes'        => $notes,
                        'read_to_page' => $readToPage,
                        'logged_at'    => $loggedAt
                    ]);
                    
                    // 2. DAFTARKAN OTOMATIS sebagai hari membaca di kalender
                    $this->calendarRepo->addSchedule($bookId, $loggedAtDate);
                    
                    $_SESSION['flash_success'] = "Catatan jurnal disimpan dan aktivitas membaca dicatat pada kalender!";
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = "Gagal menyimpan catatan jurnal.";
                }
            }
        }
        $this->redirect('/books/' . $bookId);
    }

    /**
     * Memproses penghapusan catatan jurnal
     *
     * @param int $id ID Catatan Jurnal
     */
    public function destroy($id) {
        $id = (int)$id;
        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
        
        try {
            $this->journalRepo->delete($id);
            $_SESSION['flash_success'] = "Catatan jurnal berhasil dihapus!";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Gagal menghapus catatan jurnal.";
        }
        
        if ($bookId > 0) {
            $this->redirect('/books/' . $bookId);
        } else {
            $this->redirect('/books');
        }
    }

    /**
     * Memproses pembaruan catatan jurnal
     *
     * @param int $id ID Catatan Jurnal
     */
    public function update($id) {
        $id = (int)$id;
        $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes = $_POST['notes'] ?? '';
            $readToPage = isset($_POST['read_to_page']) ? (int)$_POST['read_to_page'] : 0;
            
            if (!empty($notes)) {
                try {
                    $this->journalRepo->update($id, [
                        'notes'        => $notes,
                        'read_to_page' => $readToPage
                    ]);
                    $_SESSION['flash_success'] = "Catatan jurnal berhasil diperbarui!";
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = "Gagal memperbarui catatan jurnal.";
                }
            }
        }
        
        if ($bookId > 0) {
            $this->redirect('/books/' . $bookId);
        } else {
            $this->redirect('/books');
        }
    }
}
