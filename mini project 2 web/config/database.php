<?php
/**
 * Konfigurasi Database PDO (Kompatibel dengan MAMP & XAMPP)
 * File: config/database.php
 */

$db_host = '127.0.0.1';
$db_name = 'kicksvault_db';

// Daftar konfigurasi yang umum dipakai di MAMP dan XAMPP
$connection_configs = [
    // 1. MAMP default (port 8889, user: root, pass: root)
    ['port' => 8889, 'user' => 'root', 'pass' => 'root'],
    // 2. MAMP alternatif / XAMPP (port 3306, user: root, pass: root)
    ['port' => 3306, 'user' => 'root', 'pass' => 'root'],
    // 3. XAMPP default (port 3306, user: root, pass: kosong)
    ['port' => 3306, 'user' => 'root', 'pass' => ''],
    // 4. Default socket / host standar
    ['port' => 8889, 'user' => 'root', 'pass' => ''],
];

$pdo = null;
$connection_error = null;

foreach ($connection_configs as $cfg) {
    try {
        $dsn = "mysql:host={$db_host};port={$cfg['port']};dbname={$db_name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
        break; // Berhasil terhubung, keluar dari perulangan
    } catch (PDOException $e) {
        $connection_error = $e->getMessage();
        continue;
    }
}

// Jika koneksi belum berhasil, coba connect tanpa dbname untuk mengecek apakah database belum dibuat
if (!$pdo) {
    foreach ($connection_configs as $cfg) {
        try {
            $dsn_no_db = "mysql:host={$db_host};port={$cfg['port']};charset=utf8mb4";
            $temp_pdo = new PDO($dsn_no_db, $cfg['user'], $cfg['pass']);
            
            // Coba buat database otomatis jika belum diimpor
            $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Coba konek kembali ke database
            $dsn = "mysql:host={$db_host};port={$cfg['port']};dbname={$db_name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
            
            // Buat tabel jika belum ada
            $pdo->exec("CREATE TABLE IF NOT EXISTS `products` (
              `id` INT(11) NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(150) NOT NULL,
              `category` ENUM('Sneakers', 'Running', 'Basketball', 'Casual', 'Boots') NOT NULL DEFAULT 'Sneakers',
              `brand` VARCHAR(80) NOT NULL DEFAULT 'Other',
              `size` VARCHAR(50) NOT NULL DEFAULT '40-44',
              `price` DECIMAL(12,2) NOT NULL,
              `stock` INT(11) NOT NULL DEFAULT 0,
              `image` VARCHAR(255) DEFAULT NULL,
              `description` TEXT DEFAULT NULL,
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `unique_product_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            break;
        } catch (Exception $ex) {
            // Lanjutkan percobaan
        }
    }
}

if (!$pdo) {
    die("
    <div style='font-family: system-ui, sans-serif; max-width: 650px; margin: 50px auto; padding: 25px; border-radius: 12px; background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239;'>
        <h2 style='margin-top: 0;'>⚠️ Gagal Terhubung ke Database MySQL</h2>
        <p>Aplikasi tidak dapat terhubung ke MySQL di MAMP/XAMPP. Pastikan server MySQL di MAMP sudah aktif.</p>
        <p><strong>Pesan Kesalahan:</strong> " . htmlspecialchars($connection_error ?? 'Database not found', ENT_QUOTES, 'UTF-8') . "</p>
        <hr style='border: none; border-top: 1px solid #fecdd3; margin: 15px 0;'>
        <p><strong>Langkah Penyelesaian di MAMP:</strong></p>
        <ol style='padding-left: 20px; line-height: 1.6;'>
            <li>Buka aplikasi MAMP dan klik <strong>Start Servers</strong>.</li>
            <li>Buka <strong>phpMyAdmin</strong> melalui MAMP.</li>
            <li>Buat database baru bernama <code>kicksvault_db</code> atau impor file <code>database.sql</code> yang tersedia di root folder proyek ini.</li>
            <li>Muat ulang (refresh) halaman ini.</li>
        </ol>
    </div>
    ");
}

// Auto-seed: Jika tabel products kosong, isi data dummy sepatu otomatis
try {
    $product_count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($product_count === 0) {
        require_once __DIR__ . '/../seed.php';
        seed_default_products($pdo);
    }
} catch (Exception $e) {
    // Tabel mungkin belum terbentuk
}
