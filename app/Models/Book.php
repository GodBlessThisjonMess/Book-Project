<?php

namespace App\Models;

class Book {
    public $id;
    public $title;
    public $author;
    public $description;
    public $status;
    public $createdAt;
    public $updatedAt;
    public $totalDays;
    public $passedDays;
    public $coverUrl;
    public $isDeleted;
    public $totalPages;
    public $lastPageRead;

    /**
     * Konstruktor Entitas Book
     *
     * @param array $data Data baris dari database
     */
    public function __construct($data = []) {
        $this->id          = isset($data['id']) ? (int)$data['id'] : null;
        $this->title       = $data['title'] ?? '';
        $this->author      = $data['author'] ?? '';
        $this->description = $data['description'] ?? null;
        $this->status      = $data['status'] ?? 'Not Read';
        $this->createdAt   = $data['created_at'] ?? null;
        $this->updatedAt   = $data['updated_at'] ?? null;
        $this->totalDays   = isset($data['total_days']) ? (int)$data['total_days'] : 0;
        $this->passedDays  = isset($data['passed_days']) ? (int)$data['passed_days'] : 0;
        $this->coverUrl    = $data['cover_url'] ?? null;
        $this->isDeleted   = isset($data['is_deleted']) ? (int)$data['is_deleted'] : 0;
        $this->totalPages  = isset($data['total_pages']) ? (int)$data['total_pages'] : 0;
        $this->lastPageRead = isset($data['last_page_read']) ? (int)$data['last_page_read'] : 0;
    }

    /**
     * Menghitung persentase kemajuan membaca berdasarkan status buku dan data jadwal kalender
     */
    public function getProgressPercent() {
        if ($this->status === 'Done') {
            return 100;
        }
        if ($this->status === 'Not Read' || $this->totalPages <= 0) {
            return 0;
        }
        
        $percent = (int)round(($this->lastPageRead / $this->totalPages) * 100);
        
        // Batasi kemajuan maksimal 99% jika buku belum di-set manual sebagai 'Done' (selesai)
        if ($percent >= 100 && $this->status !== 'Done') {
            return 99;
        }
        return $percent;
    }


    /**
     * Memeriksa apakah buku sedang dibaca
     */
    public function isOngoing() {
        return $this->status === 'On Going';
    }

    /**
     * Memeriksa apakah buku telah selesai dibaca
     */
    public function isDone() {
        return $this->status === 'Done';
    }

    /**
     * Mengembalikan teks representasi label status yang ramah bagi pengguna
     */
    public function getStatusLabel() {
        switch ($this->status) {
            case 'On Going':
                return 'Sedang Dibaca';
            case 'Done':
                return 'Selesai';
            case 'Unfinished':
                return 'Tidak Selesai';
            case 'Not Read':
            default:
                return 'Belum Dibaca';
        }
    }

    /**
     * Mengembalikan gradien warna latar cover secara deterministik berdasarkan ID Buku
     */
    public function getCoverGradient() {
        $gradients = [
            'linear-gradient(135deg, #007979, #004d4d)', // Teal Khas
            'linear-gradient(135deg, #2b4c7e, #1a3050)', // Slate Blue
            'linear-gradient(135deg, #7c3aed, #5b21b6)', // Purple
            'linear-gradient(135deg, #b45309, #78350f)', // Terracotta / Amber
            'linear-gradient(135deg, #0f766e, #115e59)', // Forest Green
            'linear-gradient(135deg, #9f1239, #4c0519)', // Crimson Wine
            'linear-gradient(135deg, #1e293b, #0f172a)', // Charcoal
            'linear-gradient(135deg, #d97706, #92400e)'  // Mustard Gold
        ];
        $index = $this->id ? ($this->id % count($gradients)) : 0;
        return $gradients[$index];
    }
}
