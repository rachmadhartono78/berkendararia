<?php
/**
 * Berkendara Ria - Database Configuration
 * 
 * Konfigurasi koneksi MySQL via PDO
 * Default: XAMPP (localhost, root, tanpa password)
 */

// --- Database Settings ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'berkendararia');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default: kosong
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO connection instance
 * Auto-creates database and table if they don't exist
 * 
 * @return PDO
 */
function getDBConnection()
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // First, connect without database to create it if needed
        $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` 
                     DEFAULT CHARACTER SET utf8mb4 
                     COLLATE utf8mb4_unicode_ci");

        // Select the database
        $pdo->exec("USE `" . DB_NAME . "`");

        // Create subscribers table if not exists
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `subscribers` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `nama` VARCHAR(100) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `no_hp` VARCHAR(20) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_email` (`email`),
                INDEX `idx_created` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        return $pdo;

    }
    catch (PDOException $e) {
        // Return null and let the caller handle the error
        error_log('Database connection error: ' . $e->getMessage());
        return null;
    }
}
