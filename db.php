<?php
$config = require __DIR__ . '/config.php';
date_default_timezone_set($config['timezone'] ?? 'Asia/Jakarta');

$mysqli = new mysqli(
  $config['db_host'],
  $config['db_user'],
  $config['db_pass'],
  $config['db_name']
);
if ($mysqli->connect_error) {
  die('Koneksi DB gagal: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
?>
