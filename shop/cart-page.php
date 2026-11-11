<?php
require __DIR__ . '/customer-auth.php';
$products = json_decode(file_get_contents(__DIR__ . '/../products.json'), true) ?: [];
$byId = [];
foreach ($products as $p) $byId[(int)$p['id']] = $p;

$items = [];
$total = 0;
foreach (cart() as $pid => $q) {
    if (!isset($byId[$pid])) continue;
    $p = $byId[$pid];
    $sum = $p['price'] * $q;
    $total += $sum;
    $items[] = ['p' => $p, 'qty' => $q, 'sum' => $sum];
}
$customer = currentCustomer();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Корзина — Туймарт</title>
  <style>
    body { font-family: "Manrope", system-ui, sans-serif; background:#f6f8f7; margin:0; padding:24px; }
    .wrap { max-width:900px; margin:0 auto; }
    .card { background:#fff; border-radius:14px; padding:24px; box-shadow:0 10px 30px rgba(13,107,77,.08); margin-bottom:20px; }
    .row { display:flex; gap:16px; align-items:center; padding:14px 0; border-bottom:1px solid #eef1f0; }
    .row img { width:70px; height:70px; object-fit:cover; border-radius:10px; }
    .row .title { flex:1; font-weight:600; }
    .row .price { color:#6b7280; font-size:14px; }
    .qty input { width:56px; padding:6px; text-align:center; border:1px solid #ccc; border-radius:6px; }
    .btn { padding:10px 16px; border:none; border-radius:10px; cursor:pointer; font-weight:700; }
    .btn-primary { background:#0d6b4d; color:#fff; }
    .btn-secondary { background:#e6f4ee; color:#0d6b4d; }
    .btn-danger { background:#fdecea; color:#c0392b; }
    .total { font-size:20px; font-weight:800; text-align:right; margin-top:16px; }
    .actions { display:flex; justify-content:space-between; margin-top:20px; gap:12px; flex-wrap:wrap; }
    a { color:#0d6b4d; }
    .empty { color:#6b7280; text-align:center; padding:20px; }
  </style>
</head>
<body>
<div class="wrap">
  <p><a href="../index.html">← На главную</a></p>
  <div class="card">
    <h1 style="margin:0 0 16px">Корзина</h1>
    <?php if (!$items): ?>
      <p class="empty">Корзина пуста. <a href="../index.html">Перейти к товарам</a></p>
    <?php else: ?>
      <?php foreach ($items as $it): ?>
        <div class="row">
          <img src="<?= e($it['p']['image']) ?>" alt="">
          <div class="title">
            <?= e($it['p']['title']) ?>
            <div class="price"><?= (int)$it['p']['price'] ?> ₽ / <?= e($it['p']['unit']) ?></div>
          </div>
          <form class="qty" method="post" action="cart.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$it['p']['id'] ?>">
            <input type="number" name="qty" value="<?= (int)$it['qty'] ?>" min="1" onchange="this.form.submit()">
          </form>
          <div style="width:100px;text-align:right;font-weight:700"><?= (int)$it['sum'] ?> ₽</div>
          <form method="post" action="cart.php">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="id" value="<?= (int)$it['p']['id'] ?>">
            <button class="btn btn-danger" type="submit">✕</button>
          </form>
        </div>
      <?php endforeach; ?>
      <div class="total">Итого: <?= (int)$total ?> ₽</div>
      <div class="actions">
        <form method="post" action="cart.php">
          <input type="hidden" name="action" value="clear">
          <button class="btn btn-secondary" type="submit">Очистить корзину</button>
        </form>
        <?php if ($customer): ?>
          <a class="btn btn-primary" href="checkout.php" style="text-decoration:none">Оформить заказ</a>
        <?php else: ?>
          <a class="btn btn-primary" href="login-customer.php?next=checkout.php" style="text-decoration:none">Войти и оформить</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>