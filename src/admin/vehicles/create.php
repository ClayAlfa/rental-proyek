<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

/* Ambil semua jenis kendaraan */
$types = $pdo->query("SELECT * FROM vehicle_types ORDER BY type_name")->fetchAll();
$error = '';

/* Proses simpan */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $type_id = $_POST['type_id'];
    $price_per_day = $_POST['price_per_day'];
    $seats = $_POST['seats'];
    $transmission = $_POST['transmission'];
    $fuel = $_POST['fuel'];
    $image = trim($_POST['image']);
    $status = $_POST['status'] ?? 'Tersedia';
    
    // Validasi
    if(empty($brand) || empty($model)) {
        $error = 'Brand dan Model harus diisi';
    } elseif($price_per_day <= 0) {
        $error = 'Harga harus lebih dari 0';
    } elseif($seats <= 0) {
        $error = 'Jumlah kursi harus lebih dari 0';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO vehicles
             (brand, model, type_id, price_per_day, seats, transmission, fuel, image, status)
             VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([$brand, $model, $type_id, $price_per_day, $seats, $transmission, $fuel, $image, $status]);
        redirect('index.php');
    }
}

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Tambah Kendaraan</h2>
        <a href="index.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kendaraan *</label>
                        <select name="type_id" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= ($_POST['type_id'] ?? '') == $t['id'] ? 'selected' : '' ?>>
                                    <?= h($t['type_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand *</label>
                        <input name="brand" type="text" class="form-control" value="<?= h($_POST['brand'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Model *</label>
                        <input name="model" type="text" class="form-control" value="<?= h($_POST['model'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga per Hari (Rp) *</label>
                        <input name="price_per_day" type="number" class="form-control" min="1" value="<?= h($_POST['price_per_day'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Kursi *</label>
                        <input name="seats" type="number" class="form-control" min="1" max="20" value="<?= h($_POST['seats'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Transmisi *</label>
                        <select name="transmission" class="form-select" required>
                            <option value="Manual" <?= ($_POST['transmission'] ?? '') == 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="Automatic" <?= ($_POST['transmission'] ?? '') == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Bahan Bakar *</label>
                        <select name="fuel" class="form-select" required>
                            <option value="Bensin" <?= ($_POST['fuel'] ?? '') == 'Bensin' ? 'selected' : '' ?>>Bensin</option>
                            <option value="Diesel" <?= ($_POST['fuel'] ?? '') == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                            <option value="Listrik" <?= ($_POST['fuel'] ?? '') == 'Listrik' ? 'selected' : '' ?>>Listrik</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">URL Gambar</label>
                        <input name="image" type="url" class="form-control" value="<?= h($_POST['image'] ?? '') ?>" placeholder="https://example.com/image.jpg">
                        <div class="form-text">Kosongkan untuk menggunakan gambar default</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="Tersedia" <?= ($_POST['status'] ?? 'Tersedia') == 'Tersedia' ? 'selected' : '' ?>>Tersedia</option>
                            <option value="Servis" <?= ($_POST['status'] ?? '') == 'Servis' ? 'selected' : '' ?>>Servis</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-gradient">Simpan</button>
                    <a href="index.php" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>