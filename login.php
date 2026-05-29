<?php
$pageTitle = 'Login - Admin SwaraNusa Quiz';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="node_modules/admin-lte/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <strong>SwaraNusa</strong> Admin
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Masuk dengan akun Firebase admin</p>

        <form data-login-form>
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" autocomplete="email" data-login-email required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>

          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" autocomplete="current-password" data-login-password required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-sign-in-alt mr-1"></i> Login
          </button>
        </form>

        <p class="text-muted small mb-0 mt-3" data-login-message>
          Gunakan akun Firebase Auth yang diizinkan oleh Firestore rules.
        </p>
      </div>
    </div>
  </div>

  <script src="node_modules/jquery/dist/jquery.min.js"></script>
  <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <?php include __DIR__ . '/includes/backend_config.php'; ?>
  <script type="module" src="assets/js/admin-login.js"></script>
</body>
</html>
