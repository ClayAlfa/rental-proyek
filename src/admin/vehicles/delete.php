<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_login();
require_admin();

$id = intval($_GET['id'] ?? 0);

// Validasi ID
if ($id <= 0) {
    redirect('index.php?error=invalid_id');
}

// Cek apakah kendaraan ada
$stmt = $pdo->prepare("SELECT brand, model, status FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle_data = $stmt->fetch();

if (!$vehicle_data) {
    redirect('index.php?error=not_found');
}

// Cek apakah kendaraan sedang disewa
if ($vehicle_data['status'] === 'Disewa') {
    redirect('index.php?error=vehicle_rented');
}

// Cek apakah ada rental aktif untuk kendaraan ini
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM rentals WHERE vehicle_id = ? AND status IN ('Pending', 'Ongoing')");
$stmt->execute([$id]);
$active_rentals = $stmt->fetch()['count'];

if ($active_rentals > 0) {
    redirect('index.php?error=has_active_rentals&count=' . $active_rentals);
}

try {
    // Hapus kendaraan
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    
    // Log aktivitas
    log_activity($_SESSION['user_id'], "Menghapus kendaraan: {$vehicle_data['brand']} {$vehicle_data['model']}");
    
    redirect('index.php?success=deleted');
} catch (Exception $e) {
    redirect('index.php?error=delete_failed');
}