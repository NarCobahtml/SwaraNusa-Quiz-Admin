import { onAuthStateChanged } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
import { createBackendClient } from './firebase-client.js';
import { loadBackendData } from './admin-data.js';
import { bindAdminActions } from './admin-actions.js';
import { renderDashboard } from './admin-dashboard.js';
import {
  renderQuizTable,
  renderRewardsTable,
  renderUsersTable,
} from './admin-tables.js';
import { renderConnectionState } from './admin-utils.js';

try {
  const { auth, db, paths, supabase } = createBackendClient();

  onAuthStateChanged(auth, async (user) => {
    if (!user) {
      window.location.href = 'login.php';
      return;
    }

    try {
      const context = await loadBackendData(db, supabase, paths, requiredCollections());
      renderConnectionState('Firestore dan Supabase terhubung sebagai ' + user.email);
      renderDashboard(context);
      renderQuizTable(context);
      renderUsersTable(context);
      renderRewardsTable(context);
      bindAdminActions(context);
    } catch (error) {
      console.error(error);
      const message = error.code === 'permission-denied'
        ? 'Gagal memuat backend: akun Firebase ini belum punya izin Firestore'
        : 'Gagal memuat backend: ' + error.message;
      renderConnectionState(message, true);
    }
  });
} catch (error) {
  console.error(error);
  renderConnectionState(error.message, true);
}

function requiredCollections() {
  if (document.querySelector('[data-stat]')) {
    return ['users', 'modes', 'levels', 'questions', 'instruments', 'achievements'];
  }

  if (document.querySelector('#quizTable')) {
    return ['modes', 'levels', 'questions'];
  }

  if (document.querySelector('#usersTable')) {
    return ['users'];
  }

  if (document.querySelector('#rewardsTable')) {
    return ['instruments', 'achievements'];
  }

  return [];
}
