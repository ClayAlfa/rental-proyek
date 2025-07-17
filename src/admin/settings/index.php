<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

$success = '';
$error = '';

// Ambil data pengaturan (hanya satu baris)
$setting = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $app_name = trim($_POST['app_name'] ?? '');
        $organization = trim($_POST['organization'] ?? '');
        $year = trim($_POST['year'] ?? '');
        $contact_email = trim($_POST['contact_email'] ?? '');
        $contact_phone = trim($_POST['contact_phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        // Validasi
        if (empty($app_name) || empty($organization) || empty($year)) {
            throw new Exception('Semua field wajib harus diisi');
        }
        
        if (!is_numeric($year) || strlen($year) != 4) {
            throw new Exception('Tahun harus berupa 4 digit angka');
        }
        
        if (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }
        
        $stmt = $pdo->prepare("UPDATE settings SET app_name=?, organization=?, year=?, contact_email=?, contact_phone=?, address=?, updated_at=NOW() WHERE id=?");
        $result = $stmt->execute([
            $app_name,
            $organization,
            $year,
            $contact_email,
            $contact_phone,
            $address,
            $setting['id']
        ]);
        
        if ($result) {
            // Log aktivitas
            $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $_SESSION['user_id'],
                'update_settings',
                'Pengaturan aplikasi diperbarui'
            ]);
            
            $success = 'Pengaturan berhasil disimpan!';
            // Refresh data
            $setting = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception('Gagal menyimpan pengaturan');
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Pengaturan Aplikasi</h2>
        <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali</a>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= h($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= h($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Aplikasi</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Aplikasi *</label>
                                    <input type="text" name="app_name" class="form-control" value="<?= h($setting['app_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Organisasi / Instansi *</label>
                                    <input type="text" name="organization" class="form-control" value="<?= h($setting['organization']) ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun *</label>
                                    <input type="number" name="year" class="form-control" value="<?= h($setting['year']) ?>" min="2020" max="2030" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Kontak</label>
                                    <input type="email" name="contact_email" class="form-control" value="<?= h($setting['contact_email'] ?? '') ?>" placeholder="admin@example.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Telepon Kontak</label>
                                    <input type="tel" name="contact_phone" class="form-control" value="<?= h($setting['contact_phone'] ?? '') ?>" placeholder="+62 xxx xxxx xxxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Alamat lengkap organisasi"><?= h($setting['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
                            <button type="reset" class="btn btn-outline-secondary">🔄 Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Terakhir Diperbarui</small>
                        <div class="fw-bold"><?= $setting['updated_at'] ? date('d/m/Y H:i', strtotime($setting['updated_at'])) : 'Belum pernah' ?></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Versi PHP</small>
                        <div class="fw-bold"><?= PHP_VERSION ?></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Database</small>
                        <div class="fw-bold">MySQL <?= $pdo->query('SELECT VERSION()')->fetchColumn() ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>