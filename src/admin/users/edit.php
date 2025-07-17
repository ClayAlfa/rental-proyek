<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

/* --- Ambil data user --- */
$id = $_GET['id'] ?? 0;
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$id]);
$data = $user->fetch();

if (!$data) {
    redirect('index.php');
}

/* --- Ambil semua role --- */
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
$error = '';
$success = false;

/* --- Proses kirim form --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role_id = $_POST['role_id'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($full_name) || empty($role_id)) {
        $error = 'Field yang wajib diisi tidak boleh kosong';
    }
    // Validasi password jika diisi
    elseif (!empty($password) && strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    }
    // Validasi username unik (kecuali untuk user ini sendiri)
    else {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check->execute([$username, $id]);
        if($check->fetch()) {
            $error = 'Username sudah digunakan';
        } else {
            try {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Update data umum
                $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, phone = ?, role_id = ? WHERE id = ?");
                $result = $stmt->execute([$username, $full_name, $email, $phone, $role_id, $id]);
                
                // Update password jika diisi
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $stmt2 = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt2->execute([$hashed_password, $id]);
                }
                
                // Commit transaction
                $pdo->commit();
                
                // Set success message and redirect
                $_SESSION['success_message'] = 'Data pengguna berhasil diperbarui';
                
                // Redirect menggunakan JavaScript untuk memastikan tidak ada masalah dengan headers
                echo "<script>window.location.href='index.php';</script>";
                exit;
                
            } catch (Exception $e) {
                // Rollback transaction on error
                $pdo->rollback();
                $error = 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage();
            }
        }
    }
}

// Include header
include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Edit Pengguna</h2>
        <a href="index.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username *</label>
                    <input name="username" type="text" class="form-control" value="<?= h($_POST['username'] ?? $data['username']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" minlength="6">
                    <div class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input name="full_name" type="text" class="form-control" value="<?= h($_POST['full_name'] ?? $data['full_name']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="<?= h($_POST['email'] ?? $data['email']) ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input name="phone" type="text" class="form-control" value="<?= h($_POST['phone'] ?? $data['phone']) ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role_id" class="form-select" required>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (($_POST['role_id'] ?? $data['role_id']) == $r['id']) ? 'selected' : '' ?>>
                                <?= h($r['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-gradient">Update</button>
                <a href="index.php" class="btn btn-secondary ms-2">Batal</a>
            </form>
        </div>
    </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>
