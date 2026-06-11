<?php

namespace App\Repositories;

interface ReviewRepositoryInterface {
    /**
     * Mencari review berdasarkan ID Buku
     *
     * @param int $bookId
     * @return \App\Models\Review|null
     */
    public function findByBookId($bookId);

    /**
     * Menyimpan atau memperbarui review buku (UPSERT)
     *
     * @param array $data
     * @return bool
     */
    public function save(array $data);

    /**
     * Menghapus review berdasarkan ID
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);
}
