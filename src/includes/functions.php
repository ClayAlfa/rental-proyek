<?php
/**
 * functions.php – Fungsi utilitas
 */

/* Escape HTML */
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/* Redirect */
function redirect(string $url): never
{
    header('Location: '.$url);
    exit;
}

/* Format rupiah */
function rupiah(int|float $n): string
{
    return 'Rp '.number_format($n, 0, ',', '.');
}

/* Get base URL */
function get_base_url(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = dirname($script);
    
    // Jika di admin folder atau subfolder admin, naik ke src folder
    if (strpos($path, '/admin') !== false) {
        // Hapus /admin dan path setelahnya
        $path = preg_replace('#/admin.*$#', '', $path);
    }
    
    return $protocol . $host . $path . '/';
}
