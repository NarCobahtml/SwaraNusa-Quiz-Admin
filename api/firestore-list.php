<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(405, ['error' => 'Method not allowed']);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
  respond(400, ['error' => 'Payload JSON tidak valid']);
}

$collection = $payload['collection'] ?? '';
$maxRows = max(1, min((int)($payload['maxRows'] ?? 500), 1000));

$allowedCollections = [
  'users',
  'quiz_modes',
  'levels',
  'questions',
  'instruments',
  'missions',
  'achievements',
  'daily_login_rewards',
  'leaderboards/global/entries',
];

if (!in_array($collection, $allowedCollections, true)) {
  respond(400, ['error' => 'Collection tidak diizinkan']);
}

$serviceAccount = loadServiceAccount();
$projectId = $serviceAccount['project_id'] ?? 'swaranusa-quiz';
$accessToken = fetchAccessToken($serviceAccount);

$url = sprintf(
  'https://firestore.googleapis.com/v1/projects/%s/databases/(default)/documents/%s?pageSize=%d',
  rawurlencode($projectId),
  firestoreCollectionPath($collection),
  $maxRows
);

$response = httpRequest($url, 'GET', [
  'Authorization: Bearer ' . $accessToken,
], '');

if ($response['status'] < 200 || $response['status'] >= 300) {
  respond($response['status'], [
    'error' => 'Firestore read gagal',
    'detail' => $response['body'],
  ]);
}

$body = json_decode($response['body'], true);
$documents = $body['documents'] ?? [];
$rows = array_map('firestoreDocument', $documents);

respond(200, [
  'ok' => true,
  'collection' => $collection,
  'rows' => $rows,
]);

function loadServiceAccount()
{
  $paths = [
    __DIR__ . '/../../serviceAccountKey.json',
    __DIR__ . '/../../swaranusaquiz - 3/serviceAccountKey.json',
  ];

  foreach ($paths as $path) {
    if (is_file($path)) {
      $data = json_decode(file_get_contents($path), true);
      if (is_array($data)) return $data;
    }
  }

  respond(500, ['error' => 'serviceAccountKey.json tidak ditemukan']);
}

function firestoreCollectionPath($collection)
{
  $segments = array_filter(explode('/', $collection), fn($segment) => $segment !== '');
  return implode('/', array_map('rawurlencode', $segments));
}

function firestoreDocument(array $document)
{
  $name = $document['name'] ?? '';
  $parts = explode('/', $name);
  $row = ['id' => end($parts)];
  foreach (($document['fields'] ?? []) as $field => $value) {
    $row[$field] = firestoreValue($value);
  }
  return $row;
}

function firestoreValue(array $value)
{
  if (array_key_exists('stringValue', $value)) return $value['stringValue'];
  if (array_key_exists('integerValue', $value)) return (int)$value['integerValue'];
  if (array_key_exists('doubleValue', $value)) return (float)$value['doubleValue'];
  if (array_key_exists('booleanValue', $value)) return (bool)$value['booleanValue'];
  if (array_key_exists('timestampValue', $value)) return $value['timestampValue'];
  if (array_key_exists('nullValue', $value)) return null;

  if (array_key_exists('arrayValue', $value)) {
    return array_map('firestoreValue', $value['arrayValue']['values'] ?? []);
  }

  if (array_key_exists('mapValue', $value)) {
    $map = [];
    foreach (($value['mapValue']['fields'] ?? []) as $key => $child) {
      $map[$key] = firestoreValue($child);
    }
    return $map;
  }

  return null;
}

function fetchAccessToken(array $serviceAccount)
{
  $cachedToken = readCachedAccessToken($serviceAccount);
  if ($cachedToken) return $cachedToken;

  $now = time();
  $jwtHeader = base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
  $jwtClaim = base64Url(json_encode([
    'iss' => $serviceAccount['client_email'],
    'scope' => 'https://www.googleapis.com/auth/datastore',
    'aud' => 'https://oauth2.googleapis.com/token',
    'iat' => $now,
    'exp' => $now + 3600,
  ]));

  $unsignedJwt = $jwtHeader . '.' . $jwtClaim;
  openssl_sign($unsignedJwt, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
  $jwt = $unsignedJwt . '.' . base64Url($signature);

  $response = httpRequest('https://oauth2.googleapis.com/token', 'POST', [
    'Content-Type: application/x-www-form-urlencoded',
  ], http_build_query([
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $jwt,
  ]));

  $data = json_decode($response['body'], true);
  if ($response['status'] !== 200 || empty($data['access_token'])) {
    respond(500, ['error' => 'Gagal mengambil access token Google', 'detail' => $response['body']]);
  }

  writeCachedAccessToken($serviceAccount, $data);
  return $data['access_token'];
}

function readCachedAccessToken(array $serviceAccount)
{
  $path = accessTokenCachePath($serviceAccount);
  if (!is_file($path)) return null;

  $cacheBody = @file_get_contents($path);
  if ($cacheBody === false) return null;

  $cached = json_decode($cacheBody, true);
  if (!is_array($cached) || empty($cached['access_token']) || empty($cached['expires_at'])) {
    return null;
  }

  return ((int) $cached['expires_at'] - 120) > time()
    ? $cached['access_token']
    : null;
}

function writeCachedAccessToken(array $serviceAccount, array $data)
{
  if (empty($data['access_token'])) return;

  $payload = [
    'access_token' => $data['access_token'],
    'expires_at' => time() + (int)($data['expires_in'] ?? 3600),
  ];

  @file_put_contents(accessTokenCachePath($serviceAccount), json_encode($payload), LOCK_EX);
}

function accessTokenCachePath(array $serviceAccount)
{
  $key = sha1($serviceAccount['client_email'] ?? 'swaranusa-firestore');
  return sys_get_temp_dir() . '/swaranusa-firestore-token-' . $key . '.json';
}

function httpRequest($url, $method, array $headers, $body)
{
  $context = stream_context_create([
    'http' => [
      'method' => $method,
      'header' => implode("\r\n", $headers),
      'content' => $body,
      'ignore_errors' => true,
      'timeout' => 15,
    ],
  ]);

  $body = file_get_contents($url, false, $context);
  $status = 0;

  foreach ($http_response_header ?? [] as $header) {
    if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $match)) {
      $status = (int) $match[1];
      break;
    }
  }

  return ['status' => $status, 'body' => $body ?: ''];
}

function base64Url($value)
{
  return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function respond($status, array $data)
{
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_SLASHES);
  exit;
}
