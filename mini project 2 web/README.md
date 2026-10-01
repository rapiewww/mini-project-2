# 👟 KicksVault — Sneaker Store & Product Management System

> **Tugas Akhir: Product Manager (Mini Project 2 Web - Pemrograman Web)**  
> Aplikasi web manajemen inventaris dan katalog produk toko sepatu modern berbasis **PHP (Native PDO)** dan **MySQL**, dirancang dengan fokus pada keamanan data, validasi ketat, serta estetika visual premium.

---

## 📌 Daftar Isi
- [Tentang Aplikasi](#-tentang-aplikasi)
- [Kesesuaian dengan Persyaratan Tugas](#-kesesuaian-dengan-persyaratan-tugas)
- [Fitur Utama](#-fitur-utama)
- [Tangkapan Layar Sistem (Screenshots)](#-tangkapan-layar-sistem-screenshots)
- [Struktur Proyek](#-struktur-proyek)
- [Panduan Instalasi di MAMP (macOS)](#-panduan-instalasi-di-mamp-macos)
- [Petunjuk Memasukkan Foto Screenshot](#-petunjuk-memasukkan-foto-screenshot)
- [Lisensi](#-lisensi)

---

## 🌟 Tentang Aplikasi
**KicksVault** adalah aplikasi katalog dan manajemen inventaris toko sepatu sneakers. Sistem ini dibangun tanpa framework berat (PHP Native) namun menerapkan standar arsitektur profesional:
- **Keamanan Input & Output**: Menggunakan **PDO Prepared Statements** untuk mencegah SQL Injection serta fungsi `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` untuk proteksi Cross-Site Scripting (XSS).
- **Anti Resubmission on Refresh**: Menerapkan pola **PRG (Post/Redirect/Get)** didukung Flash Session Message agar tidak terjadi duplikasi data saat pengguna menekan tombol F5 / Refresh browser.
- **Proteksi CSRF**: Setiap aksi formulir krusial (terutama penghapusan produk dengan metode `POST`) dilindungi oleh token acak CSRF berbasis sesi kriptografis.

---

## ✅ Kesesuaian dengan Persyaratan Tugas

| Persyaratan Tugas (Slide) | Status | Implementasi pada KicksVault |
|---|:---:|---|
| **Tujuan: Aman terhadap input dasar** | ✅ | Validasi tipe data, trim input, filter sanitasi, dan PDO Parameter Binding. |
| **Menghindari data ganda saat refresh** | ✅ | Pola **PRG (Post/Redirect/Get)** dengan session flash notification. |
| **Syarat Query: INSERT -> `$pdo->prepare(...)`** | ✅ | Diterapkan di `create.php`. |
| **Syarat Query: SELECT by ID -> `$pdo->prepare(...)`** | ✅ | Diterapkan di `edit.php` & `view.php`. |
| **Syarat Query: UPDATE -> `$pdo->prepare(...)`** | ✅ | Diterapkan di `edit.php`. |
| **Syarat Query: DELETE -> `$pdo->prepare(...)`** | ✅ | Diterapkan di `delete.php`. |
| **Syarat Output: `htmlspecialchars(..., ENT_QUOTES, "UTF-8")`** | ✅ | Digunakan pada seluruh data dinamis di template kartu & detail produk. |
| **Fitur: Create (nama, kategori, harga, stok)** | ✅ | Form lengkap di `create.php` (+ Brand, Size, Gambar, & Deskripsi). |
| **Fitur: Read (card responsif)** | ✅ | Grid card interaktif di `index.php` yang responsif di HP, Tablet, & Desktop. |
| **Fitur: Update (edit berdasarkan ID)** | ✅ | `edit.php?id={id}` dengan validasi nama unik pengecualian diri sendiri. |
| **Fitur: Delete (POST + CSRF)** | ✅ | Modal konfirmasi dengan form `POST` dan token CSRF di `delete.php`. |
| **Validasi: nama &ge; 3 karakter** | ✅ | Validasi `mb_strlen($name) < 3` menghasilkan feedback error inline. |
| **Validasi: harga &gt; 0** | ✅ | Validasi numerik & non-negatif / non-nol. |
| **Validasi: stok &ge; 0** | ✅ | Validasi bilangan bulat non-negatif. |
| **Validasi: nama unik** | ✅ | Dicek di database melalui `COUNT(*)` sebelum proses simpan/update. |

---

## 🚀 Fitur Utama

1. **Dashboard Statistik Inventaris**:
   - Total model sepatu terdaftar.
   - Total pasang stok fisik gudang.
   - Estimasi total valuasi aset inventaris (Rupiah).
   - Indikator peringatan sepatu yang perlu segera di-restock.
2. **Sistem Pencarian & Penyaringan Fleksibel**:
   - Pencarian teks kata kunci secara instan (nama sepatu, merk seperti Nike/Adidas/Jordan, atau deskripsi).
   - Filter Kategori: *Sneakers*, *Running*, *Basketball*, *Casual*, dan *Boots*.
   - Filter Ketersediaan Stok: *Tersedia*, *Menipis (&le; 5)*, dan *Habis (0)*.
   - Sorting / Pengurutan: *Terbaru*, *Harga Termurah*, *Harga Termahal*, *Stok Terbanyak*, dan *Alfabetis (A-Z)*.
3. **Upload Foto Sepatu & Fallback Otomatis**:
   - Mendukung upload file gambar produk (JPG, PNG, WEBP, SVG) dengan batas maksimal 3MB.
   - Dilengkapi asset vektor bawaan untuk model sepatu terpopuler.
4. **Desain Modern Sneaker Boutique**:
   - Antarmuka bertema gelap (*Dark Luxury Sneaker aesthetic*) dengan Glassmorphism, font Google 'Plus Jakarta Sans', micro-interaction hover, dan badge status stok.

---

## 📸 Tangkapan Layar Sistem (Screenshots)

Berikut adalah dokumentasi visual antarmuka sistem KicksVault:

### 1. Halaman Utama / Katalog Produk & Ringkasan Metrik
![Katalog Produk](assets/screenshots/01-dashboard.png)

### 2. Fitur Pencarian & Filter Kategori / Stok
![Pencarian dan Filter](assets/screenshots/02-search-filter.png)

### 3. Formulir Tambah Sepatu Baru
![Tambah Produk Baru](assets/screenshots/03-add-product.png)

### 4. Validasi Input & Deteksi Nama Kembar (Unik)
![Validasi Error](assets/screenshots/04-validation-error.png)

### 5. Formulir Edit Data Sepatu by ID
![Edit Produk](assets/screenshots/05-edit-product.png)

### 6. Halaman Detail Spesifikasi Sepatu
![Detail Produk](assets/screenshots/06-detail-product.png)

### 7. Pop-up Modal Hapus Sepatu (POST + Proteksi CSRF)
![Modal Hapus Produk](assets/screenshots/07-delete-modal.png)

### 8. Struktur Basis Data di phpMyAdmin MAMP
![Database phpMyAdmin](assets/screenshots/08-database-mamp.png)

---

## 📁 Struktur Proyek

```text
mini project 2 web/
├── config/
│   └── database.php         # Koneksi PDO (Auto-deteksi port MAMP 8889 & 3306)
├── includes/
│   ├── header.php           # Template header, meta, navbar & flash message
│   ├── footer.php           # Template footer & modal konfirmasi delete CSRF
│   ├── functions.php        # Helper escaping e(), format rupiah, upload & flash PRG
│   └── csrf.php             # Generator & validator token CSRF
├── assets/
│   ├── css/
│   │   └── style.css        # Vanilla CSS modern design system
│   ├── js/
│   │   └── app.js           # Modal handler, live preview gambar & auto-dismiss alert
│   ├── images/
│   │   └── shoes/           # Sampel gambar & direktori upload foto sepatu
│   └── screenshots/         # Direktori penyimpanan foto screenshot sistem
│       └── README.md        # Panduan penamaan file screenshot
├── database.sql             # Skrip SQL skema tabel & data sampel untuk diimpor ke MAMP
├── index.php                # Read katalog sepatu, search, filter & statistik
├── create.php               # Form create sepatu + validasi aturan
├── edit.php                 # Form update sepatu by ID
├── delete.php               # Action handler delete (POST + CSRF token)
├── view.php                 # Halaman detail spesifikasi sepatu
└── README.md                # Dokumentasi proyek GitHub
```

---

## 💻 Panduan Instalasi di MAMP (macOS)

Ikuti langkah-langkah mudah berikut untuk menjalankan proyek di komputer lokal:

### 1. Pindahkan Folder Proyek
Pindahkan atau clone folder proyek ini ke dalam direktori `htdocs` milik MAMP:
```bash
# Lokasi default htdocs MAMP di macOS:
/Applications/MAMP/htdocs/mini-project-2-web
```

### 2. Nyalakan Server MAMP
1. Buka aplikasi **MAMP**.
2. Klik tombol **Start Servers** (pastikan lampu indikator Apache & MySQL berwarna hijau).

### 3. Impor Database ke phpMyAdmin
1. Buka browser dan kunjungi: [http://localhost:8888/MAMP/](http://localhost:8888/MAMP/) atau klik **Open WebStart page** di MAMP.
2. Pilih menu **Tools &rarr; phpMyAdmin**.
3. Klik tab **Import** (atau buat database baru bernama `kicksvault_db`).
4. Klik **Choose File** dan pilih file [`database.sql`](database.sql) dari folder proyek ini.
5. Klik **Go / Kirim** di bagian bawah. Tabel `products` beserta data sampel awal sepatu akan otomatis terpasang.

### 4. Akses Web Melalui Browser
Buka browser favorit Anda (Google Chrome / Safari) lalu akses:
```text
http://localhost:8888/mini-project-2-web/
```
*(Atau sesuaikan dengan nama folder proyek Anda di dalam direktori `htdocs`)*.

---

## 📷 Petunjuk Memasukkan Foto Screenshot

Untuk melengkapi tampilan [README.md](README.md) di repositori GitHub Anda:

1. Ambil screenshot layar sesuai 8 fitur yang tersedia pada aplikasi.
2. Masukkan file gambar tersebut ke dalam folder:
   ```text
   assets/screenshots/
   ```
3. Ubah nama (*rename*) file gambar Anda agar persis seperti daftar berikut:
   - `01-dashboard.png` (Katalog utama & statistik)
   - `02-search-filter.png` (Fitur filter & pencarian)
   - `03-add-product.png` (Form tambah produk)
   - `04-validation-error.png` (Contoh notifikasi validasi error)
   - `05-edit-product.png` (Form edit produk)
   - `06-detail-product.png` (Halaman detail produk)
   - `07-delete-modal.png` (Modal konfirmasi hapus dengan CSRF)
   - `08-database-mamp.png` (Tabel di phpMyAdmin)

Setelah file-file tersebut Anda masukkan dan Anda lakukan `git add`, `git commit`, dan `git push` ke GitHub, semua foto akan otomatis tertata rapi di halaman depan repository GitHub Anda!

---

## 📄 Lisensi
Proyek ini dibuat untuk keperluan akademis mata kuliah **Pemrograman Web** (Mini Project 2). Bebas digunakan dan dikembangkan untuk tujuan pembelajaran.
