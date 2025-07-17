<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
$error = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $role_id = $_POST['role_id'];
    
    // Validasi
    if(strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // Cek username sudah ada
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if($check->fetch()) {
            $error = 'Username sudah digunakan';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users(username,password,full_name,email,phone,role_id) VALUES(?,?,?,?,?,?)");
            $stmt->execute([$username, $hashed_password, $full_name, $email, $phone, $role_id]);
            redirect('index.php');
        }
    }
}
include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tambah Pengguna</h2>
        <a href="index.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm" style="max-width:600px">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username *</label>
                    <input name="username" type="text" class="form-control" value="<?= h($_POST['username'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password *</label>
                    <input name="password" type="password" class="form-control" minlength="6" required>
                    <div class="form-text">Minimal 6 karakter</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap *</label>
                    <input name="full_name" type="text" class="form-control" value="<?= h($_POST['full_name'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="<?= h($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input name="phone" type="text" class="form-control" value="<?= h($_POST['phone'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role_id" class="form-select" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= ($_POST['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                                <?= h($r['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-gradient">Simpan</button>
                <a href="index.php" class="btn btn-secondary ms-2">Batal</a>
            </form>
        </div>
    </div>
</div><?php include_once __DIR__.'/../../includes/footer.php'; ?>