    </main>

    <!-- Modal Konfirmasi Hapus Produk (POST + CSRF Protection) -->
    <div class="modal-overlay" id="deleteConfirmModal">
        <div class="modal-content">
            <div class="modal-icon-danger">
                <i class="ph-bold ph-trash"></i>
            </div>
            <h3 class="modal-title">Hapus Produk Sepatu?</h3>
            <p class="modal-desc">
                Apakah Anda yakin ingin menghapus produk <strong id="deleteTargetName" style="color: #fff;"></strong>? Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen dari sistem.
            </p>
            <form id="deleteForm" method="POST" action="delete.php">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="deleteTargetId" value="">
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ph-bold ph-trash"></i> Ya, Hapus Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <p>&copy; <?= date('Y') ?> <strong>KicksVault</strong> &bull; Mini Project 2 Web - Product Manager.</p>
            <div class="footer-badges">
                <span class="tech-tag"><i class="ph-bold ph-shield-check"></i> CSRF Protected</span>
                <span class="tech-tag"><i class="ph-bold ph-database"></i> PDO Prepared</span>
                <span class="tech-tag"><i class="ph-bold ph-lock-key"></i> XSS Sanitized</span>
                <span class="tech-tag"><i class="ph-bold ph-arrows-clockwise"></i> PRG Pattern</span>
            </div>
        </div>
    </footer>

    <!-- App JavaScript -->
    <script src="assets/js/app.js"></script>
</body>
</html>
