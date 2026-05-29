<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">SwaraNusa Admin</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">

        <li class="nav-item">
          <a href="index.php" class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="quiz.php" class="nav-link <?= ($activePage ?? '') === 'quiz' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-question-circle"></i>
            <p>Data Kuis</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="users.php" class="nav-link <?= ($activePage ?? '') === 'users' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Data Pengguna</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="rewards.php" class="nav-link <?= ($activePage ?? '') === 'rewards' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-gift"></i>
            <p>Data Hadiah</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>