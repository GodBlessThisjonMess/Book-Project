<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Mencari user berdasarkan ID
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    /**
     * Mencari user berdasarkan Username (untuk pengecekan login/keunikan)
     */
    public function findByUsername(string $username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ? new User($row) : null;
    }

    /**
     * Mendaftarkan user baru (Sign In)
     */
    public function create(array $data) {
        // Enkripsi password menggunakan bcrypt aman
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (username, password, avatar_url, bio) 
            VALUES (:username, :password, :avatar_url, :bio)
        ");
        
        return $stmt->execute([
            'username'   => htmlspecialchars(strip_tags($data['username'])),
            'password'   => $hashedPassword,
            'avatar_url' => $data['avatar_url'] ?? null,
            'bio'        => isset($data['bio']) ? htmlspecialchars($data['bio']) : ''
        ]);
    }

    /**
     * Memperbarui profil user (settings)
     */
    public function update(int $id, array $data) {
        // Cek jika password ikut diperbarui
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                UPDATE users 
                SET username = :username, password = :password, avatar_url = :avatar_url, bio = :bio 
                WHERE id = :id
            ");
            return $stmt->execute([
                'id'         => $id,
                'username'   => htmlspecialchars(strip_tags($data['username'])),
                'password'   => $hashedPassword,
                'avatar_url' => $data['avatar_url'] ?? null,
                'bio'        => htmlspecialchars($data['bio'] ?? '')
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET username = :username, avatar_url = :avatar_url, bio = :bio 
                WHERE id = :id
            ");
            return $stmt->execute([
                'id'         => $id,
                'username'   => htmlspecialchars(strip_tags($data['username'])),
                'avatar_url' => $data['avatar_url'] ?? null,
                'bio'        => htmlspecialchars($data['bio'] ?? '')
            ]);
        }
    }
}
