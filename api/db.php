<?php
// api/db.php
$config = require __DIR__ . '/config.php';
try {
  $pdo = new PDO($config['dsn'], $config['user'], $config['pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'DB connection failed', 'details' => $e->getMessage()]);
  exit;
}
