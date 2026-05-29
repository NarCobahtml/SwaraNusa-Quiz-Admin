import {
  actionButtons,
  displayName,
  escapeAttribute,
  escapeHtml,
  formatDate,
  formatNumber,
  levelLabel,
  mediaBadge,
  publicAssetUrl,
  rewardTypeBadge,
} from './admin-utils.js';

function limitWords(value, maxWords = 5) {
  const words = String(value || '').trim().split(/\s+/).filter(Boolean);
  if (words.length <= maxWords) return words.join(' ');

  return words.slice(0, maxWords).join(' ') + '...';
}

export function renderQuizTable({ questions, modes, levels }) {
  const table = $('#quizTable');
  if (!table.length) return;

  const dataTable = table.DataTable();
  dataTable.clear();

  questions.forEach((question, index) => {
    const mode = modes.find((row) => row.id === question.modeId);
    const level = levels.find((row) => row.id === question.levelId);
    const mediaType = question.mediaType || 'none';
    const status = question.isActive === true ? 'Aktif' : 'Nonaktif';
    const questionText = question.questionText || question.title || '-';

    dataTable.row.add([
      index + 1,
      escapeHtml(mode?.title || question.modeId || '-'),
      escapeHtml(levelLabel(level, question.levelId)),
      formatNumber(question.questionNumber || 0),
      `<span class="question-cell" title="${escapeAttribute(questionText)}">${escapeHtml(limitWords(questionText))}</span>`,
      `<span class="badge badge-${mediaBadge(mediaType)}">${escapeHtml(mediaType)}</span>`,
      `<span class="badge badge-${question.isActive === true ? 'success' : 'warning'}">${status}</span>`,
      actionButtons('quiz', question.id),
    ]);
  });

  dataTable.draw();
}

export function renderUsersTable({ users }) {
  const table = $('#usersTable');
  if (!table.length) return;

  const dataTable = table.DataTable();
  dataTable.clear();

  users.forEach((user, index) => {
    dataTable.row.add([
      index + 1,
      escapeHtml(displayName(user)),
      escapeHtml(user.email || '-'),
      `<span class="badge badge-${user.role === 'admin' ? 'primary' : 'secondary'}">${escapeHtml(user.role || 'user')}</span>`,
      formatNumber(user.xp || 0),
      `<span class="badge badge-${user.isActive === false || user.status === 'nonaktif' ? 'warning' : 'success'}">${escapeHtml(user.status || (user.isActive === false ? 'nonaktif' : 'aktif'))}</span>`,
      formatDate(user.createdAt),
      actionButtons('user', user.id),
    ]);
  });

  dataTable.draw();
}

export function renderRewardsTable({ rewards, supabase }) {
  const table = $('#rewardsTable');
  if (!table.length) return;

  const dataTable = table.DataTable();
  dataTable.clear();

  rewards.forEach((reward, index) => {
    const status = reward.isActive === true ? 'Aktif' : 'Nonaktif';
    const assetUrl = publicAssetUrl(supabase, reward.assetUrl);

    dataTable.row.add([
      index + 1,
      assetUrl
        ? `<img src="${escapeAttribute(assetUrl)}" alt="${escapeAttribute(reward.name)}" class="img-size-50 img-circle elevation-1">`
        : '<span class="btn btn-default btn-sm disabled" style="width: 50px; height: 50px;"><i class="fas fa-gift mt-2"></i></span>',
      escapeHtml(reward.name || '-'),
      `<span class="badge badge-${rewardTypeBadge(reward.type)}">${escapeHtml(reward.type || '-')}</span>`,
      escapeHtml(reward.unlockCondition || '-'),
      escapeHtml(reward.modeId || 'Semua Mode'),
      reward.levelNumber ? 'Level ' + formatNumber(reward.levelNumber) : '-',
      `<span class="badge badge-${reward.isActive === true ? 'success' : 'warning'}">${status}</span>`,
      actionButtons('reward', reward.id),
    ]);
  });

  dataTable.draw();
}
