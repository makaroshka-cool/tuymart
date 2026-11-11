<?php
require __DIR__ . '/auth.php';

if (currentUser()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $users = loadUsers();
    if (isset($users[$login]) && password_verify($pass, $users[$login]['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = $login;
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Неверный логин или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Вход — Туймарт</title>
  <style>
    body { font-family: system-ui, sans-serif; background:#f4f6f5; display:flex; min-height:100vh; align-items:center; justify-content:center; margin:0; }
    .card { background:#fff; padding:32px; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,.1); width:100%; max-width:380px; }
    h1 { font-size:20px; margin:0 0 20px; }
    input { width:100%; padding:10px; margin-bottom:12px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; }
    button { width:100%; padding:12px; background:#0d6b4d; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:16px; }
    .err { color:#d9534f; font-size:14px; margin-bottom:10px; }
  </style>
</head>
<body>
  <form class="card" method="post">
    <h1>Вход в админ-панель</h1>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <input type="text" name="login" placeholder="Логин" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
  </form>
</body>
</html>