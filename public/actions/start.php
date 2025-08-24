<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/utils.php';
require_login();
check_csrf();

$user = $_SESSION['user'];
// Pastikan tidak ada sesi aktif
$stmt = $mysqli->prepare('SELECT id FROM work_logs WHERE user_id=? AND end_time IS NULL');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
  header('Location: /dashboard.php'); exit;
}

$now = (new DateTime('now'))->format('Y-m-d H:i:s');
$today = (new DateTime('now'))->format('Y-m-d');

$stmt2 = $mysqli->prepare('INSERT INTO work_logs (user_id, start_time, created_at) VALUES (?,?,?)');
$stmt2->bind_param('iss', $user['id'], $now, $today);
$stmt2->execute();

header('Location: /dashboard.php');
