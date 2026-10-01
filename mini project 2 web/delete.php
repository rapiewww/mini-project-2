<?php
/**
 * Action Handler: Hapus Produk Sepatu
 * Syarat: Delete: POST + CSRF & DELETE -> $pdo->prepare(...)
 * File: delete.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

// Pastikan hanya melayani request dengan metode POST (Syarat: Delete: POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Metode tidak diizinkan. Penghapusan data harus melalui metode POST.");
}

// 1. Validasi Token CSRF (Syarat: CSRF Protection)
$csrf_token = $_POST['csrf_token'] ?? null;
if (!validate_csrf_token($csrf_token)) {
    http_response_code(403);
    die("Akses ditolak: Verifikasi token CSRF tidak valid atau sesi telah kadaluwarsa.");
}

// 2. Ambil ID produk yang akan dihapus
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID produk yang ingin dihapus tidak valid.');
    header('Location: index.php');
    exit;
}

// 3. Ambil data produk terlebih dahulu (untuk nama & jika ada file gambar fisik yang perlu dibersihkan)
$select_stmt = $pdo->prepare("SELECT name, image FROM products WHERE id = :id LIMIT 1");
$select_stmt->execute([':id' => $id]);
$product = $select_stmt->fetch();

if (!$product) {
    set_flash('warning', 'Produk tidak ditemukan atau mungkin sudah dihapus sebelumnya.');
    header('Location: index.php');
    exit;
}

// 4. Eksekusi Query DELETE menggunakan PDO Prepared Statement
// Syarat Query: DELETE -> $pdo->prepare(...)
$delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$delete_stmt->execute([':id' => $id]);

// Hapus berkas gambar khusus jika diunggah oleh user (bukan gambar sample default)
if (!empty($product['image']) && str_starts_with($product['image'], 'shoe_')) {
    $img_path = __DIR__ . '/assets/images/shoes/' . $product['image'];
    if (file_exists($img_path)) {
        @unlink($img_path);
    }
}

// 5. Pola PRG (Post/Redirect/Get) dengan Flash Message
set_flash('success', 'Produk "' . $product['name'] . '" telah berhasil dihapus dari sistem.');
header('Location: index.php');
exit;
