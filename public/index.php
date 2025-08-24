<?php
require __DIR__ . '/../db.php';
require __DIR__ . '/actions/utils.php';
$csrf = ensure_csrf();
if (!empty($_SESSION['user'])) {
  header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? '/admin.php' : '/dashboard.php'));
  exit;
}
?>
<!doctype html>
<html lang="id" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login · Work Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="min-h-screen flex items-center justify-center p-6">
  <div class="card max-w-md w-full bg-white/70 dark:bg-white/5 backdrop-blur border border-white/20">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Work Tracker</h1>
      <button class="btn btn-ghost" data-theme-toggle>🌓</button>
    </div>
    <form method="POST" action="/actions/login.php" class="space-y-3">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
      <label class="block">
        <span class="text-sm">Username</span>
        <input name="username" required class="mt-1 w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10" autocomplete="username" />
      </label>
      <label class="block">
        <span class="text-sm">Password</span>
        <input type="password" name="password" required class="mt-1 w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10" autocomplete="current-password" />
      </label>
      <button class="btn btn-primary w-full">Masuk</button>
    </form>
    <p class="text-xs opacity-70 mt-3">Admin default: admin / admin123</p>
  </div>
  <script src="/assets/app.js"></script>
</body>
</html>
