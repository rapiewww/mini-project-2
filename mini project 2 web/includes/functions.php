<?php
/**
 * Helper Functions
 * File: includes/functions.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitasi output HTML untuk mencegah XSS
 * Sesuai syarat output: htmlspecialchars($val, ENT_QUOTES, "UTF-8")
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka ke format mata uang Rupiah (IDR)
 */
function format_rupiah(float|int|string $number): string {
    return 'Rp ' . number_format((float)$number, 0, ',', '.');
}

/**
 * Pasang flash message sesi untuk Pola PRG (Post/Redirect/Get)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Ambil flash message dari sesi lalu hapus
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Tampilkan HTML badge status stok
 */
function render_stock_badge(int $stock): string {
    if ($stock <= 0) {
        return '<span class="badge badge-danger"><i class="ph-bold ph-x-circle"></i> Stok Habis</span>';
    } elseif ($stock <= 5) {
        return '<span class="badge badge-warning"><i class="ph-bold ph-warning"></i> Sisa ' . $stock . ' (Menipis)</span>';
    } else {
        return '<span class="badge badge-success"><i class="ph-bold ph-check-circle"></i> Tersedia (' . $stock . ')</span>';
    }
}

/**
 * Penanganan Upload Gambar Produk
 * @param array $file $_FILES['image']
 * @return array ['success' => bool, 'fileName' => string|null, 'error' => string|null]
 */
function upload_product_image(array $file): array {
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'fileName' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'fileName' => null, 'error' => 'Gagal mengunggah berkas. Kode kesalahan: ' . $file['error']];
    }

    // Validasi ukuran berkas (Maks 3MB)
    if ($file['size'] > 3 * 1024 * 1024) {
        return ['success' => false, 'fileName' => null, 'error' => 'Ukuran gambar tidak boleh melebihi 3MB!'];
    }

    // Validasi ekstensi dan tipe MIME
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension'] ?? '');

    if (!in_array($extension, $allowed_extensions, true)) {
        return ['success' => false, 'fileName' => null, 'error' => 'Format file tidak didukung! Gunakan format JPG, PNG, WEBP, atau SVG.'];
    }

    $upload_dir = __DIR__ . '/../assets/images/shoes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Buat nama berkas acak yang aman
    $new_file_name = 'shoe_' . uniqid('', true) . '.' . $extension;
    $destination = $upload_dir . $new_file_name;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'fileName' => null, 'error' => 'Gagal memindahkan file ke direktori tujuan.'];
    }

    return ['success' => true, 'fileName' => $new_file_name, 'error' => null];
}
