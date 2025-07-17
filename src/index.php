<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/functions.php';
include_once __DIR__.'/includes/header.php';

/* ----------------- FILTER KATEGORI ----------------- */
$type = $_GET['type'] ?? 'all';                // all | Mobil | Motor | Sepeda Listrik
$params = [];

$sql = "SELECT v.*, t.type_name
          FROM vehicles v
     LEFT JOIN vehicle_types t ON t.id = v.type_id";

if ($type !== 'all') {                         // jika filter aktif
  $sql .= " WHERE t.type_name = ?";
  $params[] = $type;
}

$sql .= " ORDER BY v.created_at DESC LIMIT 6";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --------------- DUMMY (hapus jika DB sudah terisi) --------------- */
if (!$vehicles) {
  $vehicles = [
    ['id'=>0,'brand'=>'Toyota','model'=>'Fortuner','price_per_day'=>800000,
     'seats'=>7,'transmission'=>'Automatic','fuel'=>'Diesel',
     'image'=>'https://placehold.co/600x400?text=Fortuner'],
    ['id'=>0,'brand'=>'Honda','model'=>'HR‑V','price_per_day'=>600000,
     'seats'=>5,'transmission'=>'Automatic','fuel'=>'Bensin',
     'image'=>'https://placehold.co/600x400?text=HR-V'],
    ['id'=>0,'brand'=>'Yadea','model'=>'G5','price_per_day'=>120000,
     'seats'=>1,'transmission'=>'Automatic','fuel'=>'Listrik',
     'image'=>'https://placehold.co/600x400?text=Yadea+G5'],
  ];
}
?>

<!-- =================== HERO =================== -->
<section class="hero">
  <div class="container position-relative">
    <div class="row align-items-center g-5 hero-content">
      <div class="col-lg-6">
        <h1 class="hero-title animate-fade-in-up">
          Sewa <span style="color: #ffd700;">Kendaraan</span><br>Modern & Terpercaya
        </h1>
        <p class="hero-subtitle animate-fade-in-up">Dapatkan kendaraan berkualitas dengan teknologi booking terdepan dan layanan premium untuk perjalanan terbaik Anda.</p>

        <div class="animate-fade-in-up">
          <a href="#vehicles" class="btn btn-lg btn-gradient me-3">🚗 Mulai Sewa Sekarang</a>
          <a href="vehicles.php" class="btn btn-lg" style="background: rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">📋 Lihat Katalog</a>
        </div>

        <div class="d-flex gap-5 fw-semibold mt-5 animate-fade-in-up" style="color: rgba(255,255,255,0.9);">
          <div class="text-center">
            <div class="fs-2 fw-bold" style="color: #ffd700;">500+</div>
            <div class="small">Kendaraan Tersedia</div>
          </div>
          <div class="text-center">
            <div class="fs-2 fw-bold" style="color: #ffd700;">1000+</div>
            <div class="small">Pelanggan Puas</div>
          </div>
          <div class="text-center">
            <div class="fs-2 fw-bold" style="color: #ffd700;">24/7</div>
            <div class="small">Layanan Support</div>
          </div>
        </div>
      </div>

      <!-- kartu booking -->
      <div class="col-lg-6 text-lg-end">
        <div class="booking-card animate-pulse">
          <img src="https://images.unsplash.com/photo-1493238792000-8113da705763"
               class="img-fluid" alt="SUV" style="height: 200px; object-fit: cover; width: 100%;">
          <div class="p-4">
            <h5 class="mb-2 fw-bold">⚡ Booking Instan</h5>
            <p class="text-muted mb-3">Pesan kendaraan hanya dalam 3 menit dengan teknologi AI</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge" style="background: var(--success-gradient); color: white;">✓ Tersedia</span>
              <small class="text-success fw-bold">Mulai 100rb/hari</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- === FILTER KATEGORI BARU === -->
<div class="container">
  <div class="filter-tabs justify-content-center">
    <a href="index.php?type=all#vehicles" class="filter-tab <?= $type==='all' ? 'active' : ''; ?>">
      🏠 Semua Kendaraan
    </a>

    <a href="index.php?type=Mobil#vehicles" class="filter-tab <?= $type==='Mobil' ? 'active' : ''; ?>">
      🚗 Mobil Premium
    </a>

    <a href="index.php?type=Motor#vehicles" class="filter-tab <?= $type==='Motor' ? 'active' : ''; ?>">
      🏍️ Motor Sport
    </a>

    <a href="index.php?type=Sepeda Listrik#vehicles" class="filter-tab <?= $type==='Sepeda Listrik' ? 'active' : ''; ?>">
      ⚡ E-Bike Eco
    </a>
  </div>
</div>

<!-- =================== LIST KENDARAAN =================== -->
<section id="vehicles" class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold mb-3" style="font-size: 2.5rem; color: #2d3748;">Pilihan Kendaraan Premium</h2>
      <p class="text-muted fs-5">Temukan kendaraan yang sempurna untuk setiap perjalanan Anda</p>
    </div>
    <div class="row g-4" id="catalog">
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
        ?>
        <div class="card-vehicle <?= $typeClass ?> h-100 animate-fade-in-up">
          <div class="vehicle-image-container">
            <img src="<?= h($v['image'] ?: 'https://placehold.co/600x400'); ?>" 
                 class="vehicle-image" alt="<?= h($v['brand'].' '.$v['model']); ?>">
            <div class="vehicle-badge <?= $typeClass ?>">
              <?= $typeIcon ?> <?= h($v['type_name'] ?? 'Kendaraan'); ?>
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
              <a href="booking.php?vid=<?= $v['id']; ?>" class="btn btn-<?= $typeClass ?> w-100">
                <i class="bi bi-lightning"></i> Sewa Sekarang
              </a>
            <?php else: ?>
              <a href="login.php" class="btn btn-<?= $typeClass ?> w-100">
                <i class="bi bi-box-arrow-in-right"></i> Login untuk Sewa
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <?php if (empty($vehicles)): ?>
      <div class="text-center py-5">
        <div class="mb-4" style="font-size: 4rem;">🚗</div>
        <h4 class="text-muted">Belum ada kendaraan tersedia</h4>
        <p class="text-muted">Kendaraan akan segera hadir untuk melayani perjalanan Anda</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include_once __DIR__.'/includes/footer.php'; ?>