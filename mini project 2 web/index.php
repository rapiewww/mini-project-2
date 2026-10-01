<?php
/**
 * Halaman Utama - Katalog Produk Sepatu & Pencarian
 * File: index.php
 */

$page_title = 'Katalog Sepatu & Manajemen Produk';
require_once __DIR__ . '/includes/header.php';

// Ambil parameter filter dan pencarian
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$stock_status = trim($_GET['stock_status'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');

// Bangun query SQL dinamis dengan PDO Prepared Statements
$where_clauses = [];
$params = [];

if ($search !== '') {
    $where_clauses[] = "(name LIKE :search_name OR brand LIKE :search_brand OR description LIKE :search_desc)";
    $params[':search_name'] = "%{$search}%";
    $params[':search_brand'] = "%{$search}%";
    $params[':search_desc'] = "%{$search}%";
}

if ($category !== '') {
    $where_clauses[] = "category = :category";
    $params[':category'] = $category;
}

if ($stock_status === 'available') {
    $where_clauses[] = "stock > 5";
} elseif ($stock_status === 'low') {
    $where_clauses[] = "stock > 0 AND stock <= 5";
} elseif ($stock_status === 'empty') {
    $where_clauses[] = "stock = 0";
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Opsi pengurutan (Sorting)
$order_by = match ($sort) {
    'price_asc'  => "price ASC",
    'price_desc' => "price DESC",
    'stock_desc' => "stock DESC",
    'name_asc'   => "name ASC",
    default      => "id DESC"
};

// Eksekusi query produk
$sql = "SELECT * FROM products {$where_sql} ORDER BY {$order_by}";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Hitung data statistik ringkasan toko
$stats_stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_items,
        COALESCE(SUM(stock), 0) as total_units,
        COALESCE(SUM(price * stock), 0) as total_valuation,
        COUNT(CASE WHEN stock <= 5 THEN 1 END) as low_stock_count
    FROM products
");
$stats = $stats_stmt->fetch();
?>

<!-- Hero & Summary Section -->
<section class="hero-section">
    <div class="hero-header">
        <div class="hero-title">
            <h2>Katalog Produk KicksVault</h2>
            <p>Kelola koleksi sneaker, pantau stok inventaris, dan perbarui harga secara efisien.</p>
        </div>
        <a href="create.php" class="btn btn-primary">
            <i class="ph-bold ph-plus-circle"></i> Tambah Produk Baru
        </a>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon indigo">
                <i class="ph-bold ph-sneaker"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Model Sepatu</div>
                <div class="value"><?= number_format($stats['total_items']) ?> Model</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <i class="ph-bold ph-package"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Stok Fisik</div>
                <div class="value"><?= number_format($stats['total_units']) ?> Pasang</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">
                <i class="ph-bold ph-coins"></i>
            </div>
            <div class="stat-info">
                <div class="label">Total Nilai Inventaris</div>
                <div class="value"><?= format_rupiah($stats['total_valuation']) ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon rose">
                <i class="ph-bold ph-warning-circle"></i>
            </div>
            <div class="stat-info">
                <div class="label">Perlu Restock</div>
                <div class="value"><?= number_format($stats['low_stock_count']) ?> Item</div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search Toolbar -->
<section class="filter-bar">
    <form method="GET" action="index.php" class="filter-form">
        <!-- Input Pencarian -->
        <div class="search-box">
            <i class="ph-bold ph-magnifying-glass search-icon"></i>
            <input type="text" 
                   name="search" 
                   class="search-input" 
                   placeholder="Cari nama sepatu, merk (Nike, Jordan, dll)..." 
                   value="<?= e($search) ?>">
        </div>

        <div class="filter-group">
            <!-- Filter Kategori -->
            <select name="category" class="form-select filter-auto-submit">
                <option value="">Semua Kategori</option>
                <?php 
                $categories = ['Sneakers', 'Running', 'Basketball', 'Casual', 'Boots'];
                foreach ($categories as $cat): 
                ?>
                    <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Filter Status Stok -->
            <select name="stock_status" class="form-select filter-auto-submit">
                <option value="">Semua Status Stok</option>
                <option value="available" <?= $stock_status === 'available' ? 'selected' : '' ?>>Tersedia (&gt; 5)</option>
                <option value="low" <?= $stock_status === 'low' ? 'selected' : '' ?>>Menipis (&le; 5)</option>
                <option value="empty" <?= $stock_status === 'empty' ? 'selected' : '' ?>>Stok Habis (0)</option>
            </select>

            <!-- Pengurutan (Sorting) -->
            <select name="sort" class="form-select filter-auto-submit">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Terbaru Ditambahkan</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Harga: Termurah</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Harga: Termahal</option>
                <option value="stock_desc" <?= $sort === 'stock_desc' ? 'selected' : '' ?>>Stok Terbanyak</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Nama: A - Z</option>
            </select>

            <button type="submit" class="btn btn-secondary">
                <i class="ph-bold ph-funnel"></i> Terapkan
            </button>

            <?php if ($search !== '' || $category !== '' || $stock_status !== '' || $sort !== 'newest'): ?>
                <a href="index.php" class="btn btn-secondary" title="Reset filter">
                    <i class="ph-bold ph-arrow-counter-clockwise"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<!-- Product Grid Section -->
<section>
    <div class="catalog-header">
        <h3>
            <i class="ph-bold ph-tag"></i> 
            Daftar Produk Sepatu
        </h3>
        <span class="results-count">
            Menampilkan <strong><?= count($products) ?></strong> sepatu
        </span>
    </div>

    <div class="products-grid">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="ph-bold ph-sneaker-move"></i>
                </div>
                <h4>Tidak Ada Produk Ditemukan</h4>
                <?php if ($stats['total_items'] == 0): ?>
                    <p>Database saat ini belum memiliki data produk sepatu. Anda dapat mengisi 8 produk sampel populer secara instan atau menambahkan sepatu baru.</p>
                    <a href="seed.php" class="btn btn-primary" style="margin-right: 8px;">
                        <i class="ph-bold ph-magic-wand"></i> Isi Data Sampel Otomatis
                    </a>
                    <a href="create.php" class="btn btn-secondary">Tambah Sepatu Baru</a>
                <?php else: ?>
                    <p>Coba gunakan kata kunci lain atau hapus filter untuk melihat seluruh koleksi sepatu.</p>
                    <a href="index.php" class="btn btn-secondary">Reset Pencarian</a>
                    <a href="create.php" class="btn btn-primary" style="margin-left: 8px;">Tambah Sepatu Baru</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($products as $item): ?>
                <?php
                // Tentukan lokasi file gambar
                $img_src = 'assets/images/shoes/default_placeholder.svg';
                if (!empty($item['image']) && file_exists(__DIR__ . '/assets/images/shoes/' . $item['image'])) {
                    $img_src = 'assets/images/shoes/' . $item['image'];
                }
                ?>
                <article class="product-card" id="product-card-<?= (int)$item['id'] ?>">
                    <div class="card-media">
                        <img src="<?= e($img_src) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                        <div class="card-badges">
                            <span class="badge badge-category"><?= e($item['category']) ?></span>
                            <?= render_stock_badge((int)$item['stock']) ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card-brand-row">
                            <span class="brand-name"><?= e($item['brand']) ?></span>
                            <span class="product-size"><i class="ph-bold ph-ruler"></i> EU <?= e($item['size']) ?></span>
                        </div>

                        <h4 class="product-title">
                            <a href="view.php?id=<?= (int)$item['id'] ?>">
                                <?= htmlspecialchars($item['name'], ENT_QUOTES, "UTF-8") ?>
                            </a>
                        </h4>

                        <p class="product-desc">
                            <?= e($item['description'] ?: 'Belum ada deskripsi untuk produk ini.') ?>
                        </p>

                        <div class="card-price-row">
                            <div>
                                <span style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase;">Harga Retail</span>
                                <div class="price-tag"><?= format_rupiah($item['price']) ?></div>
                            </div>
                        </div>

                        <div class="card-actions">
                            <a href="view.php?id=<?= (int)$item['id'] ?>" class="btn btn-secondary btn-sm" title="Lihat Detail">
                                <i class="ph-bold ph-eye"></i> Detail
                            </a>
                            <a href="edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-secondary btn-sm" title="Edit Data">
                                <i class="ph-bold ph-pencil-simple"></i> Edit
                            </a>
                            <button type="button" 
                                    class="btn btn-danger btn-sm btn-delete-trigger" 
                                    data-id="<?= (int)$item['id'] ?>" 
                                    data-name="<?= e($item['name']) ?>" 
                                    title="Hapus Produk">
                                <i class="ph-bold ph-trash"></i>
                            </button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
