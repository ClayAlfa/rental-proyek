<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

// Filter
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$sql = "SELECT r.*, u.username, u.full_name, v.brand, v.model, v.type_id, vt.type_name 
        FROM rentals r 
        JOIN users u ON u.id = r.user_id 
        JOIN vehicles v ON v.id = r.vehicle_id 
        JOIN vehicle_types vt ON vt.id = v.type_id";
        
$params = [];
$where = [];

if($status_filter) {
    $where[] = "r.status = ?";
    $params[] = $status_filter;
}

if($date_from) {
    $where[] = "r.rent_date >= ?";
    $params[] = $date_from;
}

if($date_to) {
    $where[] = "r.rent_date <= ?";
    $params[] = $date_to;
}

if($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rentals = $stmt->fetchAll();

// Statistik
$stats = $pdo->query("SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN status = 'Ongoing' THEN 1 END) as ongoing,
    COUNT(CASE WHEN status = 'Selesai' THEN 1 END) as completed,
    COUNT(CASE WHEN status = 'Dibatalkan' THEN 1 END) as cancelled,
    SUM(CASE WHEN status = 'Selesai' THEN total_price ELSE 0 END) as total_revenue
    FROM rentals")->fetch();

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Laporan Penyewaan</h2>
        <div>
            <button class="btn btn-success" onclick="exportData()">📊 Export Excel</button>
            <button class="btn btn-info" onclick="printReport()">🖨️ Print</button>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Penyewaan</h5>
                    <h3><?= number_format($stats['total']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>Sedang Berlangsung</h5>
                    <h3><?= number_format($stats['ongoing']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Selesai</h5>
                    <h3><?= number_format($stats['completed']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Total Pendapatan</h5>
                    <h3><?= rupiah($stats['total_revenue']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Dipesan" <?= $status_filter == 'Dipesan' ? 'selected' : '' ?>>Dipesan</option>
                        <option value="Ongoing" <?= $status_filter == 'Ongoing' ? 'selected' : '' ?>>Ongoing</option>
                        <option value="Selesai" <?= $status_filter == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="Dibatalkan" <?= $status_filter == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="date_from" class="form-control" value="<?= h($date_from) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="date_to" class="form-control" value="<?= h($date_to) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Laporan -->
    <div class="table-responsive">
        <table class="table table-hover align-middle" id="rentalTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Kendaraan</th>
                    <th>Periode</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rentals)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">Tidak ada data penyewaan</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($rentals as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                        <td>
                            <div class="fw-bold"><?= h($r['full_name']) ?></div>
                            <small class="text-muted">@<?= h($r['username']) ?></small>
                        </td>
                        <td>
                            <div class="fw-bold"><?= h($r['brand'] . ' ' . $r['model']) ?></div>
                            <small class="text-muted"><?= h($r['type_name']) ?></small>
                        </td>
                        <td>
                            <small>
                                <?= date('d/m/Y', strtotime($r['rent_date'])) ?><br>
                                s/d <?= date('d/m/Y', strtotime($r['return_date'])) ?>
                            </small>
                        </td>
                        <td class="fw-bold text-success"><?= rupiah($r['total_price']) ?></td>
                        <td>
                            <?php 
                            $statusClass = match($r['status']) {
                                'Dipesan' => 'bg-info',
                                'Ongoing' => 'bg-warning',
                                'Selesai' => 'bg-success',
                                'Dibatalkan' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= h($r['status']) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewDetail(<?= $r['id'] ?>)">Detail</button>
                                <?php if($r['status'] == 'Dipesan'): ?>
                                    <button class="btn btn-success" onclick="updateStatus(<?= $r['id'] ?>, 'Ongoing')">Mulai</button>
                                <?php elseif($r['status'] == 'Ongoing'): ?>
                                    <button class="btn btn-info" onclick="updateStatus(<?= $r['id'] ?>, 'Selesai')">Selesai</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function exportData() {
    // Implementasi export ke Excel
    alert('Fitur export akan segera tersedia');
}

function printReport() {
    window.print();
}

function viewDetail(id) {
    // Modal detail atau redirect ke halaman detail
    alert('Detail rental ID: ' + id);
}

function updateStatus(id, status) {
    if(confirm('Ubah status menjadi ' + status + '?')) {
        fetch('update_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + id + '&status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Gagal mengubah status');
            }
        });
    }
}
</script>

<?php include_once __DIR__.'/../../includes/footer.php'; ?>