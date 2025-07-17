<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

$id = $_GET['id'] ?? 0;

if($id) {
    // Cek apakah user ada
    $user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user->execute([$id]);
    $userData = $user->fetch();
    
    if($userData) {
        // Jangan hapus admin utama (id = 1)
        if($id != 1) {
            // Hapus user
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        }
    }
}

redirect('index.php');