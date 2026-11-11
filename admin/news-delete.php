<?php
require __DIR__ . '/auth.php';
requirePermission('deleteNews');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $news = array_values(array_filter(loadNews(), fn($n) => (int)$n['id'] !== $id));
    saveNews($news);
}
header('Location: dashboard.php');
exit;