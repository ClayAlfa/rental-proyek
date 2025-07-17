<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    /* ambil user + role */
    $stmt = $pdo->prepare("
        SELECT u.*, r.role_name
        FROM users u
        JOIN roles r ON r.id = u.role_id
        WHERE u.username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* ------- verifikasi hash ganda (bcrypt atau sha256) ------- */
    $ok = false;
    if ($user) {
        $hash = $user['password'];

        // jika hash diawali $2y$ => bcrypt
        if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2b$') || str_starts_with($hash, '$2a$')) {
            $ok = password_verify($password, $hash);
        }
        // jika 64 karakter heksadesimal => sha256 lama
        elseif (preg_match('/^[0-9a-f]{64}$/i', $hash)) {
            $ok = hash_equals($hash, hash('sha256', $password));
        }
    }

    if ($user && $ok) {
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'name' => $user['full_name'],
            'role' => $user['role_name']
        ];

        /* redirect sesuai role */
        if ($user['role_name'] === 'admin') {
            redirect('admin/dashboard.php');
        } else {
            redirect('user_dashboard.php');
        }
    } else {
        $error = 'Username atau password salah';
    }
}

include_once __DIR__.'/includes/header.php';
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h3 class="mb-4 text-center">Login</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error); ?></div>
      <?php endif; ?>

      <form method="POST" class="card card-body shadow-sm">
        <input name="username" class="form-control mb-3" placeholder="Username" required>
        <input name="password" type="password" class="form-control mb-3" placeholder="Password" required>
        <button class="btn btn-gradient w-100">Masuk</button>
      </form>

      <p class="text-center small mt-3">Belum punya akun?
        <a href="register.php">Daftar di sini</a>
      </p>
    </div>
  </div>
</div>
<?php include_once __DIR__.'/includes/footer.php'; ?>