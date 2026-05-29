<?php
$pageTitle = 'Data Kuis - Admin SwaraNusa Quiz';
$activePage = 'quiz';

$questions = [
  ['mode' => 'Tebak Gambar', 'level' => 'Level 1', 'number' => 1, 'question' => 'Alat musik apakah yang terlihat pada gambar?', 'mediaType' => 'image', 'status' => 'Aktif'],
  ['mode' => 'Tebak Suara', 'level' => 'Level 1', 'number' => 2, 'question' => 'Dengarkan suara berikut, lalu pilih alat musik yang benar.', 'mediaType' => 'audio', 'status' => 'Aktif'],
  ['mode' => 'Sejarah Alat Musik', 'level' => 'Level 2', 'number' => 3, 'question' => 'Dari daerah manakah alat musik Sasando berasal?', 'mediaType' => 'none', 'status' => 'Aktif'],
  ['mode' => 'Tebak Gambar', 'level' => 'Level 2', 'number' => 4, 'question' => 'Pilih nama alat musik tradisional pada gambar ini.', 'mediaType' => 'image', 'status' => 'Nonaktif'],
  ['mode' => 'Tebak Suara', 'level' => 'Level 3', 'number' => 5, 'question' => 'Bunyi alat musik pada audio dimainkan dengan cara apa?', 'mediaType' => 'audio', 'status' => 'Aktif'],
  ['mode' => 'Sejarah Alat Musik', 'level' => 'Level 3', 'number' => 6, 'question' => 'Gamelan banyak berkembang dalam kebudayaan daerah mana?', 'mediaType' => 'none', 'status' => 'Aktif'],
  ['mode' => 'Tebak Gambar', 'level' => 'Level 4', 'number' => 7, 'question' => 'Apakah nama alat musik petik pada gambar berikut?', 'mediaType' => 'image', 'status' => 'Aktif'],
  ['mode' => 'Tebak Suara', 'level' => 'Level 4', 'number' => 8, 'question' => 'Tentukan alat musik berdasarkan potongan suara berikut.', 'mediaType' => 'audio', 'status' => 'Nonaktif'],
  ['mode' => 'Sejarah Alat Musik', 'level' => 'Level 5', 'number' => 9, 'question' => 'Apa fungsi angklung dalam pertunjukan musik tradisional?', 'mediaType' => 'none', 'status' => 'Aktif'],
  ['mode' => 'Tebak Gambar', 'level' => 'Level 5', 'number' => 10, 'question' => 'Alat musik pukul apa yang ditampilkan pada gambar?', 'mediaType' => 'image', 'status' => 'Aktif'],
  ['mode' => 'Tebak Suara', 'level' => 'Level 6', 'number' => 11, 'question' => 'Suara berikut berasal dari alat musik tiup atau pukul?', 'mediaType' => 'audio', 'status' => 'Aktif'],
  ['mode' => 'Sejarah Alat Musik', 'level' => 'Level 6', 'number' => 12, 'question' => 'Siapa yang biasa memainkan alat musik Tifa dalam upacara adat?', 'mediaType' => 'none', 'status' => 'Nonaktif'],
  ['mode' => 'Tebak Gambar', 'level' => 'Level 7', 'number' => 13, 'question' => 'Pilih jawaban yang sesuai dengan bentuk alat musik ini.', 'mediaType' => 'image', 'status' => 'Aktif'],
  ['mode' => 'Tebak Suara', 'level' => 'Level 8', 'number' => 14, 'question' => 'Identifikasi suara alat musik daerah pada audio berikut.', 'mediaType' => 'audio', 'status' => 'Aktif'],
  ['mode' => 'Sejarah Alat Musik', 'level' => 'Level 9', 'number' => 15, 'question' => 'Alat musik Kolintang berasal dari wilayah mana?', 'mediaType' => 'none', 'status' => 'Aktif'],
  ['mode' => 'Tebak Gambar', 'level' => 'Level 10', 'number' => 16, 'question' => 'Alat musik pada gambar biasanya dimainkan dalam ansambel apa?', 'mediaType' => 'image', 'status' => 'Nonaktif'],
];

function quizBadgeClass($type, $value)
{
  if ($type === 'media') {
    return ['image' => 'info', 'audio' => 'success', 'none' => 'secondary'][$value] ?? 'secondary';
  }

  return $value === 'Aktif' ? 'success' : 'warning';
}

function limitWords($value, $maxWords = 5)
{
  $words = preg_split('/\s+/', trim($value));
  if (count($words) <= $maxWords) return $value;

  return implode(' ', array_slice($words, 0, $maxWords)) . '...';
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
          <h1>Data Kuis</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right">
            <a href="quiz-create.php" class="btn btn-primary">
              <i class="fas fa-plus mr-1"></i> Tambah Data
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
          <h3 class="card-title">Filter Data Kuis</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label for="filterMode">Mode</label>
                <select id="filterMode" class="form-control">
                  <option value="">Semua Mode</option>
                  <option value="Tebak Gambar">Tebak Gambar</option>
                  <option value="Tebak Suara">Tebak Suara</option>
                  <option value="Sejarah Alat Musik">Sejarah Alat Musik</option>
                </select>
              </div>
            </div>
            <div class="col-md-3 col-sm-6">
              <div class="form-group">
                <label for="filterLevel">Level</label>
                <select id="filterLevel" class="form-control">
                  <option value="">Semua Level</option>
                  <?php for ($level = 1; $level <= 10; $level++): ?>
                    <option value="Level <?= $level; ?>">Level <?= $level; ?></option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group">
                <label for="filterStatus">Status</label>
                <select id="filterStatus" class="form-control">
                  <option value="">Semua Status</option>
                  <option value="Aktif">Aktif</option>
                  <option value="Nonaktif">Nonaktif</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="form-group">
                <label for="filterMediaType">Media Type</label>
                <select id="filterMediaType" class="form-control">
                  <option value="">Semua Media</option>
                  <option value="image">image</option>
                  <option value="audio">audio</option>
                  <option value="none">none</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="button" id="resetQuizFilter" class="btn btn-secondary btn-block">
                  <i class="fas fa-undo mr-1"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Daftar Soal Kuis</h3>
        </div>
        <div class="card-body">
          <table id="quizTable" class="table table-bordered table-striped table-hover dt-responsive" style="width: 100%;">
            <thead>
              <tr>
                <th>No</th>
                <th>Mode</th>
                <th>Level</th>
                <th>Nomor Soal</th>
                <th>Pertanyaan</th>
                <th>Media Type</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($questions as $index => $question): ?>
                <tr>
                  <td><?= $index + 1; ?></td>
                  <td><?= $question['mode']; ?></td>
                  <td><?= $question['level']; ?></td>
                  <td><?= $question['number']; ?></td>
                  <td class="question-cell" title="<?= htmlspecialchars($question['question']); ?>">
                    <?= htmlspecialchars(limitWords($question['question'])); ?>
                  </td>
                  <td>
                    <span class="badge badge-<?= quizBadgeClass('media', $question['mediaType']); ?>">
                      <?= $question['mediaType']; ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge badge-<?= quizBadgeClass('status', $question['status']); ?>">
                      <?= $question['status']; ?>
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-info" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-danger" title="Hapus">
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

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
