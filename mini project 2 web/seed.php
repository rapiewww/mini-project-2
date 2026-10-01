<?php
/**
 * Seeder Helper - Mengisi & Memperbarui Data Awal Toko Sepatu dengan Foto Asli
 * File: seed.php
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

function seed_default_products(PDO $pdo, bool $forceUpdateImages = true): int {
    $sample_products = [
        [
            'name' => 'Nike Air Jordan 1 Retro High OG',
            'category' => 'Basketball',
            'brand' => 'Nike',
            'size' => '41, 42, 43, 44',
            'price' => 2899000.00,
            'stock' => 12,
            'image' => 'photo_jordan1.jpg',
            'description' => 'Siluet legendaris dengan balutan kulit premium dalam kombinasi warna ikonik Chicago. Sangat nyaman untuk gaya streetwear maupun performa lapangan.'
        ],
        [
            'name' => 'Adidas Ultraboost Light 23',
            'category' => 'Running',
            'brand' => 'Adidas',
            'size' => '39, 40, 41, 42, 43',
            'price' => 2550000.00,
            'stock' => 18,
            'image' => 'photo_ultraboost.jpg',
            'description' => 'Sepatu lari terdepan dengan teknologi bantalan BOOST 30% lebih ringan. Memberikan energi pengembalian maksimal di setiap langkah.'
        ],
        [
            'name' => 'New Balance 550 White Grey',
            'category' => 'Casual',
            'brand' => 'New Balance',
            'size' => '40, 41, 42, 43, 44.5',
            'price' => 1899000.00,
            'stock' => 8,
            'image' => 'photo_nb550.jpg',
            'description' => 'Gaya retro basket era 80-an yang timeless. Desain simpel, kulit tahan lama, dan sol karet kokoh untuk penggunaan sehari-hari.'
        ],
        [
            'name' => 'Nike Dunk Low Panda Black White',
            'category' => 'Sneakers',
            'brand' => 'Nike',
            'size' => '38, 39, 40, 41, 42, 43',
            'price' => 1950000.00,
            'stock' => 5,
            'image' => 'photo_dunk_panda.jpg',
            'description' => 'Salah satu sneaker paling populer di dunia. Desain monokrom hitam putih yang fleksibel dipadukan dengan berbagai outfit casual.'
        ],
        [
            'name' => 'Vans Old Skool Classic Black White',
            'category' => 'Casual',
            'brand' => 'Vans',
            'size' => '39, 40, 41, 42, 43',
            'price' => 999000.00,
            'stock' => 24,
            'image' => 'photo_vans_oldskool.jpg',
            'description' => 'Sepatu skate klasik dengan sidestripe ikonik. Terbuat dari kanvas dan suede berkualitas tinggi dengan waffle outsole khas Vans.'
        ],
        [
            'name' => 'Puma Suede Classic XXI',
            'category' => 'Sneakers',
            'brand' => 'Puma',
            'size' => '40, 41, 42, 43',
            'price' => 1199000.00,
            'stock' => 15,
            'image' => 'photo_puma_suede.jpg',
            'description' => 'Koleksi legendaris Puma sejak 1968. Dibuat dengan bahan suede lembut berkualitas tinggi dan aksen logo foil emas di bagian samping.'
        ],
        [
            'name' => 'Timberland 6-Inch Premium Waterproof Boot',
            'category' => 'Boots',
            'brand' => 'Timberland',
            'size' => '41, 42, 43, 44',
            'price' => 3600000.00,
            'stock' => 4,
            'image' => 'photo_timberland.jpg',
            'description' => 'Boot tahan air autentik dengan sol karet anti-selip dan insulasi prima untuk segala cuaca dan medan berat.'
        ],
        [
            'name' => 'Under Armour Curry 11 Championship',
            'category' => 'Basketball',
            'brand' => 'Under Armour',
            'size' => '42, 43, 44, 45',
            'price' => 2799000.00,
            'stock' => 7,
            'image' => 'photo_curry11.jpg',
            'description' => 'Sepatu signature Stephen Curry dengan teknologi UA Flow ganda untuk traksi tanpa tanding di lapangan basket kayu maupun outdoor.'
        ]
    ];

    $count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    if ($count === 0) {
        $insert_sql = "INSERT INTO products (name, category, brand, size, price, stock, image, description) 
                       VALUES (:name, :category, :brand, :size, :price, :stock, :image, :description)";
        $stmt = $pdo->prepare($insert_sql);

        foreach ($sample_products as $p) {
            $stmt->execute([
                ':name'        => $p['name'],
                ':category'    => $p['category'],
                ':brand'       => $p['brand'],
                ':size'        => $p['size'],
                ':price'       => $p['price'],
                ':stock'       => $p['stock'],
                ':image'       => $p['image'],
                ':description' => $p['description']
            ]);
        }
        return count($sample_products);
    } elseif ($forceUpdateImages) {
        // Perbarui gambar produk contoh yang masih berformat SVG agar menjadi foto asli JPG
        $update_stmt = $pdo->prepare("UPDATE products SET image = :image WHERE name = :name");
        foreach ($sample_products as $p) {
            $update_stmt->execute([
                ':image' => $p['image'],
                ':name'  => $p['name']
            ]);
        }
    }

    return $count;
}

// Jika diakses langsung via URL browser
if (php_sapi_name() !== 'cli' && basename($_SERVER['PHP_SELF']) === 'seed.php') {
    $total = seed_default_products($pdo, true);
    set_flash('success', 'Berhasil memperbarui katalog dengan foto sepatu asli (' . $total . ' item)!');
    header('Location: index.php');
    exit;
}
