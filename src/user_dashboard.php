<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/auth.php';
require_once __DIR__.'/includes/functions.php';

require_login();

// Pastikan hanya user biasa yang bisa akses
if (is_admin()) {
    redirect('admin/dashboard.php');
}

$user_id = $_SESSION['user']['id'];

// Ambil data rental user
$stmt = $pdo->prepare("
    SELECT r.*, v.brand, v.model, v.image, v.price_per_day
    FROM rentals r
    JOIN vehicles v ON v.id = r.vehicle_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$user_rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil kendaraan populer
$popular_vehicles = $pdo->query("
    SELECT v.*, t.type_name, COUNT(r.id) as rental_count
    FROM vehicles v
    LEFT JOIN vehicle_types t ON t.id = v.type_id
    LEFT JOIN rentals r ON r.vehicle_id = v.id
    WHERE v.status = 'Tersedia'
    GROUP BY v.id
    ORDER BY rental_count DESC, v.created_at DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

// Statistik user
$total_rentals = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE user_id = ?");
$total_rentals->execute([$user_id]);
$total_rentals = $total_rentals->fetchColumn();

$active_rentals = $pdo->prepare("SELECT COUNT(*) FROM rentals WHERE user_id = ? AND status IN ('Dipesan', 'Ongoing')");
$active_rentals->execute([$user_id]);
$active_rentals = $active_rentals->fetchColumn();

include_once __DIR__.'/includes/header.php';
?>

<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0 shadow">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-2">Selamat Datang, <?= h($_SESSION['user']['name']); ?>! 👋</h2>
                            <p class="mb-0 opacity-75">Kelola penyewaan kendaraan Anda dengan mudah dan nikmati perjalanan terbaik.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="vehicles.php" class="btn btn-light btn-lg fw-semibold">
                                <i class="bi bi-car-front me-2"></i>Sewa Kendaraan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-receipt text-primary fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1"><?= $total_rentals; ?></h3>
                        <p class="text-muted mb-0">Total Penyewaan</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-clock text-success fs-4"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1"><?= $active_rentals; ?></h3>
                        <p class="text-muted mb-0">Penyewaan Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Rental History -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Riwayat Penyewaan Terbaru</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($user_rentals)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada riwayat penyewaan</p>
                            <a href="vehicles.php" class="btn btn-primary">Mulai Sewa Sekarang</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($user_rentals as $rental): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <img src="<?= h($rental['image'] ?: 'https://placehold.co/80x60'); ?>" 
                                                 class="rounded" width="60" height="45" style="object-fit: cover;">
                                        </div>
                                        <div class="col">
                                            <h6 class="mb-1"><?= h($rental['brand'] . ' ' . $rental['model']); ?></h6>
                                            <small class="text-muted">
                                                <?= date('d M Y', strtotime($rental['rent_date'])); ?> - 
                                                <?= date('d M Y', strtotime($rental['return_date'])); ?>
                                            </small>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-<?= $rental['status'] === 'Selesai' ? 'success' : ($rental['status'] === 'Ongoing' ? 'warning' : 'primary'); ?>">
                                                <?= h($rental['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Popular Vehicles -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Kendaraan Populer</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach (array_slice($popular_vehicles, 0, 4) as $vehicle): ?>
                            <div class="col-6">
                                <div class="card border-0 bg-light h-100">
                                    <img src="<?= h($vehicle['image'] ?: 'https://placehold.co/200x120'); ?>" 
                                         class="card-img-top" style="height: 80px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1 small"><?= h($vehicle['brand'] . ' ' . $vehicle['model']); ?></h6>
                                        <p class="text-primary fw-bold small mb-1">
                                            <?= rupiah($vehicle['price_per_day']); ?>/hari
                                        </p>
                                        <a href="booking.php?vid=<?= $vehicle['id']; ?>" class="btn btn-primary btn-sm w-100">
                                            Sewa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="vehicles.php" class="btn btn-outline-primary btn-sm">Lihat Semua Kendaraan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<?php include_once __DIR__.'/includes/footer.php'; ?>