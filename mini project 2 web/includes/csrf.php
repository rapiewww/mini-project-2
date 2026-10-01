<?php
/**
 * CSRF Protection Helper
 * File: includes/csrf.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Buat atau ambil CSRF Token dari sesi
 * @return string
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render elemen input hidden CSRF token untuk form HTML
 * @return string
 */
function csrf_field(): string {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validasi token CSRF yang dikirimkan via POST
 * @param string|null $token
 * @return bool
 */
function validate_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
