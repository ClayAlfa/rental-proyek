<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';

require_user();

// Ambil ID kendaraan dari URL
$vid = $_GET['vid'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vid]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    echo "<p>Kendaraan tidak ditemukan.</p>";
    exit;
}

// Proses Form Booking
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentDate   = $_POST['rent_date'];
    $returnDate = $_POST['return_date'];
    $today      = date('Y-m-d');

    // Validasi tanggal
    if ($rentDate < $today) {
        $error = 'Tanggal sewa tidak boleh di masa lalu.';
    } elseif (strtotime($returnDate) <= strtotime($rentDate)) {
        $error = 'Tanggal kembali harus lebih besar dari tanggal sewa.';
    } else {
        // Cek jadwal bentrok
        $check = $pdo->prepare("
          SELECT COUNT(*) FROM rentals 
          WHERE vehicle_id = ? AND status = 'Dipesan'
            AND (
              (rent_date <= ? AND return_date >= ?) OR
              (rent_date <= ? AND return_date >= ?) OR
              (? <= rent_date AND ? >= return_date)
            )
        ");
        $check->execute([$vid, $rentDate, $rentDate, $returnDate, $returnDate, $rentDate, $returnDate]);
        $overlap = $check->fetchColumn();

        if ($overlap > 0) {
            $error = 'Kendaraan ini sudah dipesan pada tanggal tersebut.';
        } else {
            $days  = ceil((strtotime($returnDate) - strtotime($rentDate)) / 86400);
            $total = $days * $vehicle['price_per_day'];

            $pdo->prepare("
                INSERT INTO rentals (user_id, vehicle_id, rent_date, return_date, total_price, status)
                VALUES (?, ?, ?, ?, ?, 'Dipesan')
            ")->execute([
                $_SESSION['user']['id'],
                $vid,
                $rentDate,
                $returnDate,
                $total
            ]);

            $success = 'Pemesanan berhasil! Kami akan segera memproses pesanan Anda.';
        }
    }
}

include_once __DIR__.'/includes/header.php';
?>
<div class="container py-5" style="max-width: 600px;">
  <div class="text-center mb-4">
    <h2 class="fw-bold mb-3">🚗 Sewa <?= h($vehicle['brand'].' '.$vehicle['model']); ?></h2>
    <p class="text-muted">Lengkapi form booking untuk melanjutkan penyewaan</p>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success">
      <i class="bi bi-check-circle me-2"></i><?= h($success); ?>
    </div>
    <div class="text-center">
      <a href="index.php" class="btn btn-gradient btn-lg">🏠 Kembali ke Beranda</a>
    </div>
    <meta http-equiv="refresh" content="5;url=index.php">
  <?php else: ?>

    <?php if ($error): ?>
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i><?= h($error); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body p-4">
      <!-- Info kendaraan -->
        <div class="vehicle-specs mb-4">
          <div class="spec-item">
            <i class="bi bi-car-front"></i>
            <strong>Merk:</strong> <?= h($vehicle['brand']); ?>
          </div>
          <div class="spec-item">
            <i class="bi bi-tag"></i>
            <strong>Model:</strong> <?= h($vehicle['model']); ?>
          </div>
          <div class="spec-item">
            <i class="bi bi-currency-dollar"></i>
            <strong>Harga:</strong> <?= rupiah($vehicle['price_per_day']); ?>/hari
          </div>
          <div class="spec-item">
            <i class="bi bi-people"></i>
            <strong>Kapasitas:</strong> <?= h($vehicle['seats']); ?> orang
          </div>
          <div class="spec-item">
            <i class="bi bi-gear"></i>
            <strong>Transmisi:</strong> <?= h($vehicle['transmission']); ?>
          </div>
          <div class="spec-item">
            <i class="bi bi-fuel-pump"></i>
            <strong>Bahan Bakar:</strong> <?= h($vehicle['fuel']); ?>
          </div>
        </div>

      <!-- Form Booking -->
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold">📅 Tanggal Mulai Sewa</label>
          <input type="date" name="rent_date" class="form-control" required id="rent-date">
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold">📅 Tanggal Kembali</label>
          <input type="date" name="return_date" class="form-control" required id="return-date">
        </div>

        <div class="alert alert-info">
          <div class="d-flex justify-content-between align-items-center">
            <strong>💰 Estimasi Total Biaya:</strong>
            <span id="estimation" class="fs-5 fw-bold text-primary">-</span>
          </div>
        </div>

        <button class="btn btn-gradient w-100 btn-lg">
          <i class="bi bi-check-circle me-2"></i>Konfirmasi Sewa Sekarang
        </button>
      </form>
      </div>
    </div>

  <?php endif; ?>
</div>

<script>
  const rentInput = document.getElementById('rent-date');
  const returnInput = document.getElementById('return-date');
  const output = document.getElementById('estimation');
  const pricePerDay = <?= $vehicle['price_per_day']; ?>;

  function updateEstimation() {
    const start = new Date(rentInput.value);
    const end = new Date(returnInput.value);
    if (rentInput.value && returnInput.value && end > start) {
      const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
      const total = days * pricePerDay;
      output.innerHTML = '<i class="bi bi-currency-dollar me-1"></i>Rp ' + total.toLocaleString('id-ID') + ' <small class="text-muted">(' + days + ' hari)</small>';
    } else {
      output.innerText = '-';
    }
  }

  rentInput.addEventListener('change', updateEstimation);
  returnInput.addEventListener('change', updateEstimation);
</script>

<?php include_once __DIR__.'/includes/footer.php'; ?>
