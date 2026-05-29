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
$documentId = $payload['documentId'] ?? '';
$action = $payload['action'] ?? 'save';
$data = $payload['data'] ?? null;
$preserveCreatedAt = !empty($payload['preserveCreatedAt']);

if (!in_array($action, ['save', 'delete'], true)) {
  respond(400, ['error' => 'Aksi tidak diizinkan']);
}

$allowedCollections = [
  'questions',
  'levels',
  'instruments',
  'achievements',
  'users',
  'leaderboards/global/entries',
];
if (!in_array($collection, $allowedCollections, true)) {
  respond(400, ['error' => 'Collection tidak diizinkan']);
}

if (!preg_match('/^[A-Za-z0-9_-]+$/', $documentId)) {
  respond(400, ['error' => 'Document ID tidak valid']);
}

if ($action === 'save' && !is_array($data)) {
  respond(400, ['error' => 'Data dokumen tidak valid']);
}

$serviceAccount = loadServiceAccount();
$projectId = $serviceAccount['project_id'] ?? 'swaranusa-quiz';
$accessToken = fetchAccessToken($serviceAccount);
$now = gmdate('Y-m-d\TH:i:s\Z');

$url = sprintf(
  'https://firestore.googleapis.com/v1/projects/%s/databases/(default)/documents/%s',
  rawurlencode($projectId),
  firestoreDocumentPath($collection, $documentId)
);

if ($action === 'delete') {
  $response = httpRequest($url, 'DELETE', [
    'Authorization: Bearer ' . $accessToken,
  ], '');

  if ($response['status'] !== 404 && ($response['status'] < 200 || $response['status'] >= 300)) {
    respond($response['status'], [
      'error' => 'Firestore delete gagal',
      'detail' => $response['body'],
    ]);
  }

  respond(200, [
    'ok' => true,
    'collection' => $collection,
    'documentId' => $documentId,
    'action' => 'delete',
  ]);
}

$data['updatedAt'] = $now;
if (!$preserveCreatedAt && !isset($data['createdAt'])) {
  $data['createdAt'] = $now;
}

$maskParams = array_map(
  fn($field) => 'updateMask.fieldPaths=' . rawurlencode($field),
  array_keys($data)
);
$url .= '?' . implode('&', $maskParams);

$response = httpRequest($url, 'PATCH', [
  'Authorization: Bearer ' . $accessToken,
  'Content-Type: application/json',
], json_encode(['fields' => firestoreFields($data)]));

if ($response['status'] < 200 || $response['status'] >= 300) {
  respond($response['status'], [
    'error' => 'Firestore write gagal',
    'detail' => $response['body'],
  ]);
}

respond(200, [
  'ok' => true,
  'collection' => $collection,
  'documentId' => $documentId,
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

function firestoreDocumentPath($collection, $documentId)
{
  $segments = array_filter(explode('/', $collection), fn($segment) => $segment !== '');
  $segments[] = $documentId;
  return implode('/', array_map('rawurlencode', $segments));
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

function firestoreFields(array $data)
{
  $fields = [];
  foreach ($data as $key => $value) {
    $fields[$key] = firestoreValue($value);
  }
  return $fields;
}

function firestoreValue($value)
{
  if (is_bool($value)) return ['booleanValue' => $value];
  if (is_int($value)) return ['integerValue' => (string) $value];
  if (is_float($value)) return ['doubleValue' => $value];
  if ($value === null) return ['nullValue' => null];

  if (is_array($value)) {
    if (array_is_list($value)) {
      return ['arrayValue' => ['values' => array_map('firestoreValue', $value)]];
    }

    return ['mapValue' => ['fields' => firestoreFields($value)]];
  }

  if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value)) {
    return ['timestampValue' => $value];
  }

  return ['stringValue' => (string) $value];
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
