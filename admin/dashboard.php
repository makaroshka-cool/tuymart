<?php
require __DIR__ . '/auth.php';
$user = requireAuth();
$perms = [
    'admin'    => ['addNews' => true,  'deleteNews' => true,  'manageUsers' => true],
    'editor'   => ['addNews' => true,  'deleteNews' => true,  'manageUsers' => false],
    'operator' => ['addNews' => false, 'deleteNews' => false, 'manageUsers' => false],
][$user['role']];

$news = loadNews();
usort($news, fn($a, $b) => strcmp($b['date'], $a['date']));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Админ-панель — Туймарт</title>
  <style>
    body { font-family: system-ui, sans-serif; background:#f4f6f5; margin:0; padding:24px; }
    .wrap { max-width:900px; margin:0 auto; }
    .top { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
    .badge { background:#0d6b4d; color:#fff; padding:2px 10px; border-radius:12px; font-size:13px; }
    .card { background:#fff; padding:24px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:20px; }
    .row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #eee; gap:12px; }
    .btn { padding:8px 16px; border:none; border-radius:4px; cursor:pointer; font-size:14px; }
    .btn-add { background:#0d6b4d; color:#fff; }
    .btn-del { background:#d9534f; color:#fff; }
    .btn-out { background:#777; color:#fff; text-decoration:none; display:inline-block; }
    input, textarea { width:100%; padding:8px; margin-bottom:8px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; font-family:inherit; }
    .muted { color:#777; font-size:13px; }
  </style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <h1>Панель управления</h1>
    <div>
      <span class="badge"><?= e($user['label']) ?></span>
      <a class="btn btn-out" href="logout.php">Выйти</a>
    </div>
  </div>

  <div class="card">
    <div class="row" style="border:none">
      <h2 style="font-size:18px;margin:0">Новости</h2>
      <?php if ($perms['addNews']): ?>
        <a class="btn btn-add" href="news-add.php">+ Добавить новость</a>
      <?php endif; ?>
    </div>

    <?php if (!$news): ?>
      <p class="muted">Новостей нет.</p>
    <?php else: foreach ($news as $n): ?>
      <div class="row">
        <div>
          <strong><?= e($n['title']) ?></strong><br>
          <span class="muted"><?= e($n['date']) ?></span>
        </div>
        <?php if ($perms['deleteNews']): ?>
          <form method="post" action="news-delete.php" onsubmit="return confirm('Удалить новость?')">
            <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
            <button class="btn btn-del" type="submit">Удалить</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; endif; ?>
  </div>

  <?php if ($perms['manageUsers']): ?>
  <div class="card">
    <h2 style="font-size:18px;margin:0 0 10px">Пользователи (только администратор)</h2>
    <ul>
      <li>admin — администратор</li>
      <li>editor — редактор</li>
      <li>operator — оператор</li>
    </ul>
  </div>
  <?php endif; ?>
</div>
</body>
</html>