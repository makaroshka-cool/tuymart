<?php
require __DIR__ . '/auth.php';
requirePermission('addNews');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date  = $_POST['date'] ?? '';
    $text  = trim($_POST['text'] ?? '');
    if ($title === '' || $date === '' || $text === '') {
        $error = 'Заполните все поля';
    } else {
        $news = loadNews();
        $news[] = ['id' => time(), 'title' => $title, 'date' => $date, 'text' => $text];
        saveNews($news);
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Добавить новость — Туймарт</title>
  <style>
    body { font-family: system-ui, sans-serif; background:#f4f6f5; padding:24px; }
    .card { background:#fff; max-width:600px; margin:0 auto; padding:24px; border-radius:8px; }
    input, textarea { width:100%; padding:10px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; }
    button { padding:12px 24px; background:#0d6b4d; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    .err { color:#d9534f; margin-bottom:10px; }
    a { color:#0d6b4d; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Добавить новость</h1>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <input type="text" name="title" placeholder="Заголовок" required>
      <input type="date" name="date" required>
      <textarea name="text" rows="5" placeholder="Текст новости" required></textarea>
      <button type="submit">Сохранить</button>
      <a href="dashboard.php" style="margin-left:12px">Отмена</a>
    </form>
  </div>
</body>
</html>