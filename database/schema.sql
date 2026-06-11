-- ====================================================================
-- SKEMA BASIS DATA: PERSONAL BOOK READING TRACKER
-- DBMS: MySQL / MariaDB
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `book_tracker_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `book_tracker_db`;

-- 1. TABEL BOOKS (Menyimpan informasi buku)
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('Not Read', 'On Going', 'Done', 'Unfinished') NOT NULL DEFAULT 'Not Read',
    `cover_url` VARCHAR(2048) NULL,
    `total_pages` INT NOT NULL DEFAULT 0,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_books_status` (`status`),
    INDEX `idx_books_deleted` (`is_deleted`)
) ENGINE=InnoDB;

-- 2. TABEL READING_CALENDAR (Menyimpan jadwal harian membaca buku)
CREATE TABLE IF NOT EXISTS `reading_calendar` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL,
    `reading_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_calendar_book`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY `uq_book_date` (`book_id`, `reading_date`),
    INDEX `idx_calendar_date` (`reading_date`)
) ENGINE=InnoDB;

-- 3. TABEL REVIEWS (Ulasan dan rating buku privat - Relasi 1:1)
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL UNIQUE,
    `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `review_text` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_book`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. TABEL JOURNALS (Catatan pemikiran harian per buku - Relasi 1:N)
CREATE TABLE IF NOT EXISTS `journals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT NOT NULL,
    `notes` TEXT NOT NULL,
    `read_to_page` INT NOT NULL DEFAULT 0,
    `logged_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_journals_book`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_journals_book` (`book_id`),
    INDEX `idx_journals_logged_at` (`logged_at`)
) ENGINE=InnoDB;
