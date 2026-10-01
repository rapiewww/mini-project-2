-- ========================================================
-- Database: `kicksvault_db`
-- Mini Project 2: Toko Sepatu (Product Management System)
-- Kompatibel dengan MAMP / XAMPP / MySQL 5.7+ / MariaDB
-- ========================================================

CREATE DATABASE IF NOT EXISTS `kicksvault_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `kicksvault_db`;

-- Struktur Tabel `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- Data Dummy Awal Produk Sepatu
-- ========================================================

INSERT INTO `products` (`id`, `name`, `category`, `brand`, `size`, `price`, `stock`, `image`, `description`) VALUES
(1, 'Nike Air Jordan 1 Retro High OG', 'Basketball', 'Nike', '41, 42, 43, 44', 2899000.00, 12, 'photo_jordan1.jpg', 'Siluet legendaris dengan balutan kulit premium dalam kombinasi warna ikonik Chicago. Sangat nyaman untuk gaya streetwear maupun performa lapangan.'),
(2, 'Adidas Ultraboost Light 23', 'Running', 'Adidas', '39, 40, 41, 42, 43', 2550000.00, 18, 'photo_ultraboost.jpg', 'Sepatu lari terdepan dengan teknologi bantalan BOOST 30% lebih ringan. Memberikan energi pengembalian maksimal di setiap langkah.'),
(3, 'New Balance 550 White Grey', 'Casual', 'New Balance', '40, 41, 42, 43, 44.5', 1899000.00, 8, 'photo_nb550.jpg', 'Gaya retro basket era 80-an yang timeless. Desain simpel, kulit tahan lama, dan sol karet kokoh untuk penggunaan sehari-hari.'),
(4, 'Nike Dunk Low Panda Black White', 'Sneakers', 'Nike', '38, 39, 40, 41, 42, 43', 1950000.00, 5, 'photo_dunk_panda.jpg', 'Salah satu sneaker paling populer di dunia. Desain monokrom hitam putih yang fleksibel dipadukan dengan berbagai outfit casual.'),
(5, 'Vans Old Skool Classic Black White', 'Casual', 'Vans', '39, 40, 41, 42, 43', 999000.00, 24, 'photo_vans_oldskool.jpg', 'Sepatu skate klasik dengan sidestripe ikonik. Terbuat dari kanvas dan suede berkualitas tinggi dengan waffle outsole khas Vans.'),
(6, 'Puma Suede Classic XXI', 'Sneakers', 'Puma', '40, 41, 42, 43', 1199000.00, 15, 'photo_puma_suede.jpg', 'Koleksi legendaris Puma sejak 1968. Dibuat dengan bahan suede lembut berkualitas tinggi dan aksen logo foil emas di bagian samping.'),
(7, 'Timberland 6-Inch Premium Waterproof Boot', 'Boots', 'Timberland', '41, 42, 43, 44', 3600000.00, 4, 'photo_timberland.jpg', 'Boot tahan air autentik dengan sol karet anti-selip dan insulasi prima untuk segala cuaca dan medan berat.'),
(8, 'Under Armour Curry 11 Championship', 'Basketball', 'Under Armour', '42, 43, 44, 45', 2799000.00, 7, 'photo_curry11.jpg', 'Sepatu signature Stephen Curry dengan teknologi UA Flow ganda untuk traksi tanpa tanding di lapangan basket kayu maupun outdoor.');
