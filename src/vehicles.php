<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';
include_once __DIR__.'/includes/header.php';

$vehicles = $pdo->query(
  "SELECT v.*, t.type_name
     FROM vehicles v
LEFT JOIN vehicle_types t ON t.id = v.type_id
 ORDER BY v.created_at DESC"
)->fetchAll();
?>

<section class="py-5">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Katalog Kendaraan</h2>
    <div class="row g-4">
      <?php foreach ($vehicles as $v): ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
          <img src="<?= h($v['image'] ?: 'https://placehold.co/600x400'); ?>" class="card-img-top" alt="">
          <div class="card-body">
            <h5 class="card-title"><?= h($v['brand'].' '.$v['model']); ?></h5>
            <p class="fw-bold text-gradient fs-5 mb-2">
              Rp <?= number_format($v['price_per_day'], 0, ',', '.'); ?>/hari
            </p>
            <ul class="list-unstyled small mb-3">
              <li>Tipe: <?= h($v['type_name'] ?: 'Tidak diketahui'); ?></li>
              <li><?= h($v['seats']); ?> Penumpang</li>
              <li><?= h($v['transmission']); ?>, <?= h($v['fuel']); ?></li>
            </ul>

            <?php if (isset($_SESSION['user'])): ?>
              <a href="/rental/src/booking.php?vid=<?= $v['id']; ?>" class="btn btn-gradient w-100">
                Sewa Sekarang
              </a>
            <?php else: ?>
              <a href="/rental/src/login.php" class="btn btn-gradient w-100">
                Login untuk Sewa
              </a>
            <?php endif; ?>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (!$vehicles): ?>
        <p class="text-center text-muted">Belum ada kendaraan tersedia.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include_once __DIR__.'/includes/footer.php'; ?>