<?php
require __DIR__ . '/customer-auth.php';
$c = requireCustomer();

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
    $items[] = ['id' => $pid, 'title' => $p['title'], 'price' => $p['price'], 'qty' => $q, 'sum' => $sum];
}

$error = '';
$done = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    if (!$items) {
        $error = 'Корзина пуста.';
    } elseif ($address === '') {
        $error = 'Укажите адрес доставки.';
    } else {
        $orders = loadOrders();
        $orders[] = [
            'id' => time(),
            'email' => $c['email'],
            'name' => $c['name'],
            'phone' => $c['phone'] ?? '',
            'address' => $address,
            'comment' => $comment,
            'items' => $items,
            'total' => $total,
            'status' => 'новый',
            'created' => date('Y-m-d H:i:s'),
        ];
        saveOrders($orders);
        $_SESSION['cart'] = [];
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <title>Оформление заказа — Туймарт</title>
  <style>
    body { font-family:"Manrope",system-ui,sans-serif; background:#f6f8f7; margin:0; padding:24px; }
    .wrap { max-width:760px; margin:0 auto; }
    .card { background:#fff; border-radius:14px; padding:24px; box-shadow:0 10px 30px rgba(13,107,77,.08); }
    h1 { margin:0 0 16px; font-size:24px; }
    input, textarea { width:100%; padding:11px; margin-bottom:12px; border:1px solid #ccc; border-radius:8px; box-sizing:border-box; font-family:inherit; }
    button { padding:12px 20px; background:#0d6b4d; color:#fff; border:none; border-radius:10px; cursor:pointer; font-weight:700; font-size:16px; }
    .err { color:#c0392b; margin-bottom:12px; }
    .ok { color:#0d6b4d; font-weight:700; margin-bottom:12px; }
    .row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px dashed #eef1f0; }
    .total { font-size:20px; font-weight:800; text-align:right; margin:16px 0; }
    a { color:#0d6b4d; }
  </style>
</head>
<body>
<div class="wrap">
  <p><a href="cart-page.php">← В корзину</a></p>
  <div class="card">
    <h1>Оформление заказа</h1>
    <?php if ($done): ?>
      <p class="ok">Спасибо! Заказ оформлен. Мы свяжемся с вами.</p>
      <a href="account.php">Перейти в личный кабинет</a>
    <?php else: ?>
      <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
      <?php foreach ($items as $it): ?>
        <div class="row"><span><?= e($it['title']) ?> × <?= (int)$it['qty'] ?></span><span><?= (int)$it['sum'] ?> ₽</span></div>
      <?php endforeach; ?>
      <div class="total">Итого: <?= (int)$total ?> ₽</div>
      <form method="post">
        <input type="text" name="address" placeholder="Адрес доставки" required>
        <textarea name="comment" rows="3" placeholder="Комментарий к заказу (необязательно)"></textarea>
        <button type="submit">Подтвердить заказ</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>