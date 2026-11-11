<?php
require __DIR__ . '/customer-auth.php';
if (currentCustomer()) { header('Location: account.php'); exit; }

$next = $_GET['next'] ?? '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $next  = $_POST['next'] ?? '';
    $customers = loadCustomers();
    if (isset($customers[$email]) && password_verify($pass, $customers[$email]['password'])) {
        session_regenerate_id(true);
        $_SESSION['customer'] = $email;
        $target = $next && preg_match('/^[a-z0-9_\-.]+\.php$/i', $next) ? $next : 'account.php';
        header('Location: ' . $target);
        exit;
    }
    $error = 'Неверный e-mail или пароль';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Вход — Туймарт</title>
  <style>
    body { font-family:"Manrope",system-ui,sans-serif; background:#f6f8f7; display:flex; min-height:100vh; align-items:center; justify-content:center; margin:0; }
    .card { background:#fff; padding:32px; border-radius:14px; box-shadow:0 20px 40px rgba(13,107,77,.12); width:100%; max-width:380px; }
    h1 { font-size:22px; margin:0 0 18px; }
    input { width:100%; padding:11px; margin-bottom:12px; border:1px solid #ccc; border-radius:8px; box-sizing:border-box; font-family:inherit; }
    button { width:100%; padding:12px; background:#0d6b4d; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:16px; font-weight:700; }
    .err { color:#c0392b; font-size:14px; margin-bottom:12px; }
    a { color:#0d6b4d; }
    .muted { color:#6b7280; font-size:14px; text-align:center; margin-top:12px; }
  </style>
</head>
<body>
  <form class="card" method="post">
    <h1>Вход для покупателя</h1>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="next" value="<?= e($next) ?>">
    <input type="email" name="email" placeholder="E-mail" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
    <div class="muted">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></div>
  </form>
</body>
</html>