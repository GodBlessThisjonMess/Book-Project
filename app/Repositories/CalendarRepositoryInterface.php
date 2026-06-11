<?php

namespace App\Repositories;

interface CalendarRepositoryInterface {
    /**
     * Menambahkan jadwal membaca untuk buku pada tanggal tertentu
     *
     * @param int $bookId ID Buku
     * @param string $date Format YYYY-MM-DD
     * @return bool
     */
    public function addSchedule($bookId, $date);

    /**
     * Menghapus jadwal membaca berdasarkan ID Kalender
     *
     * @param int $id ID Kalender
     * @return bool
     */
    public function removeSchedule($id);

    /**
     * Mendapatkan semua jadwal membaca dalam bulan dan tahun tertentu
     *
     * @param int $year
     * @param int $month
     * @return array Array data jadwal lengkap dengan judul buku (hasil JOIN)
     */
    public function getSchedulesByMonth($year, $month);

    /**
     * Mendapatkan daftar buku yang dijadwalkan dibaca pada tanggal spesifik
     *
     * @param string $date Format YYYY-MM-DD
     * @return array Array of Book Models
     */
    public function getBooksScheduledForDate($date);
}
