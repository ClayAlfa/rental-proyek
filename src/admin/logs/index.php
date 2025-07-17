<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

// Filter
$action_filter = $_GET['action'] ?? '';
$user_filter = $_GET['user'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$limit = $_GET['limit'] ?? 50;

$sql = "SELECT l.*, u.full_name, u.username FROM logs l JOIN users u ON u.id = l.user_id";
$params = [];
$where = [];

if($action_filter) {
    $where[] = "l.action LIKE ?";
    $params[] = "%{$action_filter}%";
}

if($user_filter) {
    $where[] = "l.user_id = ?";
    $params[] = $user_filter;
}

if($date_from) {
    $where[] = "DATE(l.created_at) >= ?";
    $params[] = $date_from;
}

if($date_to) {
    $where[] = "DATE(l.created_at) <= ?";
    $params[] = $date_to;
}

if($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY l.created_at DESC LIMIT ?";
$params[] = (int)$limit;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Ambil daftar user untuk filter
$users = $pdo->query("SELECT id, full_name FROM users ORDER BY full_name")->fetchAll();

// Statistik
$stats = $pdo->query("SELECT 
    COUNT(*) as total_logs,
    COUNT(DISTINCT user_id) as active_users,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_logs
    FROM logs")->fetch();

include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Log Aktivitas Sistem</h2>
        <div>
            <button class="btn btn-danger" onclick="clearLogs()">🗑️ Hapus Log Lama</button>
            <a href="../dashboard.php" class="btn btn-outline-secondary">← Kembali</a>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Log</h5>
                    <h3><?= number_format($stats['total_logs']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>User Aktif</h5>
                    <h3><?= number_format($stats['active_users']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Log Hari Ini</h5>
                    <h3><?= number_format($stats['today_logs']) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Aksi</label>
                    <select name="action" class="form-select">
                        <option value="">Semua Aksi</option>
                        <option value="login" <?= $action_filter == 'login' ? 'selected' : '' ?>>Login</option>
                        <option value="logout" <?= $action_filter == 'logout' ? 'selected' : '' ?>>Logout</option>
                        <option value="create" <?= $action_filter == 'create' ? 'selected' : '' ?>>Create</option>
                        <option value="update" <?= $action_filter == 'update' ? 'selected' : '' ?>>Update</option>
                        <option value="delete" <?= $action_filter == 'delete' ? 'selected' : '' ?>>Delete</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user" class="form-select">
                        <option value="">Semua User</option>
                        <?php foreach($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $user_filter == $user['id'] ? 'selected' : '' ?>>
                                <?= h($user['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="<?= h($date_from) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="<?= h($date_to) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Limit</label>
                    <select name="limit" class="form-select">
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                        <option value="200" <?= $limit == 200 ? 'selected' : '' ?>>200</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tabel Log -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Deskripsi</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="text-muted">Tidak ada log aktivitas</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $i => $log): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-bold"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                            <small class="text-muted"><?= date('H:i:s', strtotime($log['created_at'])) ?></small>
                        </td>
                        <td>
                            <div class="fw-bold"><?= h($log['full_name']) ?></div>
                            <small class="text-muted">@<?= h($log['username']) ?></small>
                        </td>
                        <td>
                            <?php 
                            $actionClass = match(true) {
                                str_contains($log['action'], 'login') => 'bg-success',
                                str_contains($log['action'], 'logout') => 'bg-secondary',
                                str_contains($log['action'], 'create') => 'bg-primary',
                                str_contains($log['action'], 'update') => 'bg-warning',
                                str_contains($log['action'], 'delete') => 'bg-danger',
                                default => 'bg-info'
                            };
                            ?>
                            <span class="badge <?= $actionClass ?>"><?= h($log['action']) ?></span>
                        </td>
                        <td><?= h($log['description']) ?></td>
                        <td>
                            <small class="text-muted"><?= h($log['ip_address'] ?? 'N/A') ?></small>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function clearLogs() {
    if(confirm('Hapus semua log yang lebih dari 30 hari? Aksi ini tidak dapat dibatalkan.')) {
        fetch('clear_logs.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Log lama berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus log: ' + data.message);
            }
        });
    }
}
</script>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>
