<?php

namespace App\Repositories;

interface BookRepositoryInterface {
    /**
     * Mendapatkan semua koleksi buku
     *
     * @return array Array of Book Models
     */
    public function getAll();

    /**
     * Mencari buku berdasarkan ID
     *
     * @param int $id
     * @return \App\Models\Book|null
     */
    public function findById($id);

    /**
     * Menambahkan buku baru ke basis data
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data);

    /**
     * Memperbarui detail buku
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data);

    /**
     * Menghapus buku dari basis data
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Mengambil statistik ringkasan jumlah buku berdasarkan status
     *
     * @return array [Not Read => X, On Going => Y, ...]
     */
    public function getStats();

    /**
     * Memperbarui hanya status buku
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status);

    /**
     * Mengambil buku terakhir yang ditambahkan ke koleksi dan belum selesai dibaca
     *
     * @return \App\Models\Book|null
     */
    public function getLatestAddedBook();

    /**
     * Mencari buku berdasarkan kecocokan judul atau penulis
     *
     * @param string $query
     * @return array Array of Book Models
     */
    public function search($query);

    /**
     * Mengambil semua buku yang berada di tong sampah (soft deleted)
     *
     * @return array Array of Book Models
     */
    public function getTrash();

    /**
     * Mengembalikan buku dari tong sampah ke koleksi aktif
     *
     * @param int $id
     * @return bool
     */
    public function restore($id);

    /**
     * Menghapus buku secara permanen dari basis data
     *
     * @param int $id
     * @return bool
     */
    public function forceDelete($id);

    /**
     * Mengambil daftar buku yang sudah diulas (memiliki rating bintang) dan memiliki jurnal aktivitas
     *
     * @return array Array of raw arrays/objects with review and latest note data
     */
    public function getReviewedBooksWithJournals();
}
