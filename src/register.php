<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';

$success = $error = '';

/* ----------------------  PROSES KIRIM FORM  ---------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);

    /* Validasi dasar */
    if ($username === '' || $password === '' || $fullname === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        /* Username unik? */
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            /* Pastikan role customer ada */
            $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE role_name = 'customer' LIMIT 1");
            $roleStmt->execute();
            $role_id = $roleStmt->fetchColumn();
            if (!$role_id) {
                $pdo->prepare("INSERT INTO roles (role_name) VALUES ('customer')")->execute();
                $role_id = $pdo->lastInsertId();
            }

            /* Hash password aman */
            $hash = password_hash($password, PASSWORD_BCRYPT);

            /* Simpan user */
            $pdo->prepare("
                INSERT INTO users (username, password, full_name, email, phone, role_id, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ")->execute([
                $username,
                $hash,
                $fullname,
                $email ?: null,
                $phone ?: null,
                $role_id
            ]);

            /* Redirect ke halaman login */
            $_SESSION['flash_success'] = 'Pendaftaran berhasil! Silakan login.';
            header('Location: login.php');
            exit;
        }
    }
}

/* ----------------------  TAMPILKAN FORM  ---------------------- */
include_once __DIR__.'/includes/header.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <h3 class="mb-4 text-center">Daftar Akun</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error); ?></div>
      <?php elseif (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= h($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>

      <form method="POST" class="card card-body shadow-sm">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password (min. 6 karakter)</label>
          <input type="password" name="password" class="form-control" minlength="6" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email (opsional)</label>
          <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Telepon (opsional)</label>
          <input type="text" name="phone" class="form-control">
        </div>

        <button class="btn btn-gradient w-100">Daftar</button>
        <p class="text-center small mt-3 mb-0">
          Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
      </form>
    </div>
  </div>
</div>
<?php include_once __DIR__.'/includes/footer.php'; ?>