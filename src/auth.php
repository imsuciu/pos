<?php
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) { header('Location: /index.php'); exit; } }
function require_role(string $role): void { require_login(); if (current_user()['role'] !== $role) { http_response_code(403); exit('Forbidden'); } }
function login(string $username, string $password): bool {
  $st = db()->prepare('SELECT * FROM users WHERE username=? AND is_active=1 LIMIT 1');
  $st->execute([$username]); $u = $st->fetch(PDO::FETCH_ASSOC);
  if ($u && password_verify($password, $u['password_hash'])) { $_SESSION['user'] = $u; return true; }
  return false;
}
function logout(): void { unset($_SESSION['user']); session_destroy(); }
