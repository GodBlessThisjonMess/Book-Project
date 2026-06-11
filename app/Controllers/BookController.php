<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\BookRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use App\Repositories\JournalRepositoryInterface;

class BookController extends Controller {
    private $bookRepo;
    private $reviewRepo;
    private $journalRepo;

    /**
     * Konstruktor diinjeksi dengan tiga Repository sekaligus oleh Router DI Container!
     */
    public function __construct(
        BookRepositoryInterface $bookRepo,
        ReviewRepositoryInterface $reviewRepo,
        JournalRepositoryInterface $journalRepo
    ) {
        $this->bookRepo = $bookRepo;
        $this->reviewRepo = $reviewRepo;
        $this->journalRepo = $journalRepo;
    }

    /**
     * Menampilkan daftar semua buku di perpustakaan personal
     */
    public function index() {
        try {
            $books = $this->bookRepo->getAll();
        } catch (\Exception $e) {
            $books = [];
        }
        
        $this->render('books/index', [
            'books' => $books
        ]);
    }

    /**
     * Menampilkan form untuk menambahkan buku baru
     */
    public function create() {
        $this->render('books/create');
    }

    /**
     * Memproses penyimpanan buku baru
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => $_POST['title'] ?? '',
                'author'      => $_POST['author'] ?? '',
                'description' => $_POST['description'] ?? '',
                'status'      => $_POST['status'] ?? 'Not Read',
                'cover_url'   => $_POST['cover_url'] ?? '',
                'total_pages' => isset($_POST['total_pages']) ? (int)$_POST['total_pages'] : 0
            ];

            if (!empty($data['title']) && !empty($data['author'])) {
                try {
                    $this->bookRepo->create($data);
                    $_SESSION['flash_success'] = "Buku baru berhasil disimpan!";
                    $this->redirect('/books');
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = "Gagal menyimpan data buku.";
                }
            }
        }
        $this->redirect('/books/create');
    }

    /**
     * Menampilkan detail satu buku lengkap dengan review dan jurnal pribadinya
     *
     * @param int $id ID Buku
     */
    public function show($id) {
        $id = (int)$id;
        
        try {
            $book = $this->bookRepo->findById($id);
            
            if (!$book) {
                $_SESSION['flash_error'] = "Buku tidak ditemukan.";
                $this->redirect('/books');
            }

            // Ambil review dan catatan jurnal untuk buku ini
            $review = $this->reviewRepo->findByBookId($id);
            $journals = $this->journalRepo->getByBookId($id);
            
        } catch (\Exception $e) {
            $book = null;
            $review = null;
            $journals = [];
        }

        // Kirim semua variabel ke view show
        $this->render('books/show', [
            'book'     => $book,
            'review'   => $review,
            'journals' => $journals
        ]);
    }

    /**
     * Memproses pembaruan data buku (termasuk status manual)
     *
     * @param int $id ID Buku
     */
    public function update($id) {
        $id = (int)$id;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $book = $this->bookRepo->findById($id);
                if (!$book) {
                    $_SESSION['flash_error'] = "Buku tidak ditemukan.";
                    $this->redirect('/books');
                }

                $data = [
                    'title'       => $_POST['title'] ?? $book->title,
                    'author'      => $_POST['author'] ?? $book->author,
                    'description' => $_POST['description'] ?? $book->description,
                    'status'      => $_POST['status'] ?? $book->status,
                    'cover_url'   => isset($_POST['cover_url']) ? $_POST['cover_url'] : $book->coverUrl,
                    'total_pages' => isset($_POST['total_pages']) ? (int)$_POST['total_pages'] : $book->totalPages
                ];

                if (!empty($data['title']) && !empty($data['author'])) {
                    $this->bookRepo->update($id, $data);
                    $_SESSION['flash_success'] = "Informasi buku berhasil diperbarui!";
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = "Gagal memperbarui data buku.";
            }
        }
        $this->redirect('/books/' . $id);
    }

    /**
     * Memproses pembaruan status buku saja (Ubah Status Cepat)
     *
     * @param int $id ID Buku
     */
    public function updateStatus($id) {
        $id = (int)$id;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? 'Not Read';
            try {
                $this->bookRepo->updateStatus($id, $status);
                $_SESSION['flash_success'] = "Status buku berhasil diperbarui!";
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = "Gagal memperbarui status buku.";
            }
        }
        $this->redirect('/books/' . $id);
    }

    /**
     * Soft Delete Buku (Membuang buku ke tong sampah sementara)
     *
     * @param int $id ID Buku
     */
    public function destroy($id) {
        $id = (int)$id;
        
        try {
            $this->bookRepo->delete($id);
            $_SESSION['flash_success'] = "Buku berhasil dibuang ke Tong Sampah!";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Gagal membuang buku.";
        }
        $this->redirect('/books');
    }

    // ====================================================================
    // FITUR TRASH & SOFT-DELETE MANAGEMENT
    // ====================================================================

    /**
     * Menampilkan daftar semua buku yang ada di dalam tong sampah (is_deleted = 1)
     */
    public function trash() {
        try {
            $books = $this->bookRepo->getTrash();
        } catch (\Exception $e) {
            $books = [];
        }
        
        $this->render('books/trash', [
            'books' => $books
        ]);
    }

    /**
     * Mengembalikan buku dari tong sampah ke dalam koleksi aktif (is_deleted = 0)
     *
     * @param int $id ID Buku
     */
    public function restore($id) {
        $id = (int)$id;
        
        try {
            $this->bookRepo->restore($id);
            $_SESSION['flash_success'] = "Buku berhasil dikembalikan dari Tong Sampah!";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Gagal memulihkan buku.";
        }
        $this->redirect('/trash');
    }

    /**
     * Menghapus buku secara permanen dari basis data
     *
     * @param int $id ID Buku
     */
    public function forceDelete($id) {
        $id = (int)$id;
        
        try {
            $this->bookRepo->forceDelete($id);
            $_SESSION['flash_success'] = "Buku telah dihapus secara permanen!";
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = "Gagal menghapus buku secara permanen.";
        }
        $this->redirect('/trash');
    }

    // ====================================================================
    // NOTION-STYLE SEARCH API (AJAX GET => JSON)
    // ====================================================================

    /**
     * Melakukan kueri pencarian teks dan mengembalikan respon JSON
     */
    public function search() {
        $query = $_GET['q'] ?? '';
        
        // Set proper JSON headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $books = $this->bookRepo->search($query);
            $response = [];
            foreach ($books as $book) {
                $response[] = [
                    'id'           => $book->id,
                    'title'        => $book->title,
                    'author'       => $book->author,
                    'cover_url'    => $book->coverUrl ?? '',
                    'gradient'     => $book->getCoverGradient(),
                    'status_label' => $book->getStatusLabel()
                ];
            }
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}
