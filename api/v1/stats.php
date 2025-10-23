<?php
// api/v1/stats.php (GET) — unique & total visits per page within date range
require __DIR__ . '/../helpers.php';
allow_cors();
require __DIR__ . '/../db.php';

$from = isset($_GET['from']) ? $_GET['from'] : null;
$to   = isset($_GET['to']) ? $_GET['to'] : null;

$timezone = new DateTimeZone('Pacific/Auckland');

if (!$from || !$to) {
  $now = new DateTime('now', $timezone);
  $to = $now->format('Y-m-d');
  $from = $now->modify('-7 day')->format('Y-m-d');
}

try {
  $from_dt = new DateTime($from . ' 00:00:00', $timezone);
  $to_dt   = new DateTime($to . ' 23:59:59', $timezone);
} catch (Throwable $e) {
  http_response_code(400);
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'Invalid date']);
  exit;
}

$sql = "
  SELECT
    page_url,
    COUNT(*) AS total_visits,
    COUNT(DISTINCT visitor_id) AS unique_visitors
  FROM visits
  WHERE visit_time BETWEEN :from AND :to
  GROUP BY page_url
  ORDER BY unique_visitors DESC, total_visits DESC;
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':from' => $from_dt->format('Y-m-d H:i:s'),
  ':to'   => $to_dt->format('Y-m-d H:i:s'),
]);

$rows = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode([
  'from' => $from,
  'to' => $to,
  'rows' => $rows,
]);
