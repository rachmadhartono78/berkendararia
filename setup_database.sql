-- ============================================
-- BERKENDARA RIA - Database Setup
-- ============================================
-- Jalankan script ini di phpMyAdmin atau MySQL CLI
-- XAMPP: http://localhost/phpmyadmin
-- ============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS `berkendararia`
    DEFAULT CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `berkendararia`;

-- Buat tabel subscribers
CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL COMMENT 'Nama lengkap subscriber',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email subscriber (unique)',
    `no_hp` VARCHAR(20) NOT NULL COMMENT 'Nomor HP/WhatsApp',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal daftar',
    INDEX `idx_email` (`email`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CATATAN:
-- Database dan tabel akan otomatis dibuat oleh
-- db_config.php saat pertama kali form disubmit.
-- File SQL ini hanya sebagai backup/referensi.
-- ============================================
