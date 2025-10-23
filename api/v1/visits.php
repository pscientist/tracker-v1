<?php
// api/v1/visits.php (POST) — create a visit event
require __DIR__ . '/../helpers.php';
allow_cors();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  header('Allow: POST, OPTIONS');
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

require __DIR__ . '/../db.php';

$payload = json_input();

$page_url = isset($payload['page_url']) ? substr(trim($payload['page_url']), 0, 1024) : '';
$visitor_id = isset($payload['visitor_id']) ? substr(trim($payload['visitor_id']), 0, 36) : '';
$referer = isset($payload['referer']) ? substr(trim($payload['referer']), 0, 1024) : '';
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 512) : '';
$ip = get_client_ip();

if (!$page_url || !$visitor_id) {
  http_response_code(400);
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'Missing page_url or visitor_id']);
  exit;
}

$stmt = $pdo->prepare('INSERT INTO visits (page_url, visitor_id, user_agent, ip_address, referer) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$page_url, $visitor_id, $user_agent, $ip, $referer]);

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
