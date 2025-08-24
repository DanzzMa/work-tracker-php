<?php
require __DIR__ . '/../../db.php';
require __DIR__ . '/utils.php';
require_admin();
check_csrf();

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$role = ($_POST['role'] ?? 'worker') === 'admin' ? 'admin' : 'worker';

if ($username && $password) {
  $hash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $mysqli->prepare('INSERT INTO users (username, password, role) VALUES (?,?,?)');
  $stmt->bind_param('sss', $username, $hash, $role);
  @$stmt->execute();
}
header('Location: /admin.php');
