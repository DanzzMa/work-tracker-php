<?php
if (session_status() === PHP_SESSION_NONE) {
  ini_set('session.cookie_httponly', 1);
  ini_set('session.use_strict_mode', 1);
  session_start();
}

function ensure_csrf() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function check_csrf() {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
      http_response_code(403);
      exit('CSRF token invalid.');
    }
  }
}

function require_login() {
  if (empty($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
  }
}

function require_admin() {
  require_login();
  if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: /dashboard.php');
    exit;
  }
}
?>
