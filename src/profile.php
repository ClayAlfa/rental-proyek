<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';

require_user();

$user_id = $_SESSION['user']['id'];
$success = $error = '';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi
    if (empty($full_name)) {
        $error = 'Nama lengkap wajib diisi.';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif ($new_password && strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } elseif ($new_password && $new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif ($new_password && !password_verify($current_password, $user['password'])) {
        $error = 'Password saat ini salah.';
    } else {
        try {
            if ($new_password) {
                // Update dengan password baru
                $hash = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET full_name = ?, email = ?, phone = ?, password = ?
                    WHERE id = ?
                ");
                $stmt->execute([$full_name, $email ?: null, $phone ?: null, $hash, $user_id]);
            } else {
                // Update tanpa password
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET full_name = ?, email = ?, phone = ?
                    WHERE id = ?
                ");
                $stmt->execute([$full_name, $email ?: null, $phone ?: null, $user_id]);
            }

            // Update session
            $_SESSION['user']['name'] = $full_name;
            
            $success = 'Profil berhasil diperbarui!';
            
            // Refresh data user
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan saat memperbarui profil.';
        }
    }
}

include_once __DIR__.'/includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profil Saya</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i><?= h($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= h($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <!-- Info Akun -->
                            <div class="col-12">
                                <h5 class="text-muted border-bottom pb-2 mb-3">Informasi Akun</h5>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" value="<?= h($user['username']); ?>" disabled>
                                <small class="text-muted">Username tidak dapat diubah</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role</label>
                                <input type="text" class="form-control" value="Customer" disabled>
                            </div>

                            <!-- Data Pribadi -->
                            <div class="col-12 mt-4">
                                <h5 class="text-muted border-bottom pb-2 mb-3">Data Pribadi</h5>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold">Nama Lengkap *</label>
                                <input type="text" name="full_name" class="form-control" 
                                       value="<?= h($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= h($user['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Telepon</label>
                                <input type="text" name="phone" class="form-control" 
                                       value="<?= h($user['phone'] ?? ''); ?>">
                            </div>

                            <!-- Ubah Password -->
                            <div class="col-12 mt-4">
                                <h5 class="text-muted border-bottom pb-2 mb-3">Ubah Password (Opsional)</h5>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password Baru</label>
                                <input type="password" name="new_password" class="form-control" minlength="6">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control" minlength="6">
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                                    </button>
                                    <a href="user_dashboard.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Tambahan -->
            <div class="card border-0 shadow mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Informasi Akun</h6>
                    <div class="row text-sm">
                        <div class="col-md-6">
                            <strong>Bergabung:</strong> <?= date('d M Y', strtotime($user['created_at'])); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Terakhir Update:</strong> <?= date('d M Y H:i', strtotime($user['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__.'/includes/footer.php'; ?>