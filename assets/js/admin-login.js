import {
  onAuthStateChanged,
  signInWithEmailAndPassword,
} from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
import { createBackendClient, config } from './firebase-client.js';

if (!config) {
  setMessage('Konfigurasi backend SwaraNusa belum dimuat.', true);
} else {
  const { auth } = createBackendClient();
  const form = document.querySelector('[data-login-form]');

  onAuthStateChanged(auth, (user) => {
    if (user) window.location.href = 'index.php';
  });

  form?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const email = document.querySelector('[data-login-email]').value.trim();
    const password = document.querySelector('[data-login-password]').value;
    const button = form.querySelector('button[type="submit"]');

    button.disabled = true;
    setMessage('Memproses login...', false);

    try {
      await signInWithEmailAndPassword(auth, email, password);
      setMessage('Login berhasil. Mengalihkan ke dashboard...', false, 'success');
      window.location.href = 'index.php';
    } catch (error) {
      setMessage('Login gagal: ' + authErrorMessage(error), true);
      button.disabled = false;
    }
  });
}

function setMessage(message, isError = false, type = null) {
  const target = document.querySelector('[data-login-message]');
  if (!target) return;

  const className = type === 'success'
    ? 'text-success small mb-0'
    : isError
      ? 'text-danger small mb-0'
      : 'text-muted small mb-0';

  target.className = className;
  target.textContent = message;
}

function authErrorMessage(error) {
  return {
    'auth/invalid-email': 'format email tidak valid.',
    'auth/invalid-credential': 'email atau password salah.',
    'auth/user-not-found': 'akun tidak ditemukan.',
    'auth/wrong-password': 'password salah.',
    'auth/too-many-requests': 'terlalu banyak percobaan login.',
  }[error.code] || error.message;
}
