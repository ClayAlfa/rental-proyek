<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

// Ambil semua tipe dengan statistik kendaraan
$types = $pdo->query("
    SELECT vt.*, 
           COUNT(v.id) as vehicle_count,
           COUNT(CASE WHEN v.status = 'Tersedia' THEN 1 END) as available_count
    FROM vehicle_types vt 
    LEFT JOIN vehicles v ON v.type_id = vt.id 
    GROUP BY vt.id 
    ORDER BY vt.type_name
")->fetchAll();

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manajemen Jenis Kendaraan</h2>
        <div>
            <a href="create.php" class="btn btn-primary">+ Tambah Jenis</a>
            <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Jenis kendaraan berhasil ditambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] == 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Jenis kendaraan berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['success'] == 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Jenis kendaraan berhasil dihapus!
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
                <i class="fas fa-exclamation-circle me-2"></i>Jenis kendaraan tidak ditemukan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'still_used'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>Tidak dapat menghapus jenis kendaraan karena masih ada <?= intval($_GET['count'] ?? 0) ?> kendaraan yang menggunakan jenis ini!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['error'] == 'delete_failed'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Gagal menghapus jenis kendaraan!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Jenis</th>
                    <th>Total Kendaraan</th>
                    <th>Tersedia</th>
                    <th>Dibuat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($types)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">Belum ada jenis kendaraan</div>
                            <a href="create.php" class="btn btn-primary mt-2">Tambah Jenis Pertama</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($types as $i => $t): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-bold"><?= h($t['type_name']) ?></div>
                            <?php if($t['description']): ?>
                                <small class="text-muted"><?= h($t['description']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= number_format($t['vehicle_count']) ?> unit</span>
                        </td>
                        <td>
                            <span class="badge bg-success"><?= number_format($t['available_count']) ?> tersedia</span>
                        </td>
                        <td>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($t['created_at'])) ?></small>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="edit.php?id=<?= $t['id'] ?>" class="btn btn-outline-warning">Edit</a>
                                <?php if($t['vehicle_count'] == 0): ?>
                                    <a href="delete.php?id=<?= $t['id'] ?>" class="btn btn-outline-danger"
                                       onclick="return confirm('Hapus jenis kendaraan ini?')">Hapus</a>
                                <?php else: ?>
                                    <button class="btn btn-outline-danger" disabled title="Tidak dapat dihapus karena masih ada kendaraan">Hapus</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(!empty($types)): ?>
    <div class="mt-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Statistik Jenis Kendaraan</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary"><?= count($types) ?></h4>
                            <small class="text-muted">Total Jenis</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info"><?= array_sum(array_column($types, 'vehicle_count')) ?></h4>
                            <small class="text-muted">Total Kendaraan</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success"><?= array_sum(array_column($types, 'available_count')) ?></h4>
                            <small class="text-muted">Kendaraan Tersedia</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning"><?= array_sum(array_column($types, 'vehicle_count')) - array_sum(array_column($types, 'available_count')) ?></h4>
                            <small class="text-muted">Sedang Disewa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>