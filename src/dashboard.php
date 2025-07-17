<?php
/** Stand-alone dashboard/landing page tanpa query DB **/

include_once __DIR__.'/includes/header.php';

/* -------- DATA DUMMY (hapus bila DB sudah ada) -------- */
$vehicles = [
    [
        'brand'        => 'Toyota',
        'model'        => 'Fortuner',
        'price_per_day'=> 800000,
        'seats'        => 7,
        'transmission' => 'Automatic',
        'fuel'         => 'Diesel',
        'image'        => 'https://placehold.co/600x400?text=Fortuner'
    ],
    [
        'brand'        => 'Honda',
        'model'        => 'HR-V',
        'price_per_day'=> 600000,
        'seats'        => 5,
        'transmission' => 'Automatic',
        'fuel'         => 'Bensin',
        'image'        => 'https://placehold.co/600x400?text=HRV'
    ],
    [
        'brand'        => 'Toyota',
        'model'        => 'Innova',
        'price_per_day'=> 500000,
        'seats'        => 7,
        'transmission' => 'Manual',
        'fuel'         => 'Diesel',
        'image'        => 'https://placehold.co/600x400?text=Innova'
    ]
];
/* ------------------------------------------------------ */
?>

<!-- HERO -->
<section class="hero position-relative overflow-hidden py-5 text-dark">
  <div class="bg-blur bg-primary"></div>
  <div class="bg-blur bg-purple"></div>

  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <h1 class="fw-bold display-4 mb-3">
          Sewa <span class="text-gradient">Mobil</span><br>
          Terpercaya &amp; Terjangkau
        </h1>
        <p class="lead">
          Dapatkan kendaraan berkualitas dengan harga terbaik untuk perjalanan Anda.
          Proses mudah, cepat, dan terpercaya.
        </p>

        <a href="#vehicles" class="btn btn-lg btn-gradient me-2">Mulai Sewa Mobil</a>
        <a href="#catalog"  class="btn btn-outline-secondary btn-lg">Lihat Katalog</a>

        <div class="d-flex gap-5 fw-semibold mt-4">
          <div><span class="fs-3">500+</span><br>Mobil Tersedia</div>
          <div><span class="fs-3">1000+</span><br>Pelanggan Puas</div>
          <div><span class="fs-3">24/7</span><br>Layanan</div>
        </div>
      </div>

      <!-- kartu booking -->
      <div class="col-lg-6 text-lg-end">
        <div class="booking-card shadow-lg">
          <img src="https://images.unsplash.com/photo-1493238792000-8113da705763"
               class="img-fluid rounded" alt="SUV">
          <div class="p-3">
            <h5 class="mb-1">Booking Instan</h5>
            <small class="text-muted">Pesan mobil hanya dalam 3 menit</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LIST KENDARAAN -->
<section id="vehicles" class="py-5">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Pilihan Kendaraan</h2>
    <div class="row g-4" id="catalog">
      <?php foreach ($vehicles as $v): ?>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
          <img src="<?= h($v['image']); ?>" class="card-img-top"
               alt="<?= h($v['model']); ?>">
          <div class="card-body">
            <h5 class="card-title"><?= h($v['brand'].' '.$v['model']); ?></h5>
            <p class="fw-bold text-gradient fs-5 mb-2">
              Rp <?= number_format($v['price_per_day'],0,',','.'); ?>/hari
            </p>
            <ul class="list-unstyled small mb-3">
              <li><?= h($v['seats']); ?> Penumpang</li>
              <li><?= h($v['transmission']); ?></li>
              <li><?= h($v['fuel']); ?></li>
            </ul>
            <a href="/login.php" class="btn btn-gradient w-100">Sewa Sekarang</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include_once __DIR__.'/includes/footer.php'; ?>