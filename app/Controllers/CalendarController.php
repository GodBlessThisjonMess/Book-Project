<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\CalendarRepositoryInterface;
use App\Repositories\BookRepositoryInterface;

class CalendarController extends Controller {
    private $calendarRepo;
    private $bookRepo;

    /**
     * Injeksi otomatis repositori kalender dan repositori buku oleh Router DI Container
     */
    public function __construct(
        CalendarRepositoryInterface $calendarRepo,
        BookRepositoryInterface $bookRepo
    ) {
        $this->calendarRepo = $calendarRepo;
        $this->bookRepo = $bookRepo;
    }

    /**
     * Menampilkan antarmuka kalender bulanan (Fitur 2)
     */
    public function index() {
        // Ambil tahun dan bulan dari parameter GET (default: hari ini)
        $year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

        // Batasan sanitasi bulan
        if ($month < 1 || $month > 12) {
            $month = (int)date('n');
        }

        try {
            // Ambil seluruh jadwal pada bulan terpilih
            $schedules = $this->calendarRepo->getSchedulesByMonth($year, $month);
            // Ambil semua buku untuk populating form seleksi
            $books = $this->bookRepo->getAll();
        } catch (\Exception $e) {
            $schedules = [];
            $books     = [];
        }

        // Kelompokkan jadwal berdasarkan tanggal ('YYYY-MM-DD') agar mudah dirender di sel kalender
        $schedulesByDate = [];
        foreach ($schedules as $sched) {
            $date = $sched['reading_date'];
            $schedulesByDate[$date][] = $sched;
        }

        // Render halaman kalender
        $this->render('calendar/index', [
            'year'            => $year,
            'month'           => $month,
            'schedulesByDate' => $schedulesByDate,
            'books'           => $books
        ]);
    }

    /**
     * Memproses penambahan jadwal membaca buku baru
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $date   = $_POST['reading_date'] ?? '';

            if ($bookId > 0 && !empty($date)) {
                try {
                    $this->calendarRepo->addSchedule($bookId, $date);
                    $_SESSION['flash_success'] = "Jadwal membaca berhasil disimpan di kalender!";
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = "Gagal menambahkan jadwal membaca.";
                }
            }
        }
        
        // Redirect kembali ke bulan dan tahun dari tanggal yang dipilih
        if (!empty($date)) {
            $time = strtotime($date);
            $this->redirect('/calendar?year=' . date('Y', $time) . '&month=' . date('n', $time));
        } else {
            $this->redirect('/calendar');
        }
    }

    /**
     * Memproses penghapusan jadwal membaca dari tanggal tertentu
     */
    public function destroy($id) {
        $id    = (int)$id;
        $year  = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
        $month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('n');

        try {
            $this->calendarRepo->removeSchedule($id);
            $_SESSION['flash_success'] = "Jadwal membaca berhasil dihapus!";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Gagal menghapus jadwal.";
        }
        
        $this->redirect('/calendar?year=' . $year . '&month=' . $month);
    }
}
