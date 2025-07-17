<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_login();
require_admin();

$error = '';
$success = '';

/* Ambil data lama */
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('index.php?error=invalid_id');
}

$stmt = $pdo->prepare("SELECT * FROM vehicle_types WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    redirect('index.php?error=not_found');
}

/* Proses update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_name = trim($_POST['type_name'] ?? '');
    
    // Validasi
    if (empty($type_name)) {
        $error = 'Nama tipe kendaraan harus diisi';
    } elseif (strlen($type_name) < 2) {
        $error = 'Nama tipe kendaraan minimal 2 karakter';
    } elseif (strlen($type_name) > 50) {
        $error = 'Nama tipe kendaraan maksimal 50 karakter';
    } else {
        // Cek duplikasi (kecuali data sendiri)
        $stmt = $pdo->prepare("SELECT id FROM vehicle_types WHERE type_name = ? AND id != ?");
        $stmt->execute([$type_name, $id]);
        if ($stmt->fetch()) {
            $error = 'Tipe kendaraan sudah ada';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE vehicle_types SET type_name = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$type_name, $id]);
                
                // Log aktivitas
                log_activity($_SESSION['user_id'], "Mengubah tipe kendaraan: {$data['type_name']} menjadi $type_name");
                
                redirect('index.php?success=updated');
            } catch (Exception $e) {
                $error = 'Gagal mengupdate data: ' . $e->getMessage();
            }
        }
    }
}

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Edit Jenis Kendaraan</h2>
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
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Form Edit Tipe Kendaraan
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Tipe Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="type_name" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['type_name'] ?? $data['type_name']) ?>"
                                   placeholder="Contoh: Mobil, Motor, Sepeda" 
                                   required maxlength="50">
                            <div class="form-text">Minimal 2 karakter, maksimal 50 karakter</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-gradient">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Data
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><?= $data['id'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nama Saat Ini:</strong></td>
                            <td><?= htmlspecialchars($data['type_name']) ?></td>
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
                    
                    <hr>
                    <p class="mb-2"><strong>Petunjuk:</strong></p>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-check text-success me-2"></i>Nama tipe harus unik</li>
                        <li><i class="fas fa-check text-success me-2"></i>Minimal 2 karakter</li>
                        <li><i class="fas fa-check text-success me-2"></i>Maksimal 50 karakter</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>