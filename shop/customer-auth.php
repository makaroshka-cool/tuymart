<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define('CUSTOMERS_FILE', __DIR__ . '/customers.json');
define('ORDERS_FILE',    __DIR__ . '/orders.json');

function loadCustomers(): array {
    if (!file_exists(CUSTOMERS_FILE)) return [];
    return json_decode(file_get_contents(CUSTOMERS_FILE), true) ?: [];
}
function saveCustomers(array $c): void {
    file_put_contents(CUSTOMERS_FILE, json_encode($c, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
function loadOrders(): array {
    if (!file_exists(ORDERS_FILE)) return [];
    return json_decode(file_get_contents(ORDERS_FILE), true) ?: [];
}
function saveOrders(array $o): void {
    file_put_contents(ORDERS_FILE, json_encode($o, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function currentCustomer(): ?array {
    if (empty($_SESSION['customer'])) return null;
    $customers = loadCustomers();
    $email = $_SESSION['customer'];
    if (!isset($customers[$email])) return null;
    return ['email' => $email] + $customers[$email];
}
function requireCustomer(): array {
    $c = currentCustomer();
    if (!$c) { header('Location: login-customer.php'); exit; }
    return $c;
}

function cart(): array {
    if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) return [];
    return $_SESSION['cart']; // [productId => qty]
}
function cartCount(): int {
    return array_sum(cart());
}
function cartTotal(array $products): int {
    $total = 0;
    $c = cart();
    foreach ($products as $p) {
        $id = (int)$p['id'];
        if (!empty($c[$id])) $total += $p['price'] * (int)$c[$id];
    }
    return $total;
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}