<?php

namespace App\Repositories;

interface JournalRepositoryInterface {
    /**
     * Mengambil semua catatan jurnal berdasarkan ID Buku secara kronologis terbalik
     *
     * @param int $bookId
     * @return array Array of Journal Models
     */
    public function getByBookId($bookId);

    /**
     * Membuat catatan jurnal baru
     *
     * @param array $data
     * @return bool
     */
    public function create(array $data);

    /**
     * Menghapus catatan jurnal berdasarkan ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Memperbarui catatan jurnal berdasarkan ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data);
}
