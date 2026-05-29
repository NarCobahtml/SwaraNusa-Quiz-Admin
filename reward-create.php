<?php
$pageTitle = 'Tambah Hadiah - Admin SwaraNusa Quiz';
$activePage = 'rewards';

include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Tambah Hadiah</h1>
          <span data-backend-status class="badge badge-secondary">Memuat backend...</span>
        </div>
        <div class="col-sm-6">
          <div class="float-sm-right">
            <a href="rewards.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <form data-reward-create-form>
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Data Hadiah</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardType">Tipe Hadiah</label>
                  <select id="rewardType" class="form-control" data-reward-type>
                    <option value="alat_musik_digital">alat_musik_digital</option>
                    <option value="badge">badge</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardId">ID Dokumen</label>
                  <input id="rewardId" type="text" class="form-control" data-reward-id placeholder="contoh: angklung_basic" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardName">Nama</label>
                  <input id="rewardName" type="text" class="form-control" data-reward-name required>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="rewardDescription">Deskripsi / Syarat Unlock</label>
              <textarea id="rewardDescription" class="form-control" rows="3" data-reward-description></textarea>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="rewardAssetFile">Upload Asset ke Supabase</label>
                  <input id="rewardAssetFile" type="file" class="form-control-file" data-reward-asset-file accept="image/*,audio/*">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="rewardAssetUrl">Atau Asset URL</label>
                  <input id="rewardAssetUrl" type="text" class="form-control" data-reward-asset-url placeholder="https://... atau assets/...">
                </div>
              </div>
            </div>

            <div class="row" data-instrument-fields>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardRegion">Daerah</label>
                  <input id="rewardRegion" type="text" class="form-control" data-reward-region>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardSortOrder">Urutan</label>
                  <input id="rewardSortOrder" type="number" min="0" class="form-control" data-reward-sort-order value="0">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardPrice">Harga Koin</label>
                  <input id="rewardPrice" type="number" min="0" class="form-control" data-reward-price value="0">
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="rewardNoteUrls">Note URLs / Audio Minigame</label>
                  <textarea id="rewardNoteUrls" class="form-control" rows="3" data-reward-note-urls placeholder="Satu URL/path per baris, contoh: assets/audio/n1.mp3"></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-check mb-3">
                  <input id="rewardOpensMinigame" type="checkbox" class="form-check-input" data-reward-opens-minigame>
                  <label class="form-check-label" for="rewardOpensMinigame">Buka minigame saat instrumen diklik</label>
                </div>
              </div>
            </div>

            <div class="row" data-badge-fields style="display: none;">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardConditionType">Condition Type</label>
                  <input id="rewardConditionType" type="text" class="form-control" data-reward-condition-type placeholder="contoh: quiz_completed">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardConditionValue">Condition Value</label>
                  <input id="rewardConditionValue" type="number" min="0" class="form-control" data-reward-condition-value value="1">
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rewardStars">Stars</label>
                  <input id="rewardStars" type="number" min="1" max="5" class="form-control" data-reward-stars value="1">
                </div>
              </div>
            </div>

            <div class="form-check">
              <input id="rewardIsActive" type="checkbox" class="form-check-input" data-reward-active checked>
              <label class="form-check-label" for="rewardIsActive">Aktif</label>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i> Simpan Hadiah
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
