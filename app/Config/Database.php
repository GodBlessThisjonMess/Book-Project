<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $connection = null;

    /**
     * Mengambil instansi koneksi tunggal PDO (Singleton)
     *
     * @return PDO
     */
    public static function getConnection() {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
            $port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
            $db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'book_tracker_db');
            $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
            $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($_ENV['DB_PASS'] ?? '');
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
                
                // Self-healing migration for schema changes
                self::runMigrations(self::$connection);
            } catch (PDOException $e) {
                die("Koneksi database gagal: " . $e->getMessage() . ". Pastikan server MySQL Anda berjalan dan skema database telah diimpor.");
            }
        }
        return self::$connection;
    }

    /**
     * Jalankan migrasi mandiri jika kolom baru belum ada di tabel `books`
     */
    private static function runMigrations(PDO $db) {
        try {
            // 1. Buat tabel users jika belum ada
            $db->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `avatar_url` VARCHAR(2048) NULL,
                `bio` TEXT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");

            // Migrasi tabel books
            $query = $db->query("DESCRIBE `books`");
            $columns = $query->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('cover_url', $columns)) {
                $db->exec("ALTER TABLE `books` ADD COLUMN `cover_url` VARCHAR(2048) NULL AFTER `status`");
            }
            
            if (!in_array('is_deleted', $columns)) {
                $db->exec("ALTER TABLE `books` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cover_url`");
                $db->exec("ALTER TABLE `books` ADD INDEX `idx_books_deleted` (`is_deleted`)");
            }

            if (!in_array('total_pages', $columns)) {
                $db->exec("ALTER TABLE `books` ADD COLUMN `total_pages` INT NOT NULL DEFAULT 0 AFTER `cover_url`");
            }

            if (!in_array('user_id', $columns)) {
                $db->exec("ALTER TABLE `books` ADD COLUMN `user_id` INT NULL AFTER `id`");
                try {
                    $db->exec("ALTER TABLE `books` ADD CONSTRAINT `fk_books_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
                } catch (\PDOException $ex) {
                    // Abaikan jika foreign key tidak bisa dibuat (misal mesin bukan InnoDB)
                }
            }

            // Migrasi tabel journals
            $queryJ = $db->query("DESCRIBE `journals`");
            $columnsJ = $queryJ->fetchAll(PDO::FETCH_COLUMN);

            if (!in_array('read_to_page', $columnsJ)) {
                $db->exec("ALTER TABLE `journals` ADD COLUMN `read_to_page` INT NOT NULL DEFAULT 0 AFTER `notes`");
            }
        } catch (PDOException $e) {
            // Log error or ignore if tables are not fully set up yet
        }
    }
}
