<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
require_admin();

// Statistik dasar
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalVehicles = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$totalRentals  = $pdo->query("SELECT COUNT(*) FROM rentals")->fetchColumn();

// Statistik rental
$ongoingRentals = $pdo->query("SELECT COUNT(*) FROM rentals WHERE status = 'Ongoing'")->fetchColumn();
$pendingRentals = $pdo->query("SELECT COUNT(*) FROM rentals WHERE status = 'Dipesan'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(total_price) FROM rentals WHERE status = 'Selesai'")->fetchColumn() ?: 0;

// Kendaraan yang sedang disewa
$rentedVehicles = $pdo->query("
    SELECT v.*, vt.type_name, u.full_name, u.username, r.rent_date, r.return_date, r.id as rental_id
    FROM vehicles v
    JOIN vehicle_types vt ON vt.id = v.type_id
    JOIN rentals r ON r.vehicle_id = v.id
    JOIN users u ON u.id = r.user_id
    WHERE r.status = 'Ongoing'
    ORDER BY r.rent_date DESC
")->fetchAll();

// Rental terbaru yang butuh perhatian
$pendingRentalsData = $pdo->query("
    SELECT r.*, u.full_name, u.username, u.phone, v.brand, v.model, vt.type_name
    FROM rentals r
    JOIN users u ON u.id = r.user_id
    JOIN vehicles v ON v.id = r.vehicle_id
    JOIN vehicle_types vt ON vt.id = v.type_id
    WHERE r.status = 'Dipesan'
    ORDER BY r.created_at DESC
    LIMIT 5
")->fetchAll();

// User terbaru
$recentUsers = $pdo->query("
    SELECT u.*, r.role_name
    FROM users u
    JOIN roles r ON r.id = u.role_id
    ORDER BY u.created_at DESC
    LIMIT 5
")->fetchAll();

include_once __DIR__.'/../includes/header.php';
?>
<div class="container-fluid py-4">
  <h1 class="h3 fw-bold mb-4">Dashboard <span class="text-gradient">Admin</span></h1>

  <!-- Statistik Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-start border-4 border-primary">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-people-fill icon-lg text-primary me-3"></i>
          <div><small class="text-muted">Total Pengguna</small><h3><?=$totalUsers;?></h3></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-start border-4 border-success">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-truck-front-fill icon-lg text-success me-3"></i>
          <div><small class="text-muted">Total Kendaraan</small><h3><?=$totalVehicles;?></h3></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-start border-4 border-warning">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-clock-history icon-lg text-warning me-3"></i>
          <div><small class="text-muted">Sedang Disewa</small><h3><?=$ongoingRentals;?></h3></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-start border-4 border-info">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-currency-dollar icon-lg text-info me-3"></i>
          <div><small class="text-muted">Total Pendapatan</small><h3><?=rupiah($totalRevenue);?></h3></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="row g-4 mb-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">Aksi Cepat</h5>
          <div class="d-flex gap-2 flex-wrap">
            <a href="users/index.php" class="btn btn-outline-primary"><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
            <a href="vehicles/index.php" class="btn btn-outline-success"><i class="bi bi-truck me-2"></i>Kelola Kendaraan</a>
            <a href="rentals/index.php" class="btn btn-outline-warning"><i class="bi bi-receipt me-2"></i>Laporan Penyewaan</a>
            <a href="settings/index.php" class="btn btn-outline-info"><i class="bi bi-gear me-2"></i>Pengaturan</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Kendaraan yang Sedang Disewa -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header bg-warning bg-opacity-10">
          <h5 class="card-title mb-0"><i class="bi bi-truck text-warning me-2"></i>Kendaraan Sedang Disewa</h5>
        </div>
        <div class="card-body">
          <?php if(empty($rentedVehicles)): ?>
            <div class="text-center py-4">
              <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
              <p class="text-muted mt-2">Tidak ada kendaraan yang sedang disewa</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Kendaraan</th>
                    <th>Penyewa</th>
                    <th>Periode</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($rentedVehicles as $rv): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <img src="<?=h($rv['image'])?:'/assets/img/default-car.png'?>" alt="<?=h($rv['brand'])?>"
                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                          <div class="fw-bold"><?=h($rv['brand'].' '.$rv['model'])?></div>
                          <small class="text-muted"><?=h($rv['type_name'])?></small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold"><?=h($rv['full_name'])?></div>
                      <small class="text-muted">@<?=h($rv['username'])?></small>
                    </td>
                    <td>
                      <small>
                        <?=date('d/m/Y', strtotime($rv['rent_date']))?><br>
                        s/d <?=date('d/m/Y', strtotime($rv['return_date']))?>
                      </small>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary" onclick="viewRentalDetail(<?=$rv['rental_id']?>)">Detail</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Rental Menunggu & User Terbaru -->
    <div class="col-md-4">
      <!-- Rental Menunggu Persetujuan -->
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-danger bg-opacity-10">
          <h6 class="card-title mb-0"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Menunggu Persetujuan</h6>
        </div>
        <div class="card-body">
          <?php if(empty($pendingRentalsData)): ?>
            <div class="text-center py-3">
              <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
              <p class="text-muted mt-2 mb-0">Semua rental telah diproses</p>
            </div>
          <?php else: ?>
            <?php foreach($pendingRentalsData as $pr): ?>
            <div class="d-flex align-items-center mb-3 p-2 bg-light rounded">
              <div class="flex-grow-1">
                <div class="fw-bold"><?=h($pr['full_name'])?></div>
                <small class="text-muted"><?=h($pr['brand'].' '.$pr['model'])?></small>
              </div>
              <div class="text-end">
                <button class="btn btn-sm btn-success" onclick="approveRental(<?=$pr['id']?>)">Setujui</button>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- User Terbaru -->
      <div class="card shadow-sm">
        <div class="card-header bg-primary bg-opacity-10">
          <h6 class="card-title mb-0"><i class="bi bi-person-plus text-primary me-2"></i>User Terbaru</h6>
        </div>
        <div class="card-body">
          <?php foreach($recentUsers as $ru): ?>
          <div class="d-flex align-items-center mb-3">
            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
              <?=strtoupper(substr($ru['full_name'], 0, 1))?>
            </div>
            <div class="flex-grow-1">
              <div class="fw-bold"><?=h($ru['full_name'])?></div>
              <small class="text-muted">
                <span class="badge bg-<?=$ru['role_name']=='admin'?'danger':'primary'?> me-1"><?=h($ru['role_name'])?></span>
                <?=date('d/m/Y', strtotime($ru['created_at']))?>
              </small>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Rental -->
<div class="modal fade" id="rentalDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Penyewaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="rentalDetailContent">
        <!-- Content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<script>
function viewRentalDetail(rentalId) {
    // Load rental detail via AJAX
    fetch(`rentals/detail.php?id=${rentalId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(data => {
        document.getElementById('rentalDetailContent').innerHTML = data;
        new bootstrap.Modal(document.getElementById('rentalDetailModal')).show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat detail rental: ' + error.message);
    });
}

function approveRental(rentalId) {
    if(confirm('Setujui rental ini?')) {
        fetch('rentals/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${rentalId}&status=Ongoing`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Gagal menyetujui rental: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyetujui rental');
        });
    }
}

// Auto refresh dashboard every 30 seconds
setInterval(() => {
    // Only refresh if no modal is open
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 30000);
</script>

<?php include_once __DIR__.'/../includes/footer.php'; ?>
