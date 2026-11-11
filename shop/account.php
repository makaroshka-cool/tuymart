<?php
require __DIR__ . '/customer-auth.php';
$c = requireCustomer();
$orders = array_values(array_filter(loadOrders(), fn($o) => ($o['email'] ?? '') === $c['email']));
usort($orders, fn($a, $b) => strcmp($b['created'] ?? '', $a['created'] ?? ''));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Личный кабинет — Туймарт</title>
  <style>
    body { font-family:"Manrope",system-ui,sans-serif; background:#f6f8f7; margin:0; padding:24px; }
    .wrap { max-width:900px; margin:0 auto; }
    .card { background:#fff; border-radius:14px; padding:24px; box-shadow:0 10px 30px rgba(13,107,77,.08); margin-bottom:20px; }
    h1 { margin:0 0 12px; font-size:24px; }
    h2 { font-size:18px; margin:0 0 12px; }
    .muted { color:#6b7280; }
    .order { border:1px solid #eef1f0; border-radius:10px; padding:14px; margin-bottom:12px; }
    .order__head { display:flex; justify-content:space-between; margin-bottom:8px; font-weight:700; }
    .order ul { margin:0; padding-left:18px; color:#374151; }
    .btn { display:inline-block; padding:10px 16px; border-radius:10px; text-decoration:none; font-weight:700; }
    .btn-primary { background:#0d6b4d; color:#fff; }
    .btn-secondary { background:#e6f4ee; color:#0d6b4d; }
    .row { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center; }
  </style>
</head>
<body>
<div class="wrap">
  <p><a href="../index.html">← На главную</a></p>

  <div class="card">
    <div class="row">
      <div>
        <h1>Здравствуйте, <?= e($c['name']) ?>!</h1>
        <p class="muted"><?= e($c['email']) ?><?php if (!empty($c['phone'])): ?> · <?= e($c['phone']) ?><?php endif; ?></p>
      </div>
      <div>
        <a class="btn btn-secondary" href="cart-page.php">Корзина</a>
        <a class="btn btn-primary" href="logout-customer.php">Выйти</a>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Мои заказы</h2>
    <?php if (!$orders): ?>
      <p class="muted">Заказов пока нет. <a href="../index.html">Перейти к товарам</a></p>
    <?php else: foreach ($orders as $o): ?>
      <div class="order">
        <div class="order__head">
          <span>Заказ №<?= (int)$o['id'] ?></span>
          <span><?= (int)$o['total'] ?> ₽</span>
        </div>
        <div class="muted" style="font-size:13px;margin-bottom:6px">
          <?= e($o['created']) ?> · <?= e($o['status'] ?? 'новый') ?>
        </div>
        <ul>
          <?php foreach ($o['items'] as $it): ?>
            <li><?= e($it['title']) ?> × <?= (int)$it['qty'] ?> = <?= (int)$it['sum'] ?> ₽</li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; endif; ?>
  </div>
</div>
</body>
</html>