<?php
require __DIR__ . '/customer-auth.php';

header('Content-Type: application/json; charset=utf-8');

$products = json_decode(file_get_contents(__DIR__ . '/../products.json'), true) ?: [];
$byId = [];
foreach ($products as $p) $byId[(int)$p['id']] = $p;

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$id     = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$qty    = (int)($_POST['qty'] ?? 1);

if ($action === 'add') {
    if (isset($byId[$id])) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + max(1, $qty);
    }
} elseif ($action === 'update') {
    if (isset($byId[$id])) {
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = $qty;
    }
} elseif ($action === 'remove') {
    unset($_SESSION['cart'][$id]);
} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
}

$items = [];
foreach (cart() as $pid => $q) {
    if (!isset($byId[$pid])) continue;
    $p = $byId[$pid];
    $items[] = [
        'id' => $pid,
        'title' => $p['title'],
        'price' => (int)$p['price'],
        'qty' => (int)$q,
        'sum' => (int)$p['price'] * (int)$q,
        'image' => $p['image'] ?? '',
    ];
}

echo json_encode([
    'ok' => true,
    'count' => cartCount(),
    'total' => cartTotal($products),
    'items' => $items,
], JSON_UNESCAPED_UNICODE);