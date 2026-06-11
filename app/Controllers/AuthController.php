<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepositoryInterface;

class AuthController extends Controller {
    private $userRepo;

    public function __construct(UserRepositoryInterface $userRepo) {
        $this->userRepo = $userRepo;
    }

    /**
     * Menampilkan form login dan memproses login POST
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!empty($username) && !empty($password)) {
                $user = $this->userRepo->findByUsername($username);
                
                if ($user && password_verify($password, $user->password)) {
                    // Login sukses, simpan di session
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['username'] = $user->username;                  
                    $_SESSION['flash_success'] = "Selamat datang kembali, " . htmlspecialchars($user->username) . "!";
                    $this->redirect('/');
                } else {
                    $error = "Username atau password salah.";
                }
            } else {
                $error = "Semua kolom wajib diisi.";
            }
        }

        $this->renderAuth('auth/login', [
            'error' => $error ?? null
        ]);
    }

    /**
     * Menampilkan form pendaftaran (Sign In) dan memproses registrasi POST
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $bio = $_POST['bio'] ?? 'Single Account';

            if (!empty($username) && !empty($password) && !empty($confirmPassword)) {
                if ($password !== $confirmPassword) {
                    $error = "Konfirmasi kata sandi tidak cocok.";
                } else {
                    // Cek keunikan username
                    $existingUser = $this->userRepo->findByUsername($username);
                    if ($existingUser) {
                        $error = "Username sudah terdaftar.";
                    } else {
                        // Daftarkan user baru
                        $success = $this->userRepo->create([
                            'username' => $username,
                            'password' => $password,
                            'bio'      => $bio
                        ]);

                        if ($success) {
                            // Langsung login setelah register sukses
                            $user = $this->userRepo->findByUsername($username);
                            $_SESSION['user_id'] = $user->id;
                            $_SESSION['username'] = $user->username;
                            $_SESSION['flash_success'] = "Pendaftaran berhasil! Selamat datang di Book Journal.";
                            $this->redirect('/');
                        } else {
                            $error = "Terjadi kesalahan saat mendaftar.";
                        }
                    }
                }
            } else {
                $error = "Semua kolom wajib diisi.";
            }
        }

        $this->renderAuth('auth/register', [
            'error' => $error ?? null
        ]);
    }

    /**
     * Memproses logout user
     */
    public function logout() {
        // Hapus semua data session
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        // Mulai session baru hanya untuk menampung pesan flash logout
        session_start();
        $_SESSION['flash_success'] = "Anda berhasil keluar sesi.";
        $this->redirect('/login');
    }

    /**
     * Custom renderer untuk view auth agar tidak menyisipkan header & footer utama dashboard
     */
    private function renderAuth($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("File View tidak ditemukan: " . htmlspecialchars($viewFile));
        }
    }
}
