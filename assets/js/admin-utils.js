import { config } from './firebase-client.js';

export async function fetchCollection(db, path, sortOrder = null, maxRows = 500) {
  const response = await fetch('api/firestore-list.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ collection: path, maxRows }),
  });

  const result = await response.json().catch(() => ({}));
  if (!response.ok || !result.ok) {
    throw new Error(errorMessage(result, 'Gagal memuat data Firestore.'));
  }

  return result.rows || [];
}

export async function saveFirestoreDocument(collectionName, documentId, data, options = {}) {
  const response = await fetch('api/firestore-save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ collection: collectionName, documentId, data, ...options }),
  });

  const result = await response.json().catch(() => ({}));
  if (!response.ok || !result.ok) {
    throw new Error(errorMessage(result, 'Gagal menyimpan data ke Firestore.'));
  }

  return result;
}

export async function deleteFirestoreDocument(collectionName, documentId) {
  const response = await fetch('api/firestore-save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', collection: collectionName, documentId }),
  });

  const result = await response.json().catch(() => ({}));
  if (!response.ok || !result.ok) {
    throw new Error(errorMessage(result, 'Gagal menghapus data dari Firestore.'));
  }

  return result;
}

function errorMessage(result, fallback) {
  const message = result.error || fallback;
  if (!result.detail) return message;

  try {
    const detail = JSON.parse(result.detail);
    const detailMessage = detail?.error?.message;
    return detailMessage ? `${message}: ${detailMessage}` : message;
  } catch (error) {
    return `${message}: ${result.detail}`;
  }
}

export async function resolveAssetUrl(supabase, file, fallbackUrl, target = {}) {
  if (!file) return fallbackUrl;

  const isTargetObject = typeof target === 'object' && target !== null;
  const bucket = isTargetObject
    ? target.bucket || config.supabase.mediaBucket || config.supabase.avatarBucket || 'avatars'
    : config.supabase.mediaBucket || config.supabase.avatarBucket || 'avatars';
  const folder = isTargetObject ? target.folder : target;
  const extension = file.name.includes('.') ? file.name.split('.').pop() : 'bin';
  const path = `${folder}/${Date.now()}-${Math.random().toString(36).slice(2)}.${extension}`;
  const { error } = await supabase.storage.from(bucket).upload(path, file, {
    cacheControl: '3600',
    upsert: false,
    contentType: file.type || undefined,
  });

  if (error) throw new Error('Upload Supabase gagal: ' + error.message);

  return supabase.storage.from(bucket).getPublicUrl(path).data.publicUrl;
}

export async function deleteSupabaseAssetUrl(supabase, value) {
  const asset = parseSupabasePublicAsset(value);
  if (!asset) return;

  const { error } = await supabase.storage.from(asset.bucket).remove([asset.path]);
  if (error) throw new Error('Hapus Supabase gagal: ' + error.message);
}

function parseSupabasePublicAsset(value) {
  if (!value || !/^https?:\/\//.test(value)) return null;

  let url;
  try {
    url = new URL(value);
  } catch (error) {
    return null;
  }

  const marker = '/storage/v1/object/public/';
  const markerIndex = url.pathname.indexOf(marker);
  if (markerIndex === -1) return null;

  const storagePath = decodeURIComponent(url.pathname.slice(markerIndex + marker.length));
  const [bucket, ...pathParts] = storagePath.split('/');
  const path = pathParts.join('/');
  if (!bucket || !path) return null;

  return { bucket, path };
}

export function publicAssetUrl(supabase, value) {
  if (!value) return '';
  if (/^(https?:)?\/\//.test(value) || value.startsWith('assets/') || value.startsWith('node_modules/')) return value;

  const bucket = config.supabase.avatarBucket || 'avatars';
  const path = value.startsWith(bucket + '/') ? value.slice(bucket.length + 1) : value;
  return supabase.storage.from(bucket).getPublicUrl(path).data.publicUrl;
}

export function withoutTemplates(rows) {
  return rows.filter((row) => !String(row.id || '').startsWith('_'));
}

export function buildRewards(instruments, achievements) {
  return [
    ...instruments.map((item) => ({
      ...item,
      id: item.id,
      collection: 'instruments',
      name: item.name || item.id,
      type: 'alat_musik_digital',
      assetUrl: item.imageUrl || '',
      noteUrls: Array.isArray(item.noteUrls) ? item.noteUrls : [],
      unlockCondition: item.price > 0
        ? `Bisa dibeli ${formatNumber(item.price)} koin`
        : 'Sudah tersedia di koleksi',
      modeId: '',
      levelId: '',
      levelNumber: Number(item.sortOrder || 0),
      isActive: item.isActive === true,
    })),
    ...achievements.map((item) => ({
      ...item,
      id: item.id,
      collection: 'achievements',
      name: item.name || item.id,
      type: 'badge',
      assetUrl: item.iconUrl || '',
      unlockCondition: item.description || item.conditionType || '-',
      modeId: '',
      levelId: '',
      levelNumber: Number(item.conditionValue || 0),
      isActive: item.isActive === true,
    })),
  ];
}

export function renderConnectionState(message, isError = false) {
  const target = document.querySelector('[data-backend-status]');
  if (!target) return;

  target.className = 'badge badge-' + (isError ? 'danger' : 'success');
  target.textContent = message;
}

export function valueOf(form, selector) {
  return (form.querySelector(selector)?.value || '').trim();
}

export function numberOf(form, selector) {
  return Number(valueOf(form, selector) || 0);
}

export function setMessage(target, message, type = 'muted') {
  if (!target) return;
  target.className = `ml-3 text-${type}`;
  target.textContent = message;
}

export function slug(value) {
  return value.toLowerCase().trim().replace(/[^a-z0-9_ -]/g, '').replace(/\s+/g, '_').replace(/-+/g, '_');
}

export function levelLabel(level, fallback) {
  if (level?.levelNumber) return 'Level ' + level.levelNumber;
  const levelNumber = String(fallback || '').match(/(?:^|_)(\d+)$/)?.[1];
  if (levelNumber) return 'Level ' + levelNumber;
  return fallback || '-';
}

export function displayName(user) {
  return user.name || user.username || user.email || 'User';
}

export function timestampValue(value) {
  if (!value) return 0;
  if (typeof value.toMillis === 'function') return value.toMillis();
  if (typeof value.seconds === 'number') return value.seconds * 1000;
  return Date.parse(value) || 0;
}

export function formatDate(value) {
  const timestamp = timestampValue(value);
  if (!timestamp) return '-';
  return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(new Date(timestamp));
}

export function formatNumber(value) {
  return new Intl.NumberFormat('id-ID').format(Number(value || 0));
}

export function mediaBadge(value) {
  return { image: 'info', audio: 'success', none: 'secondary' }[value] || 'secondary';
}

export function rewardTypeBadge(value) {
  return {
    alat_musik_digital: 'primary',
    badge: 'info',
    item: 'success',
    lainnya: 'secondary',
  }[value] || 'secondary';
}

export function modeColor(id) {
  if (String(id).includes('suara')) return 'success';
  if (String(id).includes('sejarah')) return 'warning';
  return 'info';
}

export function modeIcon(id) {
  if (String(id).includes('suara')) return 'fas fa-volume-up';
  if (String(id).includes('sejarah')) return 'fas fa-landmark';
  return 'fas fa-image';
}

export function actionButtons(type = 'default', id = '') {
  const detailTitle = type === 'default' ? 'Detail' : 'Detail';
  const escapedType = escapeAttribute(type);
  const escapedId = escapeAttribute(id);
  return `
    <div class="btn-group btn-group-sm">
      <button type="button" class="btn btn-info" title="${detailTitle}" data-action="detail" data-type="${escapedType}" data-id="${escapedId}"><i class="fas fa-eye"></i></button>
      <button type="button" class="btn btn-warning" title="Edit" data-action="edit" data-type="${escapedType}" data-id="${escapedId}"><i class="fas fa-edit"></i></button>
      <button type="button" class="btn btn-danger" title="Hapus" data-action="delete" data-type="${escapedType}" data-id="${escapedId}"><i class="fas fa-trash"></i></button>
    </div>
  `;
}

export function setText(selector, value) {
  const target = document.querySelector(selector);
  if (target) target.textContent = value;
}

export function escapeHtml(value) {
  const element = document.createElement('div');
  element.textContent = value == null ? '' : String(value);
  return element.innerHTML;
}

export function escapeAttribute(value) {
  return escapeHtml(value).replace(/"/g, '&quot;');
}
