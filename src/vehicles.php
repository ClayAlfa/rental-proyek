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
    <div class="text-center mb-5">
      <h2 class="fw-bold mb-3" style="font-size: 2.5rem; color: #2d3748;">🚗 Katalog Kendaraan Premium</h2>
      <p class="text-muted fs-5">Jelajahi koleksi lengkap kendaraan berkualitas tinggi</p>
    </div>
    
    <div class="row g-4">
      <?php foreach ($vehicles as $v): ?>
      <div class="col-md-4">
        <?php 
        $vehicleType = strtolower($v['type_name'] ?? 'car');
        $typeClass = match($vehicleType) {
          'mobil' => 'car',
          'motor' => 'motorcycle', 
          'sepeda listrik' => 'ebike',
          default => 'car'
        };
        $typeIcon = match($vehicleType) {
          'mobil' => '🚗',
          'motor' => '🏍️',
          'sepeda listrik' => '⚡',
          default => '🚗'
        };
        $statusIcon = match($v['status']) {
          'Tersedia' => '✅',
          'Disewa' => '🔄',
          'Servis' => '🔧',
          default => '❓'
        };
        ?>
        <div class="card-vehicle <?= $typeClass ?> h-100 animate-fade-in-up">
          <div class="vehicle-image-container">
            <img src="<?= h($v['image'] ?: 'https://placehold.co/600x400'); ?>" 
                 class="vehicle-image" alt="<?= h($v['brand'].' '.$v['model']); ?>">
            <div class="vehicle-badge <?= $typeClass ?>">
              <?= $typeIcon ?> <?= h($v['type_name'] ?: 'Kendaraan'); ?>
            </div>
            <div class="position-absolute top-0 start-0 m-3">
              <span class="status-badge <?= strtolower($v['status']); ?>">
                <?= $statusIcon ?> <?= h($v['status']); ?>
              </span>
            </div>
          </div>
          
          <div class="vehicle-info">
            <h5 class="vehicle-title"><?= h($v['brand'].' '.$v['model']); ?></h5>
            <div class="vehicle-price">
              <?= rupiah($v['price_per_day']); ?>/hari
            </div>
            
            <div class="vehicle-specs">
              <div class="spec-item">
                <i class="bi bi-people"></i>
                <?= h($v['seats']); ?> Penumpang
              </div>
              <div class="spec-item">
                <i class="bi bi-gear"></i>
                <?= h($v['transmission']); ?>
              </div>
              <div class="spec-item">
                <i class="bi bi-fuel-pump"></i>
                <?= h($v['fuel']); ?>
              </div>
            </div>

            <?php if (isset($_SESSION['user'])): ?>
              <?php if ($v['status'] === 'Tersedia'): ?>
                <a href="/rental/src/booking.php?vid=<?= $v['id']; ?>" class="btn btn-<?= $typeClass ?> w-100">
                  <i class="bi bi-lightning"></i> Sewa Sekarang
                </a>
              <?php else: ?>
                <button class="btn btn-secondary w-100" disabled>
                  <i class="bi bi-x-circle"></i> Tidak Tersedia
                </button>
              <?php endif; ?>
              </a>
            <?php else: ?>
              <a href="/rental/src/login.php" class="btn btn-<?= $typeClass ?> w-100">
                <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      
      <?php if (!$vehicles): ?>
        <div class="col-12">
          <div class="text-center py-5">
            <div class="mb-4" style="font-size: 4rem;">🚗</div>
            <h4 class="text-muted">Belum ada kendaraan tersedia</h4>
            <p class="text-muted">Kendaraan akan segera hadir untuk melayani perjalanan Anda</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include_once __DIR__.'/includes/footer.php'; ?>