<?php
/**
 * Halaman Edit Produk Sepatu
 * File: edit.php
 */

$page_title = 'Edit Data Sepatu';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

// Ambil ID produk dari parameter query GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID produk sepatu tidak valid atau tidak disertakan.');
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

$errors = [];
$form_data = $product;

// Tangani Pengiriman Form Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validasi CSRF Token
    if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
        die("Permintaan tidak valid (CSRF token verification failed).");
    }

    // 2. Ambil input
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Sneakers');
    $brand = trim($_POST['brand'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $price = str_replace(',', '.', trim($_POST['price'] ?? ''));
    $stock = trim($_POST['stock'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $form_data['name'] = $name;
    $form_data['category'] = $category;
    $form_data['brand'] = $brand;
    $form_data['size'] = $size;
    $form_data['price'] = $price;
    $form_data['stock'] = $stock;
    $form_data['description'] = $description;

    // 3. Validasi aturan ketat (Sesuai Syarat Tugas):
    // - nama >= 3
    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama produk minimal 3 karakter!';
    } else {
        // - nama unik (abaikan produk itu sendiri)
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = :name AND id != :id");
        $check_stmt->execute([':name' => $name, ':id' => $id]);
        if ($check_stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama sepatu "' . e($name) . '" sudah digunakan oleh produk lain!';
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

    // 4. Penanganan unggahan gambar baru (jika ada)
    $image_name = $product['image']; // Default pakai gambar lama
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = upload_product_image($_FILES['image']);
        if (!$upload_result['success']) {
            $errors['image'] = $upload_result['error'];
        } else {
            $image_name = $upload_result['fileName'];
        }
    }

    // 5. Eksekusi UPDATE jika validasi lolos
    if (empty($errors)) {
        // Syarat Query: UPDATE -> $pdo->prepare(...)
        $update_sql = "UPDATE products 
                       SET name = :name, 
                           category = :category, 
                           brand = :brand, 
                           size = :size, 
                           price = :price, 
                           stock = :stock, 
                           image = :image, 
                           description = :description 
                       WHERE id = :id";
        
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute([
            ':name'        => $name,
            ':category'    => $category,
            ':brand'       => $brand ?: 'Universal',
            ':size'        => $size ?: '40-44',
            ':price'       => (float)$price,
            ':stock'       => (int)$stock,
            ':image'       => $image_name,
            ':description' => $description,
            ':id'          => $id
        ]);

        // PRG Pattern: Flash message & Redirect
        set_flash('success', 'Perubahan data sepatu "' . $name . '" berhasil disimpan!');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';

// Tentukan pratinjau foto
$current_img_src = 'assets/images/shoes/default_placeholder.svg';
if (!empty($form_data['image']) && file_exists(__DIR__ . '/assets/images/shoes/' . $form_data['image'])) {
    $current_img_src = 'assets/images/shoes/' . $form_data['image'];
}
?>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <a href="index.php" class="btn btn-secondary btn-sm" style="margin-bottom: 12px;">
                <i class="ph-bold ph-arrow-left"></i> Kembali ke Katalog
            </a>
            <h2>Edit Produk: <?= htmlspecialchars($product['name'], ENT_QUOTES, "UTF-8") ?></h2>
            <p>Perbarui detail spesifikasi, harga, maupun jumlah stok sepatu di bawah ini.</p>
        </div>

        <form method="POST" action="edit.php?id=<?= (int)$id ?>" enctype="multipart/form-data">
            <!-- CSRF Token Hidden Input -->
            <?= csrf_field() ?>

            <div class="form-grid">
                <!-- Nama Produk (Validasi: >= 3 & Unik) -->
                <div class="form-group full-width">
                    <label class="form-label" for="name">
                        <span>Nama Model Sepatu <span class="required">*</span></span>
                        <span class="hint">Minimal 3 karakter & unik</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control" 
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
                    </label>
                    <input type="text" 
                           id="size" 
                           name="size" 
                           class="form-control" 
                           value="<?= e($form_data['size']) ?>">
                </div>

                <!-- Foto Sepatu -->
                <div class="form-group full-width">
                    <label class="form-label" for="imageInput">
                        <span>Ganti Foto Sepatu (Opsional)</span>
                        <span class="hint">Biarkan kosong jika tidak ingin mengubah foto</span>
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
                            <img id="imagePreview" src="<?= e($current_img_src) ?>" alt="Preview Sepatu">
                        </div>
                        <span style="font-size: 0.85rem; color: var(--text-dim);">
                            Foto saat ini yang aktif pada produk.
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
                              class="form-control"><?= e($form_data['description']) ?></textarea>
                </div>
            </div>

            <!-- Tombol Aksi Simpan -->
            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ph-bold ph-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
