<?php
$pageTitle = 'Data Pengguna - Admin SwaraNusa Quiz';
$activePage = 'users';

$users = [
  ['name' => 'Ayu Lestari', 'email' => 'ayu.lestari@example.com', 'role' => 'user', 'score' => 1280, 'status' => 'aktif', 'registeredAt' => '2026-05-01'],
  ['name' => 'Bima Saputra', 'email' => 'bima.saputra@example.com', 'role' => 'user', 'score' => 980, 'status' => 'aktif', 'registeredAt' => '2026-05-02'],
  ['name' => 'Citra Dewi', 'email' => 'citra.dewi@example.com', 'role' => 'admin', 'score' => 0, 'status' => 'aktif', 'registeredAt' => '2026-05-03'],
  ['name' => 'Dimas Pratama', 'email' => 'dimas.pratama@example.com', 'role' => 'user', 'score' => 760, 'status' => 'nonaktif', 'registeredAt' => '2026-05-04'],
  ['name' => 'Eka Putri', 'email' => 'eka.putri@example.com', 'role' => 'user', 'score' => 1510, 'status' => 'aktif', 'registeredAt' => '2026-05-05'],
  ['name' => 'Farhan Akbar', 'email' => 'farhan.akbar@example.com', 'role' => 'user', 'score' => 430, 'status' => 'aktif', 'registeredAt' => '2026-05-06'],
  ['name' => 'Gita Maharani', 'email' => 'gita.maharani@example.com', 'role' => 'admin', 'score' => 0, 'status' => 'aktif', 'registeredAt' => '2026-05-07'],
  ['name' => 'Hendra Wijaya', 'email' => 'hendra.wijaya@example.com', 'role' => 'user', 'score' => 890, 'status' => 'nonaktif', 'registeredAt' => '2026-05-08'],
  ['name' => 'Intan Permata', 'email' => 'intan.permata@example.com', 'role' => 'user', 'score' => 1120, 'status' => 'aktif', 'registeredAt' => '2026-05-09'],
  ['name' => 'Joko Santoso', 'email' => 'joko.santoso@example.com', 'role' => 'user', 'score' => 650, 'status' => 'aktif', 'registeredAt' => '2026-05-10'],
  ['name' => 'Kartika Sari', 'email' => 'kartika.sari@example.com', 'role' => 'user', 'score' => 1740, 'status' => 'aktif', 'registeredAt' => '2026-05-11'],
  ['name' => 'Lukman Hakim', 'email' => 'lukman.hakim@example.com', 'role' => 'user', 'score' => 540, 'status' => 'nonaktif', 'registeredAt' => '2026-05-12'],
  ['name' => 'Maya Anggraini', 'email' => 'maya.anggraini@example.com', 'role' => 'user', 'score' => 1360, 'status' => 'aktif', 'registeredAt' => '2026-05-13'],
  ['name' => 'Nanda Putra', 'email' => 'nanda.putra@example.com', 'role' => 'admin', 'score' => 0, 'status' => 'nonaktif', 'registeredAt' => '2026-05-14'],
  ['name' => 'Oki Ramadhan', 'email' => 'oki.ramadhan@example.com', 'role' => 'user', 'score' => 1020, 'status' => 'aktif', 'registeredAt' => '2026-05-15'],
];

function userBadgeClass($type, $value)
{
  if ($type === 'role') {
    return $value === 'admin' ? 'primary' : 'secondary';
  }

  return $value === 'aktif' ? 'success' : 'warning';
}

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Pengguna</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Filter Data Pengguna</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="form-group">
                <label for="filterRole">Role</label>
                <select id="filterRole" class="form-control">
                  <option value="">Semua Role</option>
                  <option value="user">user</option>
                  <option value="admin">admin</option>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-sm-6">
              <div class="form-group">
                <label for="filterUserStatus">Status</label>
                <select id="filterUserStatus" class="form-control">
                  <option value="">Semua Status</option>
                  <option value="aktif">aktif</option>
                  <option value="nonaktif">nonaktif</option>
                </select>
              </div>
            </div>
            <div class="col-md-4 col-sm-12">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" id="resetUserFilter" class="btn btn-secondary btn-block">
                  <i class="fas fa-undo mr-1"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar Pengguna</h3>
        </div>
        <div class="card-body">
          <table id="usersTable" class="table table-bordered table-striped table-hover dt-responsive nowrap" style="width: 100%;">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Total Skor</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $index => $user): ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td><?= $user['name']; ?></td>
                  <td><?= $user['email']; ?></td>
                  <td>
                    <span class="badge badge-<?= userBadgeClass('role', $user['role']); ?>">
                      <?= $user['role']; ?>
                    </span>
                  </td>
                  <td><?= number_format($user['score']); ?></td>
                  <td>
                    <span class="badge badge-<?= userBadgeClass('status', $user['status']); ?>">
                      <?= $user['status']; ?>
                    </span>
                  </td>
                  <td><?= $user['registeredAt']; ?></td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-info" title="Detail">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button type="button" class="btn btn-warning" title="Ubah role atau status">
                        <i class="fas fa-user-cog"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
