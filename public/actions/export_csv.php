<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/utils.php';
require_admin();

$scope = $_GET['scope'] ?? 'range';
$userId = isset($_GET['user_id']) && $_GET['user_id'] !== '' ? (int)$_GET['user_id'] : null;

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="worktracker_' . $scope . '_' . date('Ymd_His') . '.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['username','start_time','end_time','duration_minutes']);

if ($scope === 'daily_all') {
  $sql = "SELECT u.username, w.start_time, w.end_time, w.duration_minutes
          FROM work_logs w JOIN users u ON u.id=w.user_id
          WHERE w.created_at = CURDATE()
          ORDER BY u.username, w.start_time";
  $res = $mysqli->query($sql);
} elseif ($scope === 'weekly_all') {
  $sql = "SELECT u.username, w.start_time, w.end_time, w.duration_minutes
          FROM work_logs w JOIN users u ON u.id=w.user_id
          WHERE YEARWEEK(w.created_at,1) = YEARWEEK(CURDATE(),1)
          ORDER BY u.username, w.start_time";
  $res = $mysqli->query($sql);
} else {
  $from = $_GET['from'] ?? null;
  $to = $_GET['to'] ?? null;
  if (!$from || !$to) {
    $from = date('Y-m-d', strtotime('-6 days'));
    $to = date('Y-m-d');
  }
  if ($userId) {
    $stmt = $mysqli->prepare("SELECT u.username, w.start_time, w.end_time, w.duration_minutes
                              FROM work_logs w JOIN users u ON u.id=w.user_id
                              WHERE w.created_at BETWEEN ? AND ? AND u.id=?
                              ORDER BY u.username, w.start_time");
    $stmt->bind_param('ssi', $from, $to, $userId);
  } else {
    $stmt = $mysqli->prepare("SELECT u.username, w.start_time, w.end_time, w.duration_minutes
                              FROM work_logs w JOIN users u ON u.id=w.user_id
                              WHERE w.created_at BETWEEN ? AND ?
                              ORDER BY u.username, w.start_time");
    $stmt->bind_param('ss', $from, $to);
  }
  $stmt->execute();
  $res = $stmt->get_result();
}

while ($row = $res->fetch_assoc()) {
  fputcsv($out, [$row['username'], $row['start_time'], $row['end_time'], $row['duration_minutes']]);
}
fclose($out);
exit;
