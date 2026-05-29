import {
  onAuthStateChanged,
  signOut,
} from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
import { createBackendClient } from './firebase-client.js';

try {
  const { auth } = createBackendClient();

  onAuthStateChanged(auth, (user) => {
    const emailTarget = document.querySelector('[data-admin-email]');
    if (emailTarget) emailTarget.textContent = user?.email || '';
  });

  document.querySelector('[data-admin-logout]')?.addEventListener('click', async (event) => {
    event.preventDefault();
    await signOut(auth);
    window.location.href = 'login.php';
  });
} catch (error) {
  console.error(error);
}
