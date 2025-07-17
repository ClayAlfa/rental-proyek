<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';

require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

// Validasi input
if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'ID dan status harus diisi']);
    exit;
}

// Validasi status yang diizinkan
$allowed_statuses = ['Dipesan', 'Ongoing', 'Selesai', 'Dibatalkan'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

try {
    // Cek apakah rental exists
    $stmt = $pdo->prepare("SELECT id, status, vehicle_id FROM rentals WHERE id = ?");
    $stmt->execute([$id]);
    $rental = $stmt->fetch();
    
    if (!$rental) {
        echo json_encode(['success' => false, 'message' => 'Rental tidak ditemukan']);
        exit;
    }
    
    // Update status rental
    $stmt = $pdo->prepare("UPDATE rentals SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $id]);
    
    if ($result) {
        // Update status kendaraan berdasarkan status rental
        $vehicle_status = match($status) {
            'Ongoing' => 'Disewa',
            'Selesai', 'Dibatalkan' => 'Tersedia',
            default => null
        };
        
        if ($vehicle_status) {
            $stmt = $pdo->prepare("UPDATE vehicles SET status = ? WHERE id = ?");
            $stmt->execute([$vehicle_status, $rental['vehicle_id']]);
        }
        
        // Log aktivitas
        $stmt = $pdo->prepare("INSERT INTO logs (user_id, activity) VALUES (?, ?)");
        $stmt->execute([
            $_SESSION['user']['id'],
            "Status rental ID {$id} diubah menjadi {$status}"
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Status berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>