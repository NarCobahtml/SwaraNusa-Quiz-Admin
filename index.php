<?php
$pageTitle = 'Dashboard - Admin SwaraNusa Quiz';
$activePage = 'dashboard';

$stats = [
  'totalQuestions' => 128,
  'activeQuestions' => 112,
  'totalUsers' => 864,
  'totalRewards' => 14,
];

$questionsByMode = [
  [
    'mode' => 'Tebak Gambar',
    'total' => 48,
    'active' => 43,
    'icon' => 'fas fa-image',
    'color' => 'info',
  ],
  [
    'mode' => 'Tebak Suara',
    'total' => 42,
    'active' => 37,
    'icon' => 'fas fa-volume-up',
    'color' => 'success',
  ],
  [
    'mode' => 'Sejarah Alat Musik',
    'total' => 38,
    'active' => 32,
    'icon' => 'fas fa-landmark',
    'color' => 'warning',
  ],
];

$latestQuestions = [
  [
    'mode' => 'Tebak Gambar',
    'level' => 'Level 3',
    'number' => 12,
    'question' => 'Alat musik tradisional apakah yang ditampilkan pada gambar?',
    'status' => 'Aktif',
  ],
  [
    'mode' => 'Tebak Suara',
    'level' => 'Level 2',
    'number' => 8,
    'question' => 'Dengarkan audio berikut, lalu pilih nama alat musik yang benar.',
    'status' => 'Aktif',
  ],
  [
    'mode' => 'Sejarah Alat Musik',
    'level' => 'Level 4',
    'number' => 6,
    'question' => 'Dari daerah manakah alat musik Sasando berasal?',
    'status' => 'Aktif',
  ],
  [
    'mode' => 'Tebak Gambar',
    'level' => 'Level 1',
    'number' => 3,
    'question' => 'Pilih jawaban yang sesuai dengan gambar alat musik berikut.',
    'status' => 'Nonaktif',
  ],
  [
    'mode' => 'Tebak Suara',
    'level' => 'Level 5',
    'number' => 15,
    'question' => 'Suara alat musik pada soal ini dimainkan dengan cara apa?',
    'status' => 'Draft',
  ],
];

$dataWarnings = [
  [
    'label' => 'Soal tanpa mediaUrl',
    'count' => 7,
    'description' => 'Perlu dilengkapi agar soal gambar atau suara dapat tampil di aplikasi.',
    'color' => 'danger',
    'icon' => 'fas fa-photo-video',
  ],
  [
    'label' => 'Soal tanpa correctAnswer',
    'count' => 3,
    'description' => 'Soal belum bisa divalidasi karena jawaban benar masih kosong.',
    'color' => 'danger',
    'icon' => 'fas fa-check-circle',
  ],
  [
    'label' => 'Soal nonaktif',
    'count' => 16,
    'description' => 'Soal tersimpan tetapi belum tersedia untuk pemain.',
    'color' => 'warning',
    'icon' => 'fas fa-pause-circle',
  ],
  [
    'label' => 'Soal tanpa levelId/modeId',
    'count' => 5,
    'description' => 'Data relasi level atau mode perlu diperbaiki sebelum dipakai.',
    'color' => 'warning',
    'icon' => 'fas fa-link',
  ],
];

$leaderboardUsers = [
  [
    'name' => 'Kartika Sari',
    'email' => 'kartika.sari@example.com',
    'score' => 1740,
    'exp' => 9200,
    'level' => 10,
    'status' => 'aktif',
  ],
  [
    'name' => 'Eka Putri',
    'email' => 'eka.putri@example.com',
    'score' => 1510,
    'exp' => 8200,
    'level' => 9,
    'status' => 'aktif',
  ],
  [
    'name' => 'Maya Anggraini',
    'email' => 'maya.anggraini@example.com',
    'score' => 1360,
    'exp' => 7600,
    'level' => 8,
    'status' => 'aktif',
  ],
  [
    'name' => 'Ayu Lestari',
    'email' => 'ayu.lestari@example.com',
    'score' => 1280,
    'exp' => 7050,
    'level' => 8,
    'status' => 'aktif',
  ],
  [
    'name' => 'Intan Permata',
    'email' => 'intan.permata@example.com',
    'score' => 1120,
    'exp' => 6100,
    'level' => 7,
    'status' => 'aktif',
  ],
  [
    'name' => 'Oki Ramadhan',
    'email' => 'oki.ramadhan@example.com',
    'score' => 1020,
    'exp' => 5650,
    'level' => 6,
    'status' => 'aktif',
  ],
];

usort($leaderboardUsers, function ($firstUser, $secondUser) {
  if ($firstUser['score'] === $secondUser['score']) {
    return $secondUser['exp'] <=> $firstUser['exp'];
  }

  return $secondUser['score'] <=> $firstUser['score'];
});

$modeChartLabels = array_column($questionsByMode, 'mode');
$modeChartTotals = array_column($questionsByMode, 'total');
$activeQuestionTotal = $stats['activeQuestions'];
$inactiveQuestionTotal = $stats['totalQuestions'] - $stats['activeQuestions'];

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Dashboard</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right">
            <a href="quiz-create.php" class="btn btn-primary">
              <i class="fas fa-plus mr-1"></i> Tambah Soal
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-question-circle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Soal</span>
              <span class="info-box-number" data-stat="totalQuestions"><?= number_format($stats['totalQuestions']); ?></span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="info-box">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-toggle-on"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Soal Aktif</span>
              <span class="info-box-number" data-stat="activeQuestions"><?= number_format($stats['activeQuestions']); ?></span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="info-box">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Pengguna</span>
              <span class="info-box-number" data-stat="totalUsers"><?= number_format($stats['totalUsers']); ?></span>
            </div>
          </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
          <div class="info-box">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-gift"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Hadiah</span>
              <span class="info-box-number" data-stat="totalRewards"><?= number_format($stats['totalRewards']); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Jumlah Soal per Mode</h3>
            </div>
            <div class="card-body">
              <div class="chart">
                <canvas id="questionsByModeChart" style="min-height: 280px; height: 280px; max-height: 280px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Soal Aktif vs Nonaktif</h3>
            </div>
            <div class="card-body">
              <div class="chart">
                <canvas id="questionStatusChart" style="min-height: 280px; height: 280px; max-height: 280px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Leaderboard Pengguna</h3>
              <div class="card-tools">
                <a href="users.php" class="btn btn-tool">
                  <i class="fas fa-users mr-1"></i> Lihat Data Pengguna
                </a>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap mb-0">
                <thead>
                  <tr>
                    <th style="width: 70px;">Rank</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th class="text-right">Skor</th>
                    <th class="text-right">EXP</th>
                    <th>Level</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody data-leaderboard-users>
                  <?php foreach ($leaderboardUsers as $index => $user): ?>
                    <?php
                      $rank = $index + 1;
                      $rankClass = 'secondary';
                      if ($rank === 1) {
                        $rankClass = 'warning';
                      } elseif ($rank === 2) {
                        $rankClass = 'info';
                      } elseif ($rank === 3) {
                        $rankClass = 'success';
                      }
                    ?>
                    <tr>
                      <td><span class="badge badge-<?= $rankClass; ?>">#<?= $rank; ?></span></td>
                      <td><?= $user['name']; ?></td>
                      <td><?= $user['email']; ?></td>
                      <td class="text-right"><strong><?= number_format($user['score']); ?></strong></td>
                      <td class="text-right"><?= number_format($user['exp']); ?></td>
                      <td><span class="badge badge-primary">Level <?= $user['level']; ?></span></td>
                      <td><span class="badge badge-success"><?= $user['status']; ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Jumlah Soal per Mode</h3>
            </div>
            <div class="card-body p-0">
              <ul class="products-list product-list-in-card pl-2 pr-2" data-mode-list>
                <?php foreach ($questionsByMode as $mode): ?>
                  <li class="item">
                    <div class="product-img">
                      <span class="btn btn-<?= $mode['color']; ?> btn-sm">
                        <i class="<?= $mode['icon']; ?>"></i>
                      </span>
                    </div>
                    <div class="product-info">
                      <span class="product-title">
                        <?= $mode['mode']; ?>
                        <span class="badge badge-<?= $mode['color']; ?> float-right"><?= $mode['total']; ?> soal</span>
                      </span>
                      <span class="product-description">
                        <?= $mode['active']; ?> soal aktif dari <?= $mode['total']; ?> soal
                      </span>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="card card-warning card-outline">
            <div class="card-header">
              <h3 class="card-title">Peringatan Data Bermasalah</h3>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                  <thead>
                    <tr>
                      <th>Masalah</th>
                      <th class="text-center" style="width: 90px;">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody data-warning-table>
                    <?php foreach ($dataWarnings as $warning): ?>
                      <tr>
                        <td>
                          <i class="<?= $warning['icon']; ?> text-<?= $warning['color']; ?> mr-1"></i>
                          <strong><?= $warning['label']; ?></strong>
                          <div class="text-muted small"><?= $warning['description']; ?></div>
                        </td>
                        <td class="text-center align-middle">
                          <span class="badge badge-<?= $warning['color']; ?>"><?= $warning['count']; ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Soal Terbaru</h3>
              <div class="card-tools">
                <a href="quiz.php" class="btn btn-tool">
                  <i class="fas fa-list mr-1"></i> Lihat Data Kuis
                </a>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap mb-0">
                <thead>
                  <tr>
                    <th>Mode</th>
                    <th>Level</th>
                    <th>No.</th>
                    <th>Pertanyaan</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody data-latest-questions>
                  <?php foreach ($latestQuestions as $question): ?>
                    <?php
                      $statusClass = 'secondary';
                      if ($question['status'] === 'Aktif') {
                        $statusClass = 'success';
                      } elseif ($question['status'] === 'Nonaktif') {
                        $statusClass = 'warning';
                      }
                    ?>
                    <tr>
                      <td><?= $question['mode']; ?></td>
                      <td><?= $question['level']; ?></td>
                      <td><?= $question['number']; ?></td>
                      <td class="text-wrap" style="min-width: 260px;"><?= $question['question']; ?></td>
                      <td><span class="badge badge-<?= $statusClass; ?>"><?= $question['status']; ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script src="node_modules/chart.js/dist/Chart.bundle.min.js"></script>
<script>
  var modeChartContext = document.getElementById('questionsByModeChart').getContext('2d');
  var statusChartContext = document.getElementById('questionStatusChart').getContext('2d');

  window.questionsByModeChart = new Chart(modeChartContext, {
    type: 'bar',
    data: {
      labels: <?= json_encode($modeChartLabels); ?>,
      datasets: [{
        label: 'Jumlah Soal',
        backgroundColor: ['#17a2b8', '#28a745', '#ffc107'],
        borderColor: ['#138496', '#218838', '#d39e00'],
        borderWidth: 1,
        data: <?= json_encode($modeChartTotals); ?>
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            precision: 0
          }
        }]
      }
    }
  });

  window.questionStatusChart = new Chart(statusChartContext, {
    type: 'doughnut',
    data: {
      labels: ['Aktif', 'Nonaktif'],
      datasets: [{
        data: [<?= $activeQuestionTotal; ?>, <?= $inactiveQuestionTotal; ?>],
        backgroundColor: ['#28a745', '#ffc107'],
        borderColor: ['#ffffff', '#ffffff'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'bottom'
      }
    }
  });
</script>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
