<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\CalendarRepositoryInterface;

class DashboardController extends Controller {
    private $bookRepo;
    private $calendarRepo;

    /**
     * Konstruktor diinjeksi dengan BookRepository dan CalendarRepository sekaligus!
     */
    public function __construct(
        BookRepositoryInterface $bookRepo,
        CalendarRepositoryInterface $calendarRepo
    ) {
        $this->bookRepo = $bookRepo;
        $this->calendarRepo = $calendarRepo;
    }

    /**
     * Menampilkan halaman statistik utama dashboard & cek konfirmasi membaca harian (Fitur 3 & 6)
     */
    public function index() {
        // 1. Ambil data statistik koleksi buku
        try {
            $stats = $this->bookRepo->getStats();
        } catch (\Exception $e) {
            $stats = [
                'Not Read'   => 0,
                'On Going'   => 0,
                'Done'       => 0,
                'Unfinished' => 0
            ];
        }

        // 2. Cek buku terakhir yang diberi catatan untuk pop up melanjutkan membaca (saat buka website)
        $scheduledBook = null;
        try {
            $scheduledBook = $this->bookRepo->getLatestAddedBook();
        } catch (\Exception $e) {
            $scheduledBook = null;
        }

        // 3. Ambil buku terakhir untuk CTA
        $latestBook = $scheduledBook;

        // 4. Ambil daftar buku pilihan terulas dengan jurnal (Preview)
        $reviewedBooks = [];
        try {
            $reviewedBooks = $this->bookRepo->getReviewedBooksWithJournals();
        } catch (\Exception $e) {
            $reviewedBooks = [];
        }

        // Render view dashboard utama
        $this->render('dashboard/index', [
            'stats'         => $stats,
            'scheduledBook' => $scheduledBook,
            'latestBook'    => $latestBook,
            'reviewedBooks' => $reviewedBooks
        ]);
    }

    /**
     * Memproses submit modal konfirmasi harian (Fitur 3)
     */
    public function confirmActivity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $status = $_POST['status'] ?? 'On Going';

            if ($bookId > 0 && in_array($status, ['On Going', 'Unfinished'])) {
                try {
                    $this->bookRepo->updateStatus($bookId, $status);
                    $_SESSION['flash_success'] = "Aktivitas membaca hari ini berhasil dikonfirmasi sebagai '" . ($status === 'On Going' ? 'Sedang Dibaca' : 'Tidak Selesai') . "'.";
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = "Terjadi kesalahan saat menyimpan konfirmasi.";
                }
            }
        }
        $this->redirect('/');
    }
}
