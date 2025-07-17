<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';

require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Hapus log yang lebih dari 30 hari
    $stmt = $pdo->prepare("DELETE FROM logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $result = $stmt->execute();
    
    $deleted_count = $stmt->rowCount();
    
    if ($result) {
        // Log aktivitas pembersihan
        $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            'clear_logs',
            "Menghapus {$deleted_count} log lama (>30 hari)"
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => "Berhasil menghapus {$deleted_count} log lama",
            'deleted_count' => $deleted_count
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus log']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>