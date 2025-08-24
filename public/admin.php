<?php
require __DIR__ . '/../db.php';
require __DIR__ . '/actions/utils.php';
require_admin();
$csrf = ensure_csrf();

// Users list
$users = $mysqli->query('SELECT id, username, role, created_at FROM users ORDER BY username ASC');

// Rekap harian
$daily = $mysqli->query("SELECT u.username, SUM(w.duration_minutes) AS total FROM work_logs w JOIN users u ON u.id=w.user_id WHERE w.created_at=CURDATE() GROUP BY u.username ORDER BY u.username");

// Rekap mingguan (ISO week)
$weekly = $mysqli->query("SELECT u.username, SUM(w.duration_minutes) AS total FROM work_logs w JOIN users u ON u.id=w.user_id WHERE YEARWEEK(w.created_at, 1) = YEARWEEK(CURDATE(),1) GROUP BY u.username ORDER BY u.username");
?>
<!doctype html>
<html lang="id" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin · Work Tracker</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="min-h-screen p-6">
  <div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold">Panel Admin</h1>
        <p class="opacity-70 text-sm">Kelola pengguna & lihat rekap waktu.</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="/logout.php" class="btn btn-ghost">Logout</a>
        <button class="btn btn-ghost" data-theme-toggle>🌓</button>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="card bg-white/70 dark:bg-white/5 border">
        <h2 class="font-semibold mb-2">Tambah Pengguna</h2>
        <form class="space-y-2" method="POST" action="/actions/add_user.php">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          <input name="username" placeholder="Username" required class="w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10" />
          <input type="password" name="password" placeholder="Password" required class="w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10" />
          <select name="role" class="w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10">
            <option value="worker">Worker</option>
            <option value="admin">Admin</option>
          </select>
          <button class="btn btn-primary">Simpan</button>
        </form>
      </div>

      <div class="card bg-white/70 dark:bg-white/5 border">
        <h2 class="font-semibold mb-2">Export</h2>
        <form class="space-y-2" method="GET" action="/actions/export_csv.php">
          <div class="grid grid-cols-2 gap-2">
            <label class="block text-sm">Dari <input type="date" name="from" class="mt-1 w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10"></label>
            <label class="block text-sm">Sampai <input type="date" name="to" class="mt-1 w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10"></label>
          </div>
          <label class="block text-sm">User (optional ID)
            <input type="number" name="user_id" class="mt-1 w-full rounded-xl border px-3 py-2 bg-white/60 dark:bg-white/10">
          </label>
          <div class="flex gap-2">
            <button class="btn btn-ghost" name="scope" value="daily_all">CSV Harian (hari ini)</button>
            <button class="btn btn-ghost" name="scope" value="weekly_all">CSV Mingguan (minggu ini)</button>
            <button class="btn btn-primary" name="scope" value="range">CSV Rentang</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card bg-white/70 dark:bg-white/5 border">
      <h2 class="font-semibold mb-2">Pengguna</h2>
      <div class="overflow-x-auto">
        <table class="table text-sm">
          <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Bergabung</th></tr></thead>
          <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
              <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= htmlspecialchars($u['created_at']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="card bg-white/70 dark:bg-white/5 border">
        <h2 class="font-semibold mb-2">Rekap Harian (Hari ini)</h2>
        <div class="overflow-x-auto">
          <table class="table text-sm">
            <thead><tr><th>Username</th><th>Total</th></tr></thead>
            <tbody>
              <?php while($d = $daily->fetch_assoc()): ?>
                <?php $t=(int)$d['total']; ?>
                <tr><td><?= htmlspecialchars($d['username']) ?></td><td><?= floor($t/60) ?>j <?= $t%60 ?>m</td></tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card bg-white/70 dark:bg-white/5 border">
        <h2 class="font-semibold mb-2">Rekap Mingguan (Minggu ini)</h2>
        <div class="overflow-x-auto">
          <table class="table text-sm">
            <thead><tr><th>Username</th><th>Total</th></tr></thead>
            <tbody>
              <?php while($w = $weekly->fetch_assoc()): ?>
                <?php $t=(int)$w['total']; ?>
                <tr><td><?= htmlspecialchars($w['username']) ?></td><td><?= floor($t/60) ?>j <?= $t%60 ?>m</td></tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="/assets/app.js"></script>
</body>
</html>
