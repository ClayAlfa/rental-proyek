<?php
// Debug: Log access
error_log('detail.php accessed with ID: ' . ($_GET['id'] ?? 'none'));

try {
    require_once __DIR__.'/../../includes/db.php';
    require_once __DIR__.'/../../includes/auth.php';
    require_once __DIR__.'/../../includes/functions.php';

    require_admin();

    $rental_id = $_GET['id'] ?? 0;

    if (!$rental_id) {
        echo '<div class="alert alert-danger">ID rental tidak valid</div>';
        exit;
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}

// Get rental detail with all related information
$stmt = $pdo->prepare("
    SELECT 
        r.*, 
        u.full_name, u.username, u.email, u.phone,
        v.brand, v.model, v.image, v.price_per_day, v.seats, v.transmission, v.fuel,
        vt.type_name,
        p.payment_date, p.amount_paid, p.payment_method, p.proof_image
    FROM rentals r
    JOIN users u ON u.id = r.user_id
    JOIN vehicles v ON v.id = r.vehicle_id
    JOIN vehicle_types vt ON vt.id = v.type_id
    LEFT JOIN payments p ON p.rental_id = r.id
    WHERE r.id = ?
");
$stmt->execute([$rental_id]);
$rental = $stmt->fetch();

if (!$rental) {
    echo '<div class="alert alert-danger">Rental tidak ditemukan</div>';
    exit;
}

// Calculate rental duration
$rent_date = new DateTime($rental['rent_date']);
$return_date = new DateTime($rental['return_date']);
$duration = $rent_date->diff($return_date)->days;

// Status badge class
$statusClass = match($rental['status']) {
    'Dipesan' => 'bg-info',
    'Ongoing' => 'bg-warning',
    'Selesai' => 'bg-success',
    'Dibatalkan' => 'bg-danger',
    default => 'bg-secondary'
};
?>

<div class="row">
    <!-- Rental Information -->
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Informasi Penyewaan</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td width="40%">ID Rental</td>
                <td><strong>#<?= str_pad($rental['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span class="badge <?= $statusClass ?>"><?= h($rental['status']) ?></span></td>
            </tr>
            <tr>
                <td>Tanggal Pesan</td>
                <td><?= date('d/m/Y H:i', strtotime($rental['created_at'])) ?></td>
            </tr>
            <tr>
                <td>Tanggal Sewa</td>
                <td><?= date('d/m/Y', strtotime($rental['rent_date'])) ?></td>
            </tr>
            <tr>
                <td>Tanggal Kembali</td>
                <td><?= date('d/m/Y', strtotime($rental['return_date'])) ?></td>
            </tr>
            <tr>
                <td>Durasi</td>
                <td><?= $duration ?> hari</td>
            </tr>
            <tr>
                <td>Total Harga</td>
                <td><strong class="text-success"><?= rupiah($rental['total_price']) ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Customer Information -->
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Informasi Penyewa</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td width="40%">Nama</td>
                <td><strong><?= h($rental['full_name']) ?></strong></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><?= h($rental['username']) ?></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><?= h($rental['email'] ?: '-') ?></td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td><?= h($rental['phone'] ?: '-') ?></td>
            </tr>
        </table>
    </div>
</div>

<hr>

<!-- Vehicle Information -->
<div class="row">
    <div class="col-md-4">
        <img src="<?= h($rental['image']) ?>" alt="<?= h($rental['brand']) ?>" 
             class="img-fluid rounded" style="width: 100%; max-height: 200px; object-fit: cover;">
    </div>
    <div class="col-md-8">
        <h6 class="fw-bold mb-3">Informasi Kendaraan</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td width="30%">Kendaraan</td>
                <td><strong><?= h($rental['brand'] . ' ' . $rental['model']) ?></strong></td>
            </tr>
            <tr>
                <td>Jenis</td>
                <td><?= h($rental['type_name']) ?></td>
            </tr>
            <tr>
                <td>Kapasitas</td>
                <td><?= $rental['seats'] ?> orang</td>
            </tr>
            <tr>
                <td>Transmisi</td>
                <td><?= h($rental['transmission']) ?></td>
            </tr>
            <tr>
                <td>Bahan Bakar</td>
                <td><?= h($rental['fuel']) ?></td>
            </tr>
            <tr>
                <td>Harga per Hari</td>
                <td><?= rupiah($rental['price_per_day']) ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if ($rental['payment_date']): ?>
<hr>
<!-- Payment Information -->
<div class="row">
    <div class="col-12">
        <h6 class="fw-bold mb-3">Informasi Pembayaran</h6>
        <table class="table table-borderless table-sm">
            <tr>
                <td width="20%">Tanggal Bayar</td>
                <td><?= date('d/m/Y H:i', strtotime($rental['payment_date'])) ?></td>
            </tr>
            <tr>
                <td>Jumlah Bayar</td>
                <td><strong class="text-success"><?= rupiah($rental['amount_paid']) ?></strong></td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td><?= h($rental['payment_method']) ?></td>
            </tr>
            <?php if ($rental['proof_image']): ?>
            <tr>
                <td>Bukti Bayar</td>
                <td>
                    <img src="<?= h($rental['proof_image']) ?>" alt="Bukti Pembayaran" 
                         class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                </td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="d-flex justify-content-end gap-2 mt-4">
    <?php if ($rental['status'] == 'Dipesan'): ?>
        <button class="btn btn-success" onclick="updateRentalStatus(<?= $rental['id'] ?>, 'Ongoing')">
            <i class="bi bi-play-circle me-1"></i>Mulai Sewa
        </button>
        <button class="btn btn-danger" onclick="updateRentalStatus(<?= $rental['id'] ?>, 'Dibatalkan')">
            <i class="bi bi-x-circle me-1"></i>Batalkan
        </button>
    <?php elseif ($rental['status'] == 'Ongoing'): ?>
        <button class="btn btn-info" onclick="updateRentalStatus(<?= $rental['id'] ?>, 'Selesai')">
            <i class="bi bi-check-circle me-1"></i>Selesaikan
        </button>
    <?php endif; ?>
    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

<script>
function updateRentalStatus(rentalId, newStatus) {
    if(confirm(`Ubah status menjadi ${newStatus}?`)) {
        fetch('../rentals/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${rentalId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Close modal and refresh parent page
                bootstrap.Modal.getInstance(document.getElementById('rentalDetailModal')).hide();
                window.location.reload();
            } else {
                alert('Gagal mengubah status: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengubah status');
        });
    }
}
</script>
