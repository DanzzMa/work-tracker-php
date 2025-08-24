<?php
require __DIR__ . '/../db.php';
require __DIR__ . '/actions/utils.php';
session_destroy();
header('Location: /index.php');
