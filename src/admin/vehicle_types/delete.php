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

// Cek apakah tipe kendaraan ada
$stmt = $pdo->prepare("SELECT type_name FROM vehicle_types WHERE id = ?");
$stmt->execute([$id]);
$type_data = $stmt->fetch();

if (!$type_data) {
    redirect('index.php?error=not_found');
}

// Cek apakah masih ada kendaraan yang menggunakan tipe ini
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM vehicles WHERE type_id = ?");
$stmt->execute([$id]);
$vehicle_count = $stmt->fetch()['count'];

if ($vehicle_count > 0) {
    redirect('index.php?error=still_used&count=' . $vehicle_count);
}

try {
    // Hapus tipe kendaraan
    $stmt = $pdo->prepare("DELETE FROM vehicle_types WHERE id = ?");
    $stmt->execute([$id]);
    
    // Log aktivitas
    log_activity($_SESSION['user_id'], "Menghapus tipe kendaraan: {$type_data['type_name']}");
    
    redirect('index.php?success=deleted');
} catch (Exception $e) {
    redirect('index.php?error=delete_failed');
}