import { getApps, initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-app.js';
import { getAuth } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-auth.js';
import { getFirestore } from 'https://www.gstatic.com/firebasejs/10.12.5/firebase-firestore.js';
import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm';

export const config = window.SwaraNusaBackendConfig;

export function createBackendClient() {
  if (!config) throw new Error('Konfigurasi backend SwaraNusa belum dimuat.');

  const app = getApps().length ? getApps()[0] : initializeApp(config.firebase);

  return {
    app,
    auth: getAuth(app),
    db: getFirestore(app),
    paths: config.firestorePaths,
    supabase: createClient(config.supabase.url, config.supabase.publishableKey),
  };
}
