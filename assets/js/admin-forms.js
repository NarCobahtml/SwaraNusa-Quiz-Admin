import { onAuthStateChanged } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
import { config, createBackendClient } from './firebase-client.js';
import {
  escapeAttribute,
  escapeHtml,
  fetchCollection,
  numberOf,
  resolveAssetUrl,
  saveFirestoreDocument,
  setMessage,
  slug,
  valueOf,
} from './admin-utils.js';

const REWARD_STORAGE = {
  badge: { bucket: config?.supabase?.rewardBucket || 'reward', folder: 'badges' },
  instrumentImage: { bucket: config?.supabase?.rewardBucket || 'reward', folder: 'instruments' },
};

function quizMediaStorage(mediaType, levelNumber) {
  const modeFolder = mediaType === 'audio' ? 'tebak_suara' : 'tebak_gambar';
  return {
    bucket: config?.supabase?.mediaBucket || 'quiz-media',
    folder: `${modeFolder}/level_${levelNumber}`,
  };
}

try {
  const { auth, db, supabase } = createBackendClient();

  onAuthStateChanged(auth, async (user) => {
    if (!user) return;

    await hydrateQuizForm(db, supabase);
    bindRewardForm(supabase);
  });
} catch (error) {
  console.error(error);
}

async function hydrateQuizForm(db, supabase) {
  const form = document.querySelector('[data-quiz-create-form]');
  if (!form || form.dataset.ready === 'true') return;
  form.dataset.ready = 'true';

  const modeSelect = form.querySelector('[data-quiz-mode]');
  const message = form.querySelector('[data-form-message]');
  const modes = sortBy(await fetchCollection(db, 'quiz_modes'), 'order');

  modeSelect.innerHTML = '<option value="">Pilih Mode</option>' + modes
    .map((mode) => `<option value="${escapeAttribute(mode.id)}">${escapeHtml(mode.title || mode.id)}</option>`)
    .join('');

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    setMessage(message, 'Menyimpan soal...');

    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;

    try {
      await saveQuiz(form, supabase, modes);
      setMessage(message, 'Soal berhasil disimpan ke Firestore.', 'success');
      setTimeout(() => window.location.href = 'quiz.php', 900);
    } catch (error) {
      console.error(error);
      setMessage(message, error.message, 'danger');
      button.disabled = false;
    }
  });
}

async function saveQuiz(form, supabase, modes) {
  const modeId = valueOf(form, '[data-quiz-mode]');
  const levelNumber = numberOf(form, '[data-quiz-level-number]');
  const selectedMode = modes.find((mode) => mode.id === modeId);
  const modeTitle = selectedMode?.title || modeId;
  const levelId = `${modeId}_${levelNumber}`;
  const questionNumber = numberOf(form, '[data-quiz-question-number]');
  const mediaType = valueOf(form, '[data-quiz-media-type]') || 'none';
  const file = form.querySelector('[data-quiz-media-file]').files[0];
  const options = [...form.querySelectorAll('[data-quiz-option]')]
    .map((input) => input.value.trim())
    .filter(Boolean);

  if (!modeId) throw new Error('Pilih mode soal.');
  if (levelNumber < 1) throw new Error('Level minimal 1.');
  if (options.length !== 4) throw new Error('Isi tepat 4 opsi jawaban.');

  const mediaUrl = mediaType === 'none'
    ? ''
    : await resolveAssetUrl(
      supabase,
      file,
      valueOf(form, '[data-quiz-media-url]'),
      quizMediaStorage(mediaType, levelNumber),
    );

  await saveFirestoreDocument('levels', levelId, {
    modeId,
    levelNumber,
    title: `${modeTitle} Level ${levelNumber}`,
    description: '',
    totalQuestions: questionNumber,
    requiredXp: 0,
    unlockAfterLevelId: levelNumber > 1 ? `${modeId}_${levelNumber - 1}` : '',
    rewardXp: 50,
    rewardCoin: 100,
    isActive: true,
  });

  await saveFirestoreDocument('questions', `${levelId}_q${questionNumber}`, {
    modeId,
    levelId,
    questionNumber,
    title: valueOf(form, '[data-quiz-title]'),
    questionText: valueOf(form, '[data-quiz-question-text]'),
    mediaType,
    mediaUrl,
    options,
    correctAnswer: valueOf(form, '[data-quiz-correct-answer]'),
    explanation: valueOf(form, '[data-quiz-explanation]'),
    timeLimitSeconds: numberOf(form, '[data-quiz-time-limit]'),
    points: numberOf(form, '[data-quiz-points]'),
    isActive: form.querySelector('[data-quiz-active]').checked,
  });
}

function bindRewardForm(supabase) {
  const form = document.querySelector('[data-reward-create-form]');
  if (!form || form.dataset.ready === 'true') return;
  form.dataset.ready = 'true';

  const typeSelect = form.querySelector('[data-reward-type]');
  const message = form.querySelector('[data-form-message]');

  syncRewardTypeFields(form, typeSelect.value);
  typeSelect.addEventListener('change', () => syncRewardTypeFields(form, typeSelect.value));

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    setMessage(message, 'Menyimpan hadiah...');

    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;

    try {
      await saveReward(form, supabase);
      setMessage(message, 'Hadiah berhasil disimpan ke Firestore.', 'success');
      setTimeout(() => window.location.href = 'rewards.php', 900);
    } catch (error) {
      console.error(error);
      setMessage(message, error.message, 'danger');
      button.disabled = false;
    }
  });
}

function syncRewardTypeFields(form, type) {
  const isBadge = type === 'badge';
  form.querySelector('[data-instrument-fields]').style.display = isBadge ? 'none' : '';
  form.querySelector('[data-badge-fields]').style.display = isBadge ? '' : 'none';
}

async function saveReward(form, supabase) {
  const type = valueOf(form, '[data-reward-type]');
  const id = slug(valueOf(form, '[data-reward-id]'));
  const file = form.querySelector('[data-reward-asset-file]').files[0];
  const assetUrl = type === 'badge'
    ? await resolveAssetUrl(supabase, file, valueOf(form, '[data-reward-asset-url]'), REWARD_STORAGE.badge)
    : await resolveAssetUrl(supabase, file, valueOf(form, '[data-reward-asset-url]'), REWARD_STORAGE.instrumentImage);

  if (!id) throw new Error('ID dokumen wajib diisi.');

  if (type === 'badge') {
    await saveFirestoreDocument('achievements', id, {
      name: valueOf(form, '[data-reward-name]'),
      description: valueOf(form, '[data-reward-description]'),
      iconUrl: assetUrl,
      stars: numberOf(form, '[data-reward-stars]'),
      conditionType: valueOf(form, '[data-reward-condition-type]'),
      conditionValue: numberOf(form, '[data-reward-condition-value]'),
      isActive: form.querySelector('[data-reward-active]').checked,
    });
    return;
  }

  await saveFirestoreDocument('instruments', id, {
    name: valueOf(form, '[data-reward-name]'),
    region: valueOf(form, '[data-reward-region]'),
    description: valueOf(form, '[data-reward-description]'),
    imageUrl: assetUrl,
    noteUrls: linesOf(valueOf(form, '[data-reward-note-urls]')),
    sortOrder: numberOf(form, '[data-reward-sort-order]'),
    price: numberOf(form, '[data-reward-price]'),
    opensMinigame: form.querySelector('[data-reward-opens-minigame]').checked,
    isActive: form.querySelector('[data-reward-active]').checked,
  });
}

function linesOf(value) {
  return String(value || '')
    .split('\n')
    .map((item) => item.trim())
    .filter(Boolean);
}

function sortBy(rows, field) {
  return [...rows].sort((a, b) => {
    const left = a[field] ?? '';
    const right = b[field] ?? '';
    if (typeof left === 'number' && typeof right === 'number') {
      return left - right;
    }
    return String(left).localeCompare(String(right), 'id');
  });
}
