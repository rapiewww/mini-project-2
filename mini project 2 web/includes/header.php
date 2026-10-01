<?php
/**
 * Header Template
 * File: includes/header.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/functions.php';

$page_title = $page_title ?? 'Koleksi Sepatu & Manajemen Produk';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KicksVault - Sistem Manajemen Produk Toko Sepatu Berbasis PHP PDO & MySQL">
    <title><?= e($page_title) ?> | KicksVault Store</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons CDN -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Sticky Navigation -->
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="brand-logo">
                <div class="brand-icon">
                    <i class="ph-bold ph-sneaker"></i>
                </div>
                <div class="brand-text">
                    <h1>KICKSVAULT</h1>
                    <span>SNEAKER MANAGER</span>
                </div>
            </a>

            <div class="nav-actions">
                <a href="index.php" class="btn btn-secondary">
                    <i class="ph-bold ph-squares-four"></i>
                    <span>Katalog</span>
                </a>
                <a href="create.php" class="btn btn-primary" id="btn-add-product">
                    <i class="ph-bold ph-plus-circle"></i>
                    <span>Tambah Sepatu</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="container">
        <!-- Flash Alert Message (PRG Notification) -->
        <?php if ($flash): ?>
            <div style="margin-top: 24px;">
                <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                    <div class="alert-content">
                        <?php if ($flash['type'] === 'success'): ?>
                            <i class="ph-bold ph-check-circle"></i>
                        <?php elseif ($flash['type'] === 'danger'): ?>
                            <i class="ph-bold ph-warning-octagon"></i>
                        <?php else: ?>
                            <i class="ph-bold ph-info"></i>
                        <?php endif; ?>
                        <span><?= e($flash['message']) ?></span>
                    </div>
                    <button type="button" class="alert-close" aria-label="Tutup pesan">&times;</button>
                </div>
            </div>
        <?php endif; ?>
