<?php
/**
 * auth.php – Helper autentikasi
 */
if (session_status() === PHP_SESSION_NONE) session_start();

/* Proteksi halaman */
function require_login(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: /rental/src/login.php');
        exit;
    }
}

/* Cek admin */
function is_admin(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

/* Proteksi halaman khusus admin */
function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        header('Location: /rental/src/user_dashboard.php');
        exit;
    }
}

/* Proteksi halaman khusus user */
function require_user(): void
{
    require_login();
    if (is_admin()) {
        header('Location: /rental/src/admin/dashboard.php');
        exit;
    }
}

/* Cek apakah user adalah customer */
function is_customer(): bool
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'customer';
}