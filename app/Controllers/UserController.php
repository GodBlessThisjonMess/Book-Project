<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepositoryInterface;

class UserController extends Controller {
    private $userRepo;

    public function __construct(UserRepositoryInterface $userRepo) {
        $this->userRepo = $userRepo;
    }

    /**
     * Halaman Pengaturan Akun (Username, Foto Profil, Deskripsi)
     */
    public function settings() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->redirect('/login');
        }

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = "User tidak ditemukan.";
            $this->redirect('/');
        }

        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $password = $_POST['password'] ?? '';
            $avatarUrl = $user->avatarUrl; // Default ke avatar lama

            if (empty($username)) {
                $error = "Username tidak boleh kosong.";
            } else {
                // Verifikasi keunikan username baru jika diubah
                if ($username !== $user->username) {
                    $existingUser = $this->userRepo->findByUsername($username);
                    if ($existingUser) {
                        $error = "Username '$username' sudah digunakan oleh orang lain.";
                    }
                }

                if (!$error) {
                    // Coba tangani pengunggahan berkas avatar jika ada
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['avatar']['tmp_name'];
                        $fileName = $_FILES['avatar']['name'];
                        $fileSize = $_FILES['avatar']['size'];
                        $fileType = $_FILES['avatar']['type'];
                        
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                        
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (in_array($fileExtension, $allowedExtensions)) {
                            // Batasi ukuran file (misal maksimal 2MB)
                            if ($fileSize < 2 * 1024 * 1024) {
                                $uploadDir = __DIR__ . '/../../uploads/avatars/';
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0777, true);
                                }
                                
                                $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                                $destPath = $uploadDir . $newFileName;
                                
                                if (move_uploaded_file($fileTmpPath, $destPath)) {
                                    $avatarUrl = 'uploads/avatars/' . $newFileName;
                                    
                                    // Hapus berkas avatar lama jika ada berkas lokal sebelumnya
                                    if ($user->avatarUrl && strpos($user->avatarUrl, 'uploads/avatars/') === 0) {
                                        $oldFilePath = __DIR__ . '/../../' . $user->avatarUrl;
                                        if (file_exists($oldFilePath)) {
                                            @unlink($oldFilePath);
                                        }
                                    }
                                } else {
                                    $error = "Gagal memindahkan file yang diunggah ke folder penyimpanan.";
                                }
                            } else {
                                $error = "Ukuran file terlalu besar. Maksimal adalah 2MB.";
                            }
                        } else {
                            $error = "Ekstensi file tidak didukung. Format yang diizinkan: JPG, JPEG, PNG, GIF, WEBP.";
                        }
                    }

                    if (!$error) {
                        // Jalankan kueri pembaruan profil
                        $updated = $this->userRepo->update($userId, [
                            'username'   => $username,
                            'bio'        => $bio,
                            'avatar_url' => $avatarUrl,
                            'password'   => $password // Halaman settings mengizinkan ganti password opsional
                        ]);

                        if ($updated) {
                            $_SESSION['username'] = $username; // Perbarui data sesi
                            $_SESSION['flash_success'] = "Pengaturan akun berhasil diperbarui!";
                            $this->redirect('/settings');
                        } else {
                            $error = "Terjadi kesalahan sistem saat memperbarui profil.";
                        }
                    }
                }
            }
        }

        $this->render('auth/settings', [
            'user'    => $user,
            'error'   => $error,
            'success' => $success
        ]);
    }
}
