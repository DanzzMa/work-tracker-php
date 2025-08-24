<?php
require __DIR__ . '/../db.php';
require __DIR__ . '/actions/utils.php';
require_login();
$csrf = ensure_csrf();
$user = $_SESSION['user'];
// cek ongoing session
$stmt = $mysqli->prepare('SELECT id, start_time FROM work_logs WHERE user_id=? AND end_time IS NULL ORDER BY id DESC LIMIT 1');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$ongoing = $stmt->get_result()->fetch_assoc();

// Total hari ini
$stmt2 = $mysqli->prepare('SELECT COALESCE(SUM(duration_minutes),0) AS total FROM work_logs WHERE user_id=? AND created_at=CURDATE()');
$stmt2->bind_param('i', $user['id']);
$stmt2->execute();
$today_total = (int)$stmt2->get_result()->fetch_assoc()['total'];

// Riwayat singkat (7 terbaru)
$stmt3 = $mysqli->prepare('SELECT start_time, end_time, duration_minutes FROM work_logs WHERE user_id=? ORDER BY id DESC LIMIT 7');
$stmt3->bind_param('i', $user['id']);
$stmt3->execute();
$recent = $stmt3->get_result();
?>
<!doctype html>
<html lang="id" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard · <?= htmlspecialchars($user['username']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body class="min-h-screen p-6">
  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold">Halo, <?= htmlspecialchars($user['username']) ?></h1>
        <p class="opacity-70 text-sm">Status jam kerja Anda hari ini.</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="/logout.php" class="btn btn-ghost">Logout</a>
        <button class="btn btn-ghost" data-theme-toggle>🌓</button>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <div class="card bg-white/70 dark:bg-white/5 border">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">Timer</h2>
          <?php if ($ongoing): ?>
            <span class="badge badge-live">Sedang bekerja</span>
          <?php else: ?>
            <span class="badge">Tidak aktif</span>
          <?php endif; ?>
        </div>
        <div class="text-5xl font-mono my-4" id="live-timer" data-start="<?= $ongoing ? htmlspecialchars($ongoing['start_time']) : '' ?>">
          <?php
          if ($ongoing) {
            $start = new DateTime($ongoing['start_time']);
            $now = new DateTime('now');
            $mins = floor(($now->getTimestamp() - $start->getTimestamp())/60);
            $h = str_pad((string)floor($mins/60),2,'0',STR_PAD_LEFT);
            $m = str_pad((string)($mins%60),2,'0',STR_PAD_LEFT);
            echo $h . ':' . $m;
          } else {
            echo '00:00';
          }
          ?>
        </div>
        <div class="flex gap-2">
          <?php if (!$ongoing): ?>
            <form method="POST" action="/actions/start.php">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
              <button class="btn btn-primary">Masuk</button>
            </form>
          <?php else: ?>
            <form method="POST" action="/actions/stop.php" onsubmit="return confirm('Akhiri sesi kerja sekarang?');">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
              <button class="btn btn-danger">Berhenti</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

      <div class="card bg-white/70 dark:bg-white/5 border">
        <h2 class="font-semibold">Total Hari Ini</h2>
        <?php $jam=floor($today_total/60); $menit=$today_total%60; ?>
        <div class="text-4xl font-bold my-4"><?= $jam ?>j <?= $menit ?>m</div>
        <form method="GET" action="/actions/export_csv.php">
          <input type="hidden" name="scope" value="daily_self">
          <button class="btn btn-ghost">Export CSV (Hari ini)</button>
        </form>
      </div>
    </div>

    <div class="card bg-white/70 dark:bg-white/5 border">
      <h2 class="font-semibold mb-2">Riwayat Terakhir</h2>
      <div class="overflow-x-auto">
        <table class="table text-sm">
          <thead><tr><th>Mulai</th><th>Selesai</th><th>Durasi</th></tr></thead>
          <tbody>
            <?php while($r = $recent->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($r['start_time']) ?></td>
                <td><?= htmlspecialchars($r['end_time'] ?? '-') ?></td>
                <td><?= floor($r['duration_minutes']/60) ?>j <?= $r['duration_minutes']%60 ?>m</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <script src="/assets/app.js"></script>
</body>
</html>
