<?php
$pageTitle = 'Data Hadiah - Admin SwaraNusa Quiz';
$activePage = 'rewards';

$rewards = [
  [
    'rewardId' => 'reward_drum_basic',
    'name' => 'Drum Digital Basic',
    'type' => 'alat_musik_digital',
    'assetUrl' => '',
    'unlockCondition' => 'Selesaikan Tebak Gambar Level 1',
    'modeId' => 'tebak_gambar',
    'levelId' => 'tebak_gambar_1',
    'levelNumber' => 1,
    'isActive' => true,
    'createdAt' => '2026-05-01',
  ],
  [
    'rewardId' => 'reward_gamelan_digital',
    'name' => 'Gamelan Digital',
    'type' => 'alat_musik_digital',
    'assetUrl' => '',
    'unlockCondition' => 'Skor minimal 80 di Sejarah Level 1',
    'modeId' => 'sejarah',
    'levelId' => 'sejarah_1',
    'levelNumber' => 1,
    'isActive' => true,
    'createdAt' => '2026-05-02',
  ],
  [
    'rewardId' => 'reward_badge_pendengar_hebat',
    'name' => 'Badge Pendengar Hebat',
    'type' => 'badge',
    'assetUrl' => '',
    'unlockCondition' => 'Selesaikan Tebak Suara Level 1',
    'modeId' => 'tebak_suara',
    'levelId' => 'tebak_suara_1',
    'levelNumber' => 1,
    'isActive' => false,
    'createdAt' => '2026-05-03',
  ],
  [
    'rewardId' => 'reward_angklung_gold',
    'name' => 'Angklung Gold Badge',
    'type' => 'badge',
    'assetUrl' => 'node_modules/admin-lte/dist/img/AdminLTELogo.png',
    'unlockCondition' => 'Selesaikan 5 soal Tebak Gambar tanpa salah',
    'modeId' => 'tebak_gambar',
    'levelId' => 'tebak_gambar_3',
    'levelNumber' => 3,
    'isActive' => true,
    'createdAt' => '2026-05-04',
  ],
  [
    'rewardId' => 'reward_tifa_item',
    'name' => 'Tifa Practice Item',
    'type' => 'item',
    'assetUrl' => '',
    'unlockCondition' => 'Selesaikan Tebak Suara Level 4',
    'modeId' => 'tebak_suara',
    'levelId' => 'tebak_suara_4',
    'levelNumber' => 4,
    'isActive' => true,
    'createdAt' => '2026-05-05',
  ],
  [
    'rewardId' => 'reward_kolintang_story',
    'name' => 'Cerita Kolintang',
    'type' => 'lainnya',
    'assetUrl' => '',
    'unlockCondition' => 'Skor minimal 90 di Sejarah Level 5',
    'modeId' => 'sejarah',
    'levelId' => 'sejarah_5',
    'levelNumber' => 5,
    'isActive' => true,
    'createdAt' => '2026-05-06',
  ],
  [
    'rewardId' => 'reward_sasando_digital',
    'name' => 'Sasando Digital',
    'type' => 'alat_musik_digital',
    'assetUrl' => '',
    'unlockCondition' => 'Selesaikan Sejarah Alat Musik Level 7',
    'modeId' => 'sejarah',
    'levelId' => 'sejarah_7',
    'levelNumber' => 7,
    'isActive' => false,
    'createdAt' => '2026-05-07',
  ],
  [
    'rewardId' => 'reward_master_suara',
    'name' => 'Badge Master Suara',
    'type' => 'badge',
    'assetUrl' => '',
    'unlockCondition' => 'Selesaikan Tebak Suara Level 10',
    'modeId' => 'tebak_suara',
    'levelId' => 'tebak_suara_10',
    'levelNumber' => 10,
    'isActive' => true,
    'createdAt' => '2026-05-08',
  ],
];

function rewardTypeBadgeClass($type)
{
  return [
    'alat_musik_digital' => 'primary',
    'badge' => 'info',
    'item' => 'success',
    'lainnya' => 'secondary',
  ][$type] ?? 'secondary';
}

function rewardModeLabel($modeId)
{
  return [
    'tebak_gambar' => 'Tebak Gambar',
    'tebak_suara' => 'Tebak Suara',
    'sejarah' => 'Sejarah Alat Musik',
  ][$modeId] ?? 'Semua Mode';
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
          <h1>Data Hadiah</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right">
            <a href="reward-create.php" class="btn btn-primary">
              <i class="fas fa-plus mr-1"></i> Tambah Hadiah
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Filter Data Hadiah</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label for="filterRewardType">Tipe Hadiah</label>
                <select id="filterRewardType" class="form-control">
                  <option value="">Semua Tipe</option>
                  <option value="alat_musik_digital">alat_musik_digital</option>
                  <option value="badge">badge</option>
                  <option value="item">item</option>
                  <option value="lainnya">lainnya</option>
                </select>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label for="filterRewardMode">Mode</label>
                <select id="filterRewardMode" class="form-control">
                  <option value="">Semua Mode</option>
                  <option value="Tebak Gambar">Tebak Gambar</option>
                  <option value="Tebak Suara">Tebak Suara</option>
                  <option value="Sejarah Alat Musik">Sejarah Alat Musik</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group">
                <label for="filterRewardLevel">Level</label>
                <select id="filterRewardLevel" class="form-control">
                  <option value="">Semua Level</option>
                  <?php for ($level = 1; $level <= 10; $level++): ?>
                    <option value="Level <?= $level; ?>">Level <?= $level; ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group">
                <label for="filterRewardStatus">Status</label>
                <select id="filterRewardStatus" class="form-control">
                  <option value="">Semua Status</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Nonaktif">Nonaktif</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" id="resetRewardFilter" class="btn btn-secondary btn-block">
                  <i class="fas fa-undo mr-1"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar Hadiah</h3>
        </div>
        <div class="card-body">
          <table id="rewardsTable" class="table table-bordered table-striped table-hover dt-responsive nowrap" style="width: 100%;">
            <thead>
              <tr>
                <th>No</th>
                <th>Preview</th>
                <th>Nama Hadiah</th>
                <th>Tipe Hadiah</th>
                <th>Syarat Unlock</th>
                <th>Mode Terkait</th>
                <th>Level Terkait</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rewards as $index => $reward): ?>
                <?php
                  $modeLabel = rewardModeLabel($reward['modeId']);
                  $levelLabel = 'Level ' . $reward['levelNumber'];
                  $statusLabel = $reward['isActive'] ? 'Aktif' : 'Nonaktif';
                  $statusClass = $reward['isActive'] ? 'success' : 'warning';
                  $modalId = 'rewardDetailModal' . $index;
                ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td>
                    <?php if ($reward['assetUrl']): ?>
                      <img src="<?= $reward['assetUrl']; ?>" alt="<?= $reward['name']; ?>" class="img-size-50 img-circle elevation-1">
                    <?php else: ?>
                      <span class="btn btn-default btn-sm disabled" style="width: 50px; height: 50px;">
                        <i class="fas fa-gift mt-2"></i>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td><?= $reward['name']; ?></td>
                  <td>
                    <span class="badge badge-<?= rewardTypeBadgeClass($reward['type']); ?>">
                      <?= $reward['type']; ?>
                    </span>
                  </td>
                  <td><?= $reward['unlockCondition']; ?></td>
                  <td><?= $modeLabel; ?></td>
                  <td><?= $levelLabel; ?></td>
                  <td>
                    <span class="badge badge-<?= $statusClass; ?>"><?= $statusLabel; ?></span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#<?= $modalId; ?>" title="Detail">
                        <i class="fas fa-eye"></i>
                      </button>
                      <a href="reward-edit.php?id=<?= urlencode($reward['rewardId']); ?>" class="btn btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                      </a>
                      <button type="button" class="btn btn-danger btn-delete-reward" data-reward-name="<?= $reward['name']; ?>" title="Hapus">
                        <i class="fas fa-trash"></i>
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

<?php foreach ($rewards as $index => $reward): ?>
  <?php
    $modeLabel = rewardModeLabel($reward['modeId']);
    $levelLabel = 'Level ' . $reward['levelNumber'];
    $statusLabel = $reward['isActive'] ? 'Aktif' : 'Nonaktif';
    $statusClass = $reward['isActive'] ? 'success' : 'warning';
    $modalId = 'rewardDetailModal' . $index;
  ?>
  <div class="modal fade" id="<?= $modalId; ?>" tabindex="-1" role="dialog" aria-labelledby="<?= $modalId; ?>Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="<?= $modalId; ?>Label">Detail Hadiah</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-3 text-center mb-3">
              <?php if ($reward['assetUrl']): ?>
                <img src="<?= $reward['assetUrl']; ?>" alt="<?= $reward['name']; ?>" class="img-fluid img-thumbnail">
              <?php else: ?>
                <div class="border rounded p-4 text-muted">
                  <i class="fas fa-gift fa-3x mb-2"></i>
                  <div>Belum ada assetUrl</div>
                </div>
              <?php endif; ?>
            </div>
            <div class="col-md-9">
              <dl class="row mb-0">
                <dt class="col-sm-4">Reward ID</dt>
                <dd class="col-sm-8"><?= $reward['rewardId']; ?></dd>
                <dt class="col-sm-4">Nama Hadiah</dt>
                <dd class="col-sm-8"><?= $reward['name']; ?></dd>
                <dt class="col-sm-4">Tipe</dt>
                <dd class="col-sm-8">
                  <span class="badge badge-<?= rewardTypeBadgeClass($reward['type']); ?>"><?= $reward['type']; ?></span>
                </dd>
                <dt class="col-sm-4">Syarat Unlock</dt>
                <dd class="col-sm-8"><?= $reward['unlockCondition']; ?></dd>
                <dt class="col-sm-4">Mode</dt>
                <dd class="col-sm-8"><?= $modeLabel; ?></dd>
                <dt class="col-sm-4">Level ID</dt>
                <dd class="col-sm-8"><?= $reward['levelId']; ?></dd>
                <dt class="col-sm-4">Level</dt>
                <dd class="col-sm-8"><?= $levelLabel; ?></dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8"><span class="badge badge-<?= $statusClass; ?>"><?= $statusLabel; ?></span></dd>
                <dt class="col-sm-4">Dibuat</dt>
                <dd class="col-sm-8"><?= $reward['createdAt']; ?></dd>
              </dl>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <a href="reward-edit.php?id=<?= urlencode($reward['rewardId']); ?>" class="btn btn-warning">
            <i class="fas fa-edit mr-1"></i> Edit
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
