<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/utils.php';
require_login();
check_csrf();

$user = $_SESSION['user'];
$stmt = $mysqli->prepare('SELECT id, start_time FROM work_logs WHERE user_id=? AND end_time IS NULL ORDER BY id DESC LIMIT 1');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { header('Location: /dashboard.php'); exit; }

$end = new DateTime('now');
$start = new DateTime($row['start_time']);
$mins = max(0, (int) round(($end->getTimestamp() - $start->getTimestamp())/60));

$stmt2 = $mysqli->prepare('UPDATE work_logs SET end_time=?, duration_minutes=? WHERE id=?');
$endStr = $end->format('Y-m-d H:i:s');
$stmt2->bind_param('sii', $endStr, $mins, $row['id']);
$stmt2->execute();

header('Location: /dashboard.php');
