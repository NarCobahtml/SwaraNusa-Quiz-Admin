<?php
$pageTitle = 'Tambah Soal - Admin SwaraNusa Quiz';
$activePage = 'quiz';

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Tambah Soal</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right">
            <a href="quiz.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form data-quiz-create-form>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Soal</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="quizModeId">Mode</label>
                  <select id="quizModeId" class="form-control" data-quiz-mode required>
                    <option value="">Memuat mode...</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="quizLevelNumber">Level</label>
                  <input id="quizLevelNumber" type="number" min="1" class="form-control" data-quiz-level-number value="1" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="quizQuestionNumber">Nomor Soal</label>
                  <input id="quizQuestionNumber" type="number" min="1" class="form-control" data-quiz-question-number required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="quizTitle">Judul</label>
                  <input id="quizTitle" type="text" class="form-control" data-quiz-title value="Tebak Gambar" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="quizMediaType">Media Type</label>
                  <select id="quizMediaType" class="form-control" data-quiz-media-type>
                    <option value="image">image</option>
                    <option value="audio">audio</option>
                    <option value="none">none</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="quizTimeLimit">Waktu</label>
                  <input id="quizTimeLimit" type="number" min="0" class="form-control" data-quiz-time-limit value="30">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label for="quizPoints">Poin</label>
                  <input id="quizPoints" type="number" min="0" class="form-control" data-quiz-points value="10">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="quizQuestionText">Pertanyaan</label>
              <textarea id="quizQuestionText" class="form-control" rows="3" data-quiz-question-text required></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="quizMediaFile">Upload Media ke Supabase</label>
                  <input id="quizMediaFile" type="file" class="form-control-file" data-quiz-media-file accept="image/*,audio/*">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="quizMediaUrl">Atau Media URL</label>
                  <input id="quizMediaUrl" type="text" class="form-control" data-quiz-media-url placeholder="https://... atau assets/...">
                </div>
              </div>
            </div>

            <div class="row">
              <?php for ($optionIndex = 1; $optionIndex <= 4; $optionIndex++): ?>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quizOption<?= $optionIndex; ?>">Opsi <?= $optionIndex; ?></label>
                    <input id="quizOption<?= $optionIndex; ?>" type="text" class="form-control" data-quiz-option required>
                  </div>
                </div>
              <?php endfor; ?>
            </div>

            <div class="form-group">
              <label for="quizCorrectAnswer">Jawaban Benar</label>
              <input id="quizCorrectAnswer" type="text" class="form-control" data-quiz-correct-answer required>
            </div>

            <div class="form-group">
              <label for="quizExplanation">Pembahasan</label>
              <textarea id="quizExplanation" class="form-control" rows="3" data-quiz-explanation></textarea>
            </div>

            <div class="form-check">
              <input id="quizIsActive" type="checkbox" class="form-check-input" data-quiz-active checked>
              <label class="form-check-label" for="quizIsActive">Aktif</label>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Simpan Soal
            </button>
            <span class="ml-3 text-muted" data-form-message></span>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>

<?php
include 'includes/footer.php';
include 'includes/scripts.php';
?>
