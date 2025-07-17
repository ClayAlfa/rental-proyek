<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/functions.php';
?>
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
  <div class="container">
    <!-- ganti URL absolut ➜ relatif -->
    <a class="navbar-brand fw-bold" href="<?= get_base_url() ?>index.php">RentalKu</a>

    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto">
        <!-- Beranda -->
        <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>index.php">Beranda</a></li>

        <?php if (isset($_SESSION['user'])): ?>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <!-- Dashboard admin -->
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>admin/dashboard.php">Dashboard Admin</a></li>
          <?php else: ?>
            <!-- Dashboard user -->
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>user_dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>vehicles.php">Kendaraan</a></li>
          <?php endif; ?>
          <!-- Profile & Logout -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?= h($_SESSION['user']['name']); ?>
            </a>
            <ul class="dropdown-menu">
              <?php if ($_SESSION['user']['role'] !== 'admin'): ?>
                <li><a class="dropdown-item" href="<?= get_base_url() ?>profile.php">Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= get_base_url() ?>logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <!-- Login & Register -->
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url() ?>register.php">Daftar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
