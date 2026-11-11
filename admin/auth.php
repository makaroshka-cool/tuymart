<?php
session_start();

define('USERS_FILE', __DIR__ . '/users.json');
define('NEWS_FILE',  __DIR__ . '/../news-data.json');

function loadUsers(): array {
    return json_decode(file_get_contents(USERS_FILE), true) ?: [];
}

function currentUser(): ?array {
    if (empty($_SESSION['user'])) return null;
    $users = loadUsers();
    $login = $_SESSION['user'];
    if (!isset($users[$login])) return null;
    return ['login' => $login] + $users[$login];
}

function requireAuth(): array {
    $u = currentUser();
    if (!$u) { header('Location: login.php'); exit; }
    return $u;
}

function requirePermission(string $perm): array {
    $u = requireAuth();
    $perms = [
        'admin'    => ['addNews' => true,  'deleteNews' => true,  'manageUsers' => true],
        'editor'   => ['addNews' => true,  'deleteNews' => true,  'manageUsers' => false],
        'operator' => ['addNews' => false, 'deleteNews' => false, 'manageUsers' => false],
    ];
    if (empty($perms[$u['role']][$perm])) {
        http_response_code(403);
        exit('Доступ запрещён: недостаточно прав.');
    }
    return $u;
}

function loadNews(): array {
    if (!file_exists(NEWS_FILE)) return [];
    return json_decode(file_get_contents(NEWS_FILE), true) ?: [];
}

function saveNews(array $news): void {
    file_put_contents(NEWS_FILE, json_encode($news, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}