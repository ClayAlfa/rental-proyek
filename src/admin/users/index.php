<?php
require_once __DIR__.'/../../includes/db.php';
require_once __DIR__.'/../../includes/auth.php';
require_once __DIR__.'/../../includes/functions.php';

require_admin();

$users = $pdo->query("SELECT u.*, r.role_name FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.created_at DESC")->fetchAll();
include_once __DIR__.'/../../includes/header.php';
?>
<div class="container-fluid py-4">
  <h2 class="fw-bold mb-3">Pengguna</h2>
  
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= h($_SESSION['success_message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>
  
  <a href="create.php" class="btn btn-gradient mb-3">+ Tambah</a>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Terdaftar</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($users as $i=>$u): ?>
        <tr>
          <td><?=$i+1;?></td>
          <td><?=h($u['username']);?></td>
          <td><?=h($u['full_name']);?></td>
          <td><?=h($u['email'] ?: '-');?></td>
          <td>
            <span class="badge <?= $u['role_name'] == 'admin' ? 'bg-danger' : 'bg-primary' ?>">
              <?=h($u['role_name']);?>
            </span>
          </td>
          <td><?=date('d/m/Y', strtotime($u['created_at']));?></td>
          <td class="text-end">
            <a href="edit.php?id=<?=$u['id'];?>" class="btn btn-sm btn-warning">Edit</a>
            <?php if($u['id'] != 1): // Jangan tampilkan tombol hapus untuk admin utama ?>
              <a href="delete.php?id=<?=$u['id'];?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user <?=h($u['username'])?>?')">Hapus</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once __DIR__.'/../../includes/footer.php'; ?>