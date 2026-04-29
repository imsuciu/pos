<?php
session_start();
$configPath = __DIR__ . '/../config/config.php';
if (!file_exists($configPath)) {
  $configPath = __DIR__ . '/../config/config.example.php';
}
$config = require $configPath;
date_default_timezone_set($config['app']['timezone'] ?? 'Europe/Bucharest');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';
