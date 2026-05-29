import {
  displayName,
  escapeHtml,
  formatNumber,
  levelLabel,
  modeColor,
  modeIcon,
  setText,
  timestampValue,
} from './admin-utils.js';

export function renderDashboard({ users, modes, levels, questions, rewards }) {
  setText('[data-stat="totalQuestions"]', formatNumber(questions.length));
  setText('[data-stat="activeQuestions"]', formatNumber(questions.filter((row) => row.isActive === true).length));
  setText('[data-stat="totalUsers"]', formatNumber(users.length));
  setText('[data-stat="totalRewards"]', formatNumber(rewards.length));

  const modeRows = modes.map((mode) => {
    const modeQuestions = questions.filter((question) => question.modeId === mode.id);
    return {
      mode: mode.title || mode.id,
      total: modeQuestions.length,
      active: modeQuestions.filter((question) => question.isActive === true).length,
      icon: modeIcon(mode.id),
      color: modeColor(mode.id),
    };
  });

  renderModeList(modeRows);
  renderWarnings(questions);
  renderLatestQuestions(questions, modes, levels);
  renderLeaderboard(users.slice(0, 10));
  refreshCharts(modeRows, questions);
}

function renderModeList(rows) {
  const list = document.querySelector('[data-mode-list]');
  if (!list) return;

  list.innerHTML = rows.map((row) => `
    <li class="item">
      <div class="product-img">
        <span class="btn btn-${row.color} btn-sm"><i class="${row.icon}"></i></span>
      </div>
      <div class="product-info">
        <span class="product-title">
          ${escapeHtml(row.mode)}
          <span class="badge badge-${row.color} float-right">${formatNumber(row.total)} soal</span>
        </span>
        <span class="product-description">${formatNumber(row.active)} soal aktif dari ${formatNumber(row.total)} soal</span>
      </div>
    </li>
  `).join('');
}

function renderWarnings(questions) {
  const rows = [
    ['Soal tanpa mediaUrl', questions.filter((row) => row.mediaType && row.mediaType !== 'none' && !row.mediaUrl).length, 'Perlu dilengkapi agar soal gambar atau suara dapat tampil di aplikasi.', 'danger', 'fas fa-photo-video'],
    ['Soal tanpa correctAnswer', questions.filter((row) => !row.correctAnswer).length, 'Soal belum bisa divalidasi karena jawaban benar masih kosong.', 'danger', 'fas fa-check-circle'],
    ['Soal nonaktif', questions.filter((row) => row.isActive !== true).length, 'Soal tersimpan tetapi belum tersedia untuk pemain.', 'warning', 'fas fa-pause-circle'],
    ['Soal tanpa levelId/modeId', questions.filter((row) => !row.levelId || !row.modeId).length, 'Data relasi level atau mode perlu diperbaiki sebelum dipakai.', 'warning', 'fas fa-link'],
  ];

  const tbody = document.querySelector('[data-warning-table]');
  if (!tbody) return;

  tbody.innerHTML = rows.map(([label, count, description, color, icon]) => `
    <tr>
      <td>
        <i class="${icon} text-${color} mr-1"></i>
        <strong>${escapeHtml(label)}</strong>
        <div class="text-muted small">${escapeHtml(description)}</div>
      </td>
      <td class="text-center align-middle"><span class="badge badge-${color}">${formatNumber(count)}</span></td>
    </tr>
  `).join('');
}

function renderLatestQuestions(questions, modes, levels) {
  const tbody = document.querySelector('[data-latest-questions]');
  if (!tbody) return;

  const latest = [...questions]
    .sort((a, b) => timestampValue(b.updatedAt || b.createdAt) - timestampValue(a.updatedAt || a.createdAt))
    .slice(0, 8);

  tbody.innerHTML = latest.map((question) => {
    const mode = modes.find((row) => row.id === question.modeId);
    const level = levels.find((row) => row.id === question.levelId);
    const status = question.isActive === true ? 'Aktif' : 'Nonaktif';
    const statusClass = question.isActive === true ? 'success' : 'warning';
    return `
      <tr>
        <td>${escapeHtml(mode?.title || question.modeId || '-')}</td>
        <td>${escapeHtml(level?.title || levelLabel(level, question.levelId))}</td>
        <td>${formatNumber(question.questionNumber || 0)}</td>
        <td class="text-wrap" style="min-width: 260px;">${escapeHtml(question.questionText || question.title || '-')}</td>
        <td><span class="badge badge-${statusClass}">${status}</span></td>
      </tr>
    `;
  }).join('');
}

function renderLeaderboard(users) {
  const tbody = document.querySelector('[data-leaderboard-users]');
  if (!tbody) return;

  tbody.innerHTML = users.map((user, index) => {
    const rank = index + 1;
    const rankClass = rank === 1 ? 'warning' : rank === 2 ? 'info' : rank === 3 ? 'success' : 'secondary';
    return `
      <tr>
        <td><span class="badge badge-${rankClass}">#${rank}</span></td>
        <td>${escapeHtml(displayName(user))}</td>
        <td>${escapeHtml(user.email || '-')}</td>
        <td class="text-right"><strong>${formatNumber(user.xp || 0)}</strong></td>
        <td class="text-right">${formatNumber(user.xp || 0)}</td>
        <td><span class="badge badge-primary">Level ${formatNumber(user.level || 0)}</span></td>
        <td><span class="badge badge-success">aktif</span></td>
      </tr>
    `;
  }).join('');
}

function refreshCharts(modeRows, questions) {
  if (window.questionsByModeChart) {
    window.questionsByModeChart.data.labels = modeRows.map((row) => row.mode);
    window.questionsByModeChart.data.datasets[0].data = modeRows.map((row) => row.total);
    window.questionsByModeChart.update();
  }

  if (window.questionStatusChart) {
    window.questionStatusChart.data.datasets[0].data = [
      questions.filter((row) => row.isActive === true).length,
      questions.filter((row) => row.isActive !== true).length,
    ];
    window.questionStatusChart.update();
  }
}
