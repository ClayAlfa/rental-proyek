<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_login();
require_admin();

$error = '';
$success = '';

/* Data lama */
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('index.php?error=invalid_id');
}

$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    redirect('index.php?error=not_found');
}

/* Ambil tipe */
$types = $pdo->query("SELECT * FROM vehicle_types ORDER BY type_name")->fetchAll();

/* Proses update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $type_id = intval($_POST['type_id'] ?? 0);
    $price_per_day = floatval($_POST['price_per_day'] ?? 0);
    $seats = intval($_POST['seats'] ?? 0);
    $transmission = $_POST['transmission'] ?? '';
    $fuel = $_POST['fuel'] ?? '';
    $image = trim($_POST['image'] ?? '');
    $status = $_POST['status'] ?? '';
    
    // Validasi
    if (empty($brand)) {
        $error = 'Brand harus diisi';
    } elseif (empty($model)) {
        $error = 'Model harus diisi';
    } elseif ($type_id <= 0) {
        $error = 'Jenis kendaraan harus dipilih';
    } elseif ($price_per_day <= 0) {
        $error = 'Harga per hari harus lebih dari 0';
    } elseif ($seats <= 0 || $seats > 50) {
        $error = 'Jumlah kursi harus antara 1-50';
    } elseif (!in_array($transmission, ['Manual', 'Automatic'])) {
        $error = 'Transmisi tidak valid';
    } elseif (!in_array($fuel, ['Bensin', 'Diesel', 'Listrik'])) {
        $error = 'Jenis bahan bakar tidak valid';
    } elseif (!in_array($status, ['Tersedia', 'Disewa', 'Servis'])) {
        $error = 'Status tidak valid';
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE vehicles SET brand=?, model=?, type_id=?, price_per_day=?,
                 seats=?, transmission=?, fuel=?, image=?, status=?, updated_at=NOW() WHERE id=?"
            );
            $stmt->execute([
                $brand, $model, $type_id, $price_per_day,
                $seats, $transmission, $fuel, $image, $status, $id
            ]);
            
            // Log aktivitas
            log_activity($_SESSION['user_id'], "Mengubah kendaraan: $brand $model");
            
            redirect('index.php?success=updated');
        } catch (Exception $e) {
            $error = 'Gagal mengupdate data: ' . $e->getMessage();
        }
    }
}

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Kendaraan</h2>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Form Edit Kendaraan
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Kendaraan <span class="text-danger">*</span></label>
                                <select name="type_id" class="form-select" required>
                                    <option value="">Pilih Jenis</option>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= $t['id']==$data['type_id']?'selected':''; ?>>
                                            <?= htmlspecialchars($t['type_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand <span class="text-danger">*</span></label>
                                <input type="text" name="brand" value="<?= htmlspecialchars($_POST['brand'] ?? $data['brand']) ?>" 
                                       class="form-control" placeholder="Contoh: Toyota, Honda" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Model <span class="text-danger">*</span></label>
                                <input type="text" name="model" value="<?= htmlspecialchars($_POST['model'] ?? $data['model']) ?>" 
                                       class="form-control" placeholder="Contoh: Avanza, Civic" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Harga per Hari <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="price_per_day" 
                                           value="<?= htmlspecialchars($_POST['price_per_day'] ?? $data['price_per_day']) ?>" 
                                           class="form-control" min="1" step="1000" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Jumlah Kursi <span class="text-danger">*</span></label>
                                <input type="number" name="seats" 
                                       value="<?= htmlspecialchars($_POST['seats'] ?? $data['seats']) ?>" 
                                       class="form-control" min="1" max="50" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Transmisi <span class="text-danger">*</span></label>
                                <select name="transmission" class="form-select" required>
                                    <option value="Manual" <?= ($data['transmission']=='Manual')?'selected':''; ?>>Manual</option>
                                    <option value="Automatic" <?= ($data['transmission']=='Automatic')?'selected':''; ?>>Automatic</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bahan Bakar <span class="text-danger">*</span></label>
                                <select name="fuel" class="form-select" required>
                                    <option value="Bensin" <?= ($data['fuel']=='Bensin')?'selected':''; ?>>Bensin</option>
                                    <option value="Diesel" <?= ($data['fuel']=='Diesel')?'selected':''; ?>>Diesel</option>
                                    <option value="Listrik" <?= ($data['fuel']=='Listrik')?'selected':''; ?>>Listrik</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">URL Gambar</label>
                                <input type="url" name="image" value="<?= htmlspecialchars($_POST['image'] ?? $data['image']) ?>" 
                                       class="form-control" placeholder="https://example.com/image.jpg">
                                <div class="form-text">Opsional: URL gambar kendaraan</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <?php foreach (['Tersedia','Disewa','Servis'] as $st): ?>
                                        <option value="<?= $st ?>" <?= ($st==$data['status'])?'selected':''; ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="index.php" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-gradient">
                                <i class="fas fa-save me-2"></i>Update Kendaraan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Kendaraan
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['image'])): ?>
                        <div class="text-center mb-3">
                            <img src="<?= htmlspecialchars($data['image']) ?>" 
                                 alt="<?= htmlspecialchars($data['brand'] . ' ' . $data['model']) ?>"
                                 class="img-fluid rounded" style="max-height: 150px;" 
                                 onerror="this.style.display='none'">
                        </div>
                    <?php endif; ?>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><?= $data['id'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Kendaraan:</strong></td>
                            <td><?= htmlspecialchars($data['brand'] . ' ' . $data['model']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status Saat Ini:</strong></td>
                            <td>
                                <span class="badge bg-<?= $data['status']=='Tersedia'?'success':($data['status']=='Disewa'?'warning':'secondary') ?>">
                                    <?= $data['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($data['created_at'] ?? 'now')) ?></td>
                        </tr>
                        <?php if (!empty($data['updated_at'])): ?>
                        <tr>
                            <td><strong>Terakhir Diubah:</strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($data['updated_at'])) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>