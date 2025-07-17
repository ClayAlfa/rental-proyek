<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

// Filter pencarian
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';

$sql = "SELECT v.*, t.type_name FROM vehicles v JOIN vehicle_types t ON t.id=v.type_id";
$params = [];
$where = [];

if($search) {
    $where[] = "(v.brand LIKE ? OR v.model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if($type_filter) {
    $where[] = "v.type_id = ?";
    $params[] = $type_filter;
}

if($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY v.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vehicles = $stmt->fetchAll();

// Ambil semua tipe untuk filter
$types = $pdo->query("SELECT * FROM vehicle_types ORDER BY type_name")->fetchAll();
include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manajemen Kendaraan</h2>
        <a href="create.php" class="btn btn-gradient">+ Tambah Kendaraan</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Kendaraan berhasil ditambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] == 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Kendaraan berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Kendaraan berhasil dihapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] == 'invalid_id'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>ID tidak valid!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'not_found'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Kendaraan tidak ditemukan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'vehicle_rented'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Tidak dapat menghapus kendaraan yang sedang disewa!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'has_active_rentals'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Tidak dapat menghapus kendaraan karena masih ada <?= intval($_GET['count'] ?? 0) ?> rental aktif!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'delete_failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Gagal menghapus kendaraan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Filter dan Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari brand atau model..." value="<?= h($search) ?>">
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select">
                        <option value="">Semua Jenis</option>
                        <?php foreach($types as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $type_filter == $t['id'] ? 'selected' : '' ?>>
                                <?= h($t['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Kendaraan</th>
                    <th>Tipe</th>
                    <th>Spesifikasi</th>
                    <th>Harga/Hari</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($vehicles)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="text-muted">
                                <?= $search || $type_filter ? 'Tidak ada kendaraan yang sesuai dengan filter' : 'Belum ada kendaraan' ?>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($vehicles as $i=>$k): ?>
                    <tr>
                        <td><?=$i+1;?></td>
                        <td>
                            <img src="<?=h($k['image']?:'https://placehold.co/80x60?text=No+Image');?>" 
                                 width="60" height="45" 
                                 style="border-radius:8px; object-fit:cover;" 
                                 alt="<?=h($k['brand'].' '.$k['model'])?>">
                        </td>
                        <td>
                            <div class="fw-bold"><?=h($k['brand']);?></div>
                            <small class="text-muted"><?=h($k['model']);?></small>
                        </td>
                        <td>
                            <span class="badge bg-info"><?=h($k['type_name']);?></span>
                        </td>
                        <td>
                            <small>
                                <?=h($k['seats']);?> kursi • <?=h($k['transmission']);?><br>
                                <?=h($k['fuel']);?>
                            </small>
                        </td>
                        <td class="fw-bold text-success"><?=rupiah($k['price_per_day']);?></td>
                        <td>
                            <?php 
                            $statusClass = match($k['status']) {
                                'Tersedia' => 'bg-success',
                                'Disewa' => 'bg-warning',
                                'Servis' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?=$statusClass?>"><?=h($k['status']);?></span>
                        </td>
                        <td class="text-end">
                            <a href="edit.php?id=<?=$k['id'];?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?=$k['id'];?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Yakin ingin menghapus <?=h($k['brand'].' '.$k['model'])?>?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php include_once __DIR__.'/../../includes/footer.php'; ?>