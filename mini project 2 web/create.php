<?php
/**
 * Halaman Tambah Produk Sepatu Baru
 * File: create.php
 */

$page_title = 'Tambah Sepatu Baru';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$form_data = [
    'name' => '',
    'category' => 'Sneakers',
    'brand' => '',
    'size' => '40, 41, 42, 43',
    'price' => '',
    'stock' => '0',
    'description' => ''
];

// Proses Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validasi CSRF Token
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        die("Permintaan tidak valid (CSRF token verification failed). Silakan muat ulang halaman.");
    }

    // 2. Ambil dan sanitasi input form dasar
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Sneakers');
    $brand = trim($_POST['brand'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $price = str_replace(',', '.', trim($_POST['price'] ?? ''));
    $stock = trim($_POST['stock'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $form_data = [
        'name' => $name,
        'category' => $category,
        'brand' => $brand,
        'size' => $size,
        'price' => $price,
        'stock' => $stock,
        'description' => $description
    ];

    // 3. Validasi aturan (Sesuai Syarat Tugas):
    // - nama >= 3 karakter
    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama produk minimal 3 karakter!';
    } else {
        // - nama unik (cek ketersediaan di database)
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name");
        $check_stmt->execute([':name' => $name]);
        if ($check_stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama sepatu "' . e($name) . '" sudah terdaftar! Gunakan nama yang berbeda.';
        }
    }

    // - harga > 0
    if ($price === '' || !is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Harga harus berupa angka dan lebih besar dari 0 (harga > 0)!';
    }

    // - stok >= 0
    if ($stock === '' || !is_numeric($stock) || (int)$stock < 0) {
        $errors['stock'] = 'Stok harus berupa bilangan bulat dan tidak boleh negatif (stok >= 0)!';
    }

    // Validasi brand
    if (empty($brand)) {
        $form_data['brand'] = 'Nike'; // default fallback
    }

    // 4. Penanganan Berkas Gambar
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = upload_product_image($_FILES['image']);
        if (!$upload_result['success']) {
            $errors['image'] = $upload_result['error'];
        } else {
            $image_name = $upload_result['fileName'];
        }
    }

    // 5. Jika tidak ada error validasi, masukkan ke database
    if (empty($errors)) {
        // Syarat Query: INSERT -> $pdo->prepare(...)
        $insert_sql = "INSERT INTO products (name, category, brand, size, price, stock, image, description) 
                       VALUES (:name, :category, :brand, :size, :price, :stock, :image, :description)";
        
        $insert_stmt = $pdo->prepare($insert_sql);
        $insert_stmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':brand'       => $brand ?: 'Universal',
            ':size'        => $size ?: '40-44',
            ':price'       => (float)$price,
            ':stock'       => (int)$stock,
            ':image'       => $image_name,
            ':description' => $description
        ]);

        // PRG Pattern: Post/Redirect/Get untuk menghindari data ganda saat refresh!
        set_flash('success', 'Produk sepatu "' . $name . '" berhasil ditambahkan ke katalog!');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <a href="index.php" class="btn btn-secondary btn-sm" style="margin-bottom: 12px;">
                <i class="ph-bold ph-arrow-left"></i> Kembali ke Katalog
            </a>
            <h2>Tambah Produk Sepatu Baru</h2>
            <p>Masukkan informasi detail produk sneaker di bawah ini. Semua field bertanda bintang (*) wajib diisi.</p>
        </div>

        <form method="POST" action="create.php" enctype="multipart/form-data">
            <!-- CSRF Token Hidden Field -->
            <?= csrf_field() ?>

            <div class="form-grid">
                <!-- Nama Produk (Validasi: >= 3 & Unik) -->
                <div class="form-group full-width">
                    <label class="form-label" for="name">
                        <span>Nama Model Sepatu <span class="required">*</span></span>
                        <span class="hint">Minimal 3 karakter & harus unik</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
                           placeholder="Contoh: Nike Air Jordan 1 Retro High" 
                           value="<?= e($form_data['name']) ?>" 
                           required>
                    <?php if (isset($errors['name'])): ?>
                        <span class="form-error"><i class="ph-bold ph-warning-circle"></i> <?= e($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Kategori -->
                <div class="form-group">
                    <label class="form-label" for="category">
                        <span>Kategori <span class="required">*</span></span>
                    </label>
                    <select id="category" name="category" class="form-select" style="width: 100%;">
                        <?php 
                        $cats = ['Sneakers', 'Running', 'Basketball', 'Casual', 'Boots'];
                        foreach ($cats as $c): 
                        ?>
                            <option value="<?= $c ?>" <?= $form_data['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Merk / Brand -->
                <div class="form-group">
                    <label class="form-label" for="brand">
                        <span>Merk / Brand</span>
                    </label>
                    <input type="text" 
                           id="brand" 
                           name="brand" 
                           class="form-control" 
                           placeholder="Contoh: Nike, Adidas, New Balance" 
                           value="<?= e($form_data['brand']) ?>">
                </div>

                <!-- Harga (Validasi: > 0) -->
                <div class="form-group">
                    <label class="form-label" for="price">
                        <span>Harga (IDR) <span class="required">*</span></span>
                        <span class="hint">Harus &gt; 0</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           class="form-control" 
                           placeholder="Contoh: 1850000" 
                           step="1000" 
                           min="1" 
                           value="<?= e($form_data['price']) ?>" 
                           required>
                    <?php if (isset($errors['price'])): ?>
                        <span class="form-error"><i class="ph-bold ph-warning-circle"></i> <?= e($errors['price']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Stok (Validasi: >= 0) -->
                <div class="form-group">
                    <label class="form-label" for="stock">
                        <span>Jumlah Stok <span class="required">*</span></span>
                        <span class="hint">Harus &ge; 0</span>
                    </label>
                    <input type="number" 
                           id="stock" 
                           name="stock" 
                           class="form-control" 
                           placeholder="Contoh: 10" 
                           min="0" 
                           value="<?= e($form_data['stock']) ?>" 
                           required>
                    <?php if (isset($errors['stock'])): ?>
                        <span class="form-error"><i class="ph-bold ph-warning-circle"></i> <?= e($errors['stock']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Ukuran / Size -->
                <div class="form-group full-width">
                    <label class="form-label" for="size">
                        <span>Pilihan Ukuran (EU)</span>
                        <span class="hint">Contoh: 39, 40, 41, 42, 43</span>
                    </label>
                    <input type="text" 
                           id="size" 
                           name="size" 
                           class="form-control" 
                           placeholder="Contoh: 40, 41, 42, 43, 44" 
                           value="<?= e($form_data['size']) ?>">
                </div>

                <!-- Upload Gambar Foto Sepatu -->
                <div class="form-group full-width">
                    <label class="form-label" for="imageInput">
                        <span>Unggah Foto Sepatu</span>
                        <span class="hint">Format JPG, PNG, WEBP (Maks 3MB)</span>
                    </label>
                    <input type="file" 
                           id="imageInput" 
                           name="image" 
                           class="form-control" 
                           accept="image/png, image/jpeg, image/webp, image/svg+xml">
                    <?php if (isset($errors['image'])): ?>
                        <span class="form-error"><i class="ph-bold ph-warning-circle"></i> <?= e($errors['image']) ?></span>
                    <?php endif; ?>

                    <div class="image-preview-container">
                        <div class="image-preview-box">
                            <img id="imagePreview" src="assets/images/shoes/default_placeholder.svg" alt="Preview Sepatu">
                        </div>
                        <span style="font-size: 0.85rem; color: var(--text-dim);">
                            Pratinjau gambar produk yang akan tampil pada kartu katalog.
                        </span>
                    </div>
                </div>

                <!-- Deskripsi Produk -->
                <div class="form-group full-width">
                    <label class="form-label" for="description">
                        <span>Deskripsi & Spesifikasi Produk</span>
                    </label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control" 
                              placeholder="Tuliskan material bahan, bantalan sol, kenyamanan, atau keunggulan sepatu ini..."><?= e($form_data['description']) ?></textarea>
                </div>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-bold ph-floppy-disk"></i> Simpan Sepatu ke Database
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
