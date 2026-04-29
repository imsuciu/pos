<?php
function db(): PDO {
  static $pdo = null;
  global $config;
  if ($pdo === null) {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['db']['host'], $config['db']['port'], $config['db']['name'], $config['db']['charset']);
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }
  return $pdo;
}
