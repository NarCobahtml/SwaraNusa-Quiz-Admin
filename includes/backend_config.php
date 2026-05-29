<?php
$backendConfig = [
  'firebase' => [
    'apiKey' => 'AIzaSyB1yvTe8OoIYv1n7gdpRYg1wdOz4wBVonY',
    'authDomain' => 'swaranusa-quiz.firebaseapp.com',
    'projectId' => 'swaranusa-quiz',
    'storageBucket' => 'swaranusa-quiz.firebasestorage.app',
    'messagingSenderId' => '769197054026',
    'appId' => '1:769197054026:web:dcbc86e21c93d11b70aeb3',
    'measurementId' => 'G-46P8JEMXJ8',
  ],
  'supabase' => [
    'url' => 'https://osqknrakoqfvweuywzxi.supabase.co',
    'publishableKey' => 'sb_publishable__aUAXHeOB50ZlXoPOVAjhg_0wNrZh7p',
    'avatarBucket' => 'avatars',
    'mediaBucket' => 'quiz-media',
    'rewardBucket' => 'reward',
  ],
  'firestorePaths' => [
    'users' => 'users',
    'quizModes' => 'quiz_modes',
    'levels' => 'levels',
    'questions' => 'questions',
    'instruments' => 'instruments',
    'missions' => 'missions',
    'achievements' => 'achievements',
    'dailyLoginRewards' => 'daily_login_rewards',
    'leaderboards' => 'leaderboards',
  ],
];
?>
<script>
  window.SwaraNusaBackendConfig = <?= json_encode($backendConfig, JSON_UNESCAPED_SLASHES); ?>;
</script>
