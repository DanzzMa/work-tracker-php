<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/utils.php';
check_csrf();

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');

$stmt = $mysqli->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if ($user && password_verify($password, $user['password'])) {
  session_regenerate_id(true);
  $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']];
  header('Location: ' . ($user['role'] === 'admin' ? '/admin.php' : '/dashboard.php'));
  exit;
}
header('Location: /index.php');
