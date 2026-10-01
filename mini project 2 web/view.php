<?php
/**
 * Halaman Detail Spesifikasi Produk Sepatu
 * File: view.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID produk sepatu tidak valid.');
    header('Location: index.php');
    exit;
}

// Syarat Query: SELECT by ID -> $pdo->prepare(...)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Produk sepatu dengan ID #' . $id . ' tidak ditemukan!');
    header('Location: index.php');
    exit;
}

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';

// Tentukan gambar
$img_src = 'assets/images/shoes/default_placeholder.svg';
if (!empty($product['image']) && file_exists(__DIR__ . '/assets/images/shoes/' . $product['image'])) {
    $img_src = 'assets/images/shoes/' . $product['image'];
}
?>

<div style="margin-top: 30px;">
    <a href="index.php" class="btn btn-secondary btn-sm" style="margin-bottom: 20px;">
        <i class="ph-bold ph-arrow-left"></i> Kembali ke Katalog
    </a>

    <div class="detail-card">
        <!-- Galeri Gambar -->
        <div class="detail-gallery">
            <img src="<?= e($img_src) ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, "UTF-8") ?>">
        </div>

        <!-- Informasi Produk -->
        <div class="detail-info">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="detail-brand"><?= e($product['brand']) ?></span>
                <?= render_stock_badge((int)$product['stock']) ?>
            </div>

            <!-- Syarat Output: htmlspecialchars($product["name"], ENT_QUOTES, "UTF-8") -->
            <h2 class="detail-title">
                <?= htmlspecialchars($product['name'], ENT_QUOTES, "UTF-8") ?>
            </h2>

            <div class="detail-price">
                <?= format_rupiah($product['price']) ?>
            </div>

            <p style="color: var(--text-muted); line-height: 1.7; margin-bottom: 20px;">
                <?= nl2br(e($product['description'] ?: 'Tidak ada deskripsi rinci untuk produk sepatu ini.')) ?>
            </p>

            <!-- Tabel Spesifikasi Teknis -->
            <table class="specs-table">
                <tbody>
                    <tr>
                        <td>ID Produk</td>
                        <td>#<?= (int)$product['id'] ?></td>
                    </tr>
                    <tr>
                        <td>Kategori</td>
                        <td><span class="badge badge-category"><?= e($product['category']) ?></span></td>
                    </tr>
                    <tr>
                        <td>Merk / Brand</td>
                        <td><?= e($product['brand']) ?></td>
                    </tr>
                    <tr>
                        <td>Ukuran Tersedia</td>
                        <td>EU <?= e($product['size']) ?></td>
                    </tr>
                    <tr>
                        <td>Sisa Stok</td>
                        <td><strong><?= number_format($product['stock']) ?></strong> pasang</td>
                    </tr>
                    <tr>
                        <td>Ditambahkan Pada</td>
                        <td><?= date('d M Y, H:i', strtotime($product['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td>Terakhir Diperbarui</td>
                        <td><?= date('d M Y, H:i', strtotime($product['updated_at'])) ?></td>
                    </tr>
                </tbody>
            </table>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; margin-top: auto; padding-top: 20px; border-top: 1px solid var(--border-subtle);">
                <a href="edit.php?id=<?= (int)$product['id'] ?>" class="btn btn-primary" style="flex: 1;">
                    <i class="ph-bold ph-pencil-simple"></i> Edit Data Sepatu
                </a>
                <button type="button" 
                        class="btn btn-danger btn-delete-trigger" 
                        data-id="<?= (int)$product['id'] ?>" 
                        data-name="<?= e($product['name']) ?>" 
                        title="Hapus Produk">
                    <i class="ph-bold ph-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
