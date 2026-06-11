<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\ReviewRepositoryInterface;

class ReviewController extends Controller {
    private $reviewRepo;

    public function __construct(ReviewRepositoryInterface $reviewRepo) {
        $this->reviewRepo = $reviewRepo;
    }

    /**
     * Memproses penyimpanan atau pembaruan ulasan buku (Fitur 4)
     *
     * @param int $bookId ID Buku yang diulas
     */
    public function storeOrUpdate($bookId) {
        $bookId = (int)$bookId;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'book_id'     => $bookId,
                'rating'      => isset($_POST['rating']) ? (int)$_POST['rating'] : 5,
                'review_text' => $_POST['review_text'] ?? ''
            ];

            try {
                $this->reviewRepo->save($data);
                $_SESSION['flash_success'] = "Ulasan buku berhasil disimpan!";
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = "Gagal menyimpan ulasan buku.";
            }
        }
        $this->redirect('/books/' . $bookId);
    }
}
